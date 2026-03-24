<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Review;
use App\Models\User;
use App\Services\MarketplaceAnalyticsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function __construct(private readonly MarketplaceAnalyticsService $analytics)
    {
    }

    public function dashboard(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasRole('admin')) {
            $data = $this->analytics->getAdminDashboardData();
            $sellers = User::role('vendedor')->orderBy('name')->get(['id', 'name']);

            return view('dashboard.admin', [
                'user' => $user,
                'metrics' => $data['metrics'],
                'initialCharts' => $data['charts'],
                'sellers' => $sellers,
            ]);
        }
        
        if ($user->hasRole('vendedor')) {
            $data = $this->analytics->getSellerDashboardData((int) $user->id);

            return view('dashboard.seller', [
                'user' => $user,
                'metrics' => $data['metrics'],
                'recentOrders' => $data['recent_orders'],
                'productRanking' => $data['product_ranking'],
                'notifications' => $data['notifications'],
                'initialCharts' => $data['charts'],
            ]);
        }
        
        if ($user->hasRole('cliente')) {
            $estadisticas = [
                'compras_realizadas' => Pedido::where('user_id', $user->id)->count(),
                'comentarios_dejados' => Review::where('user_id', $user->id)->count(),
                'total_gastado' => Pedido::where('user_id', $user->id)->sum('total'),
                'pedidos_recientes' => Pedido::where('user_id', $user->id)->orderBy('created_at', 'desc')->limit(5)->get(),
            ];

            return view('dashboard', compact('user', 'estadisticas'));
        }

        return view('dashboard', compact('user'));
    }

    public function adminBusinessIntelligence()
    {
        $user = auth()->user();
        abort_unless($user && $user->hasRole('admin'), 403, 'No autorizado.');

        $data = $this->analytics->getAdminBusinessIntelligenceData();
        $sellers = User::role('vendedor')->orderBy('name')->get(['id', 'name']);

        return view('dashboard.admin_bi', [
            'user' => $user,
            'metrics' => $data['metrics'],
            'insights' => $data['insights'],
            'alerts' => $data['alerts'],
            'initialCharts' => $data['charts'],
            'sellers' => $sellers,
        ]);
    }

    public function sellerData(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        abort_unless($user && $user->hasRole('vendedor'), 403, 'No autorizado.');

        return response()->json($this->analytics->getSellerDashboardData((int) $user->id));
    }

    public function adminData(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        abort_unless($user && $user->hasRole('admin'), 403, 'No autorizado.');

        return response()->json($this->analytics->getAdminDashboardData());
    }

    public function adminBusinessIntelligenceData(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        abort_unless($user && $user->hasRole('admin'), 403, 'No autorizado.');

        return response()->json($this->analytics->getAdminBusinessIntelligenceData());
    }

    public function exportReport(Request $request)
    {
        $user = auth()->user();
        abort_unless($user, 403, 'No autorizado.');

        $validated = $request->validate([
            'type' => 'required|in:ventas_vendedor,global,pedidos,solicitudes',
            'format' => 'required|in:pdf,excel',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'seller_id' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|string|in:pendiente,enviado,entregado,cancelado,anulado',
        ]);

        $restrictedSellerId = $user->hasRole('vendedor') ? (int) $user->id : null;
        $report = $this->analytics->getReportData($validated['type'], $validated, $restrictedSellerId);

        if (($validated['format'] ?? 'pdf') === 'pdf') {
            $pdf = Pdf::loadView('dashboard.report_pdf', [
                'title' => $report['title'],
                'headers' => $report['headers'],
                'rows' => $report['rows'],
                'filters' => $validated,
            ])->setPaper('a4', 'landscape');

            return $pdf->download('reporte_' . $validated['type'] . '_' . now()->format('Ymd_His') . '.pdf');
        }

        return $this->downloadCsvExcelCompatible($report['title'], $report['headers'], $report['rows']);
    }

    private function downloadCsvExcelCompatible(string $title, array $headers, $rows): StreamedResponse
    {
        $filename = 'reporte_' . str()->slug($title) . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($headers, $rows) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers, ';');

            foreach ($rows as $row) {
                fputcsv($output, (array) $row, ';');
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
