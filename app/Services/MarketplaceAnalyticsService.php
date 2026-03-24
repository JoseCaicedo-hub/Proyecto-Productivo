<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Solicitud;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MarketplaceAnalyticsService
{
    public function getSellerDashboardData(int $sellerId): array
    {
        $monthWindow = $this->buildMonthWindow(12);

        $base = PedidoDetalle::query()
            ->join('pedidos', 'pedido_detalles.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'pedido_detalles.producto_id', '=', 'productos.id')
            ->where('productos.user_id', $sellerId);

        $completedOrders = (clone $base)
            ->where(function ($query) {
                $query->where('pedidos.estado', 'entregado')
                    ->orWhere('pedido_detalles.envio_estado', 'entregado');
            })
            ->distinct('pedidos.id')
            ->count('pedidos.id');

        $validSales = (clone $base)
            ->whereNotIn('pedidos.estado', ['cancelado', 'anulado']);

        $totalRevenue = (int) round((float) (clone $validSales)->sum(DB::raw('pedido_detalles.cantidad * pedido_detalles.precio')));
        $totalUnitsSold = (int) (clone $validSales)->sum('pedido_detalles.cantidad');
        $averageTicket = $completedOrders > 0 ? (int) round($totalRevenue / $completedOrders) : 0;

        $currentMonthRevenue = (int) round((float) (clone $validSales)
            ->whereBetween('pedidos.created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum(DB::raw('pedido_detalles.cantidad * pedido_detalles.precio')));

        $previousMonthRevenue = (int) round((float) (clone $validSales)
            ->whereBetween('pedidos.created_at', [now()->copy()->subMonth()->startOfMonth(), now()->copy()->subMonth()->endOfMonth()])
            ->sum(DB::raw('pedido_detalles.cantidad * pedido_detalles.precio')));

        $growth = $this->calculateGrowth($currentMonthRevenue, $previousMonthRevenue);

        $salesByMonthRaw = (clone $validSales)
            ->selectRaw("DATE_FORMAT(pedidos.created_at, '%Y-%m') as ym")
            ->selectRaw('COUNT(DISTINCT pedidos.id) as total')
            ->whereBetween('pedidos.created_at', [$monthWindow['startDate'], $monthWindow['endDate']])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $incomeByMonthRaw = (clone $validSales)
            ->selectRaw("DATE_FORMAT(pedidos.created_at, '%Y-%m') as ym")
            ->selectRaw('SUM(pedido_detalles.cantidad * pedido_detalles.precio) as total')
            ->whereBetween('pedidos.created_at', [$monthWindow['startDate'], $monthWindow['endDate']])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $topProducts = (clone $validSales)
            ->select('productos.id', 'productos.nombre')
            ->selectRaw('SUM(pedido_detalles.cantidad) as unidades')
            ->selectRaw('SUM(pedido_detalles.cantidad * pedido_detalles.precio) as ingresos')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('unidades')
            ->limit(5)
            ->get();

        $ordersByStatus = (clone $base)
            ->select('pedidos.estado')
            ->selectRaw('COUNT(DISTINCT pedidos.id) as total')
            ->groupBy('pedidos.estado')
            ->orderByDesc('total')
            ->get();

        $recentOrders = (clone $base)
            ->leftJoin('users as compradores', 'pedidos.user_id', '=', 'compradores.id')
            ->select('pedidos.id', 'pedidos.estado', 'pedidos.created_at', 'compradores.name as comprador')
            ->selectRaw('SUM(pedido_detalles.cantidad * pedido_detalles.precio) as subtotal_vendedor')
            ->groupBy('pedidos.id', 'pedidos.estado', 'pedidos.created_at', 'compradores.name')
            ->orderByDesc('pedidos.created_at')
            ->limit(8)
            ->get();

        $newOrdersCount = (clone $base)
            ->where('pedidos.estado', 'pendiente')
            ->where('pedidos.created_at', '>=', now()->subHours(48))
            ->distinct('pedidos.id')
            ->count('pedidos.id');

        $completedRecentlyCount = (clone $base)
            ->where(function ($query) {
                $query->where('pedidos.estado', 'entregado')
                    ->orWhere('pedido_detalles.envio_estado', 'entregado');
            })
            ->where('pedidos.updated_at', '>=', now()->subDays(7))
            ->distinct('pedidos.id')
            ->count('pedidos.id');

        $notifications = collect();
        if ($newOrdersCount > 0) {
            $notifications->push([
                'type' => 'primary',
                'text' => "Tienes {$newOrdersCount} pedido(s) nuevos pendientes en las últimas 48 horas.",
            ]);
        }
        if ($completedRecentlyCount > 0) {
            $notifications->push([
                'type' => 'success',
                'text' => "{$completedRecentlyCount} pedido(s) fueron completados en los últimos 7 días.",
            ]);
        }
        if ($notifications->isEmpty()) {
            $notifications->push([
                'type' => 'info',
                'text' => 'Sin alertas recientes. Tu operación está al día.',
            ]);
        }

        return [
            'metrics' => [
                'completed_orders' => $completedOrders,
                'total_revenue' => $totalRevenue,
                'products_sold' => $totalUnitsSold,
                'average_ticket' => $averageTicket,
                'growth' => $growth,
            ],
            'charts' => [
                'sales_by_month' => [
                    'labels' => $monthWindow['labels'],
                    'data' => $this->mapMonthlyData($monthWindow['keys'], $salesByMonthRaw),
                ],
                'income_by_month' => [
                    'labels' => $monthWindow['labels'],
                    'data' => $this->mapMonthlyData($monthWindow['keys'], $incomeByMonthRaw),
                ],
                'top_products' => [
                    'labels' => $topProducts->pluck('nombre')->values()->all(),
                    'data' => $topProducts->pluck('unidades')->map(fn ($value) => (int) $value)->values()->all(),
                ],
                'orders_by_status' => [
                    'labels' => $ordersByStatus->pluck('estado')->map(fn ($status) => ucfirst((string) $status))->values()->all(),
                    'data' => $ordersByStatus->pluck('total')->map(fn ($value) => (int) $value)->values()->all(),
                ],
            ],
            'recent_orders' => $recentOrders,
            'product_ranking' => $topProducts,
            'notifications' => $notifications,
        ];
    }

    public function getAdminDashboardData(): array
    {
        $monthWindow = $this->buildMonthWindow(12);

        $totalOrders = Pedido::count();
        $globalSales = (int) round((float) Pedido::whereNotIn('estado', ['cancelado', 'anulado'])->sum('total'));
        $totalSellers = User::role('vendedor')->count();
        $totalClients = User::role('cliente')->count();

        $ordersByMonthRaw = Pedido::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym")
            ->selectRaw('COUNT(*) as total')
            ->whereBetween('created_at', [$monthWindow['startDate'], $monthWindow['endDate']])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $incomeByMonthRaw = Pedido::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym")
            ->selectRaw('SUM(total) as total')
            ->whereNotIn('estado', ['cancelado', 'anulado'])
            ->whereBetween('created_at', [$monthWindow['startDate'], $monthWindow['endDate']])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $statusRows = Pedido::query()
            ->select('estado')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('estado')
            ->orderByDesc('total')
            ->get();

        $userGrowthRaw = DB::table('users')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->whereIn('roles.name', ['cliente', 'vendedor'])
            ->whereBetween('users.created_at', [$monthWindow['startDate'], $monthWindow['endDate']])
            ->selectRaw("DATE_FORMAT(users.created_at, '%Y-%m') as ym")
            ->selectRaw("SUM(CASE WHEN roles.name = 'cliente' THEN 1 ELSE 0 END) as clientes")
            ->selectRaw("SUM(CASE WHEN roles.name = 'vendedor' THEN 1 ELSE 0 END) as vendedores")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        return [
            'metrics' => [
                'total_orders' => $totalOrders,
                'global_sales' => $globalSales,
                'total_sellers' => $totalSellers,
                'total_clients' => $totalClients,
            ],
            'charts' => [
                'global_sales_by_month' => [
                    'labels' => $monthWindow['labels'],
                    'data' => $this->mapMonthlyData($monthWindow['keys'], $ordersByMonthRaw),
                ],
                'global_income_by_month' => [
                    'labels' => $monthWindow['labels'],
                    'data' => $this->mapMonthlyData($monthWindow['keys'], $incomeByMonthRaw),
                ],
                'orders_by_status' => [
                    'labels' => $statusRows->pluck('estado')->map(fn ($status) => ucfirst((string) $status))->values()->all(),
                    'data' => $statusRows->pluck('total')->map(fn ($value) => (int) $value)->values()->all(),
                ],
                'users_growth' => [
                    'labels' => $monthWindow['labels'],
                    'clientes' => $this->mapMonthlyData($monthWindow['keys'], $userGrowthRaw, 'clientes'),
                    'vendedores' => $this->mapMonthlyData($monthWindow['keys'], $userGrowthRaw, 'vendedores'),
                ],
            ],
        ];
    }

    public function getAdminBusinessIntelligenceData(): array
    {
        $monthWindow = $this->buildMonthWindow(12);

        $solicitudesAprobadas = Solicitud::whereIn('estado', ['aceptada', 'aprobada'])->count();
        $solicitudesPendientes = Solicitud::where('estado', 'pendiente')->count();
        $solicitudesRechazadas = Solicitud::where('estado', 'rechazada')->count();

        $sellerPerformance = PedidoDetalle::query()
            ->join('pedidos', 'pedido_detalles.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'pedido_detalles.producto_id', '=', 'productos.id')
            ->join('users', 'users.id', '=', 'productos.user_id')
            ->whereNotIn('pedidos.estado', ['cancelado', 'anulado'])
            ->groupBy('users.id', 'users.name')
            ->select('users.id', 'users.name')
            ->selectRaw('COUNT(DISTINCT pedidos.id) as pedidos')
            ->selectRaw('SUM(pedido_detalles.cantidad * pedido_detalles.precio) as ingresos')
            ->get();

        $successfulSellers = $sellerPerformance
            ->filter(fn ($row) => ((int) $row->pedidos >= 10) || ((float) $row->ingresos >= 1000000))
            ->count();

        $registeredUsers = User::count();
        $buyersCount = Pedido::distinct('user_id')->count('user_id');
        $conversionRate = $registeredUsers > 0 ? round(($buyersCount / $registeredUsers) * 100, 2) : 0;

        $flowData = [
            'registrados' => $registeredUsers,
            'compradores' => $buyersCount,
            'recurrentes' => Pedido::query()
                ->select('user_id')
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) >= 2')
                ->count(),
        ];

        $solicitudesEvolutionRaw = Solicitud::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym")
            ->selectRaw("SUM(CASE WHEN estado IN ('aceptada','aprobada') THEN 1 ELSE 0 END) as aprobadas")
            ->selectRaw("SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes")
            ->selectRaw("SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas")
            ->whereBetween('created_at', [$monthWindow['startDate'], $monthWindow['endDate']])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $marketGrowthOrdersRaw = Pedido::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym")
            ->selectRaw('COUNT(*) as pedidos')
            ->selectRaw('SUM(total) as ingresos')
            ->whereBetween('created_at', [$monthWindow['startDate'], $monthWindow['endDate']])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $retentionRate = $this->calculateRetentionRate();
        $monthGrowth = $this->calculateGrowthForCurrentMonth();

        $insights = collect();
        $insights->push('La conversión de registrados a compradores es de ' . $conversionRate . '%.');
        $insights->push('Vendedores exitosos detectados: ' . $successfulSellers . '.');
        if ($monthGrowth['income_growth'] > 0) {
            $insights->push('Las ventas crecieron ' . $monthGrowth['income_growth'] . '% respecto al mes anterior.');
        } else {
            $insights->push('Las ventas cayeron ' . abs($monthGrowth['income_growth']) . '% frente al mes anterior.');
        }

        $alerts = collect();
        if ($monthGrowth['income_growth'] < 0) {
            $alerts->push(['type' => 'danger', 'text' => 'Alerta: caída de ingresos respecto al mes anterior.']);
        }
        if ($solicitudesPendientes > 20) {
            $alerts->push(['type' => 'warning', 'text' => 'Hay un volumen alto de solicitudes pendientes de revisión.']);
        }
        if ($this->usersRegisteredCurrentMonth() < 5) {
            $alerts->push(['type' => 'warning', 'text' => 'Pocos registros de usuarios en el mes actual.']);
        }
        if ($alerts->isEmpty()) {
            $alerts->push(['type' => 'success', 'text' => 'Sin alertas críticas por el momento.']);
        }

        return [
            'metrics' => [
                'solicitudes_aprobadas' => $solicitudesAprobadas,
                'solicitudes_pendientes' => $solicitudesPendientes,
                'solicitudes_rechazadas' => $solicitudesRechazadas,
                'successful_sellers' => $successfulSellers,
                'conversion_rate' => $conversionRate,
                'retention_rate' => $retentionRate,
                'period_comparison' => $monthGrowth,
            ],
            'charts' => [
                'flow' => [
                    'labels' => ['Registrados', 'Compradores', 'Recurrentes'],
                    'data' => array_values($flowData),
                ],
                'conversion' => [
                    'labels' => ['Compradores', 'No compradores'],
                    'data' => [$buyersCount, max($registeredUsers - $buyersCount, 0)],
                ],
                'solicitudes_evolution' => [
                    'labels' => $monthWindow['labels'],
                    'aprobadas' => $this->mapMonthlyData($monthWindow['keys'], $solicitudesEvolutionRaw, 'aprobadas'),
                    'pendientes' => $this->mapMonthlyData($monthWindow['keys'], $solicitudesEvolutionRaw, 'pendientes'),
                    'rechazadas' => $this->mapMonthlyData($monthWindow['keys'], $solicitudesEvolutionRaw, 'rechazadas'),
                ],
                'market_growth' => [
                    'labels' => $monthWindow['labels'],
                    'pedidos' => $this->mapMonthlyData($monthWindow['keys'], $marketGrowthOrdersRaw, 'pedidos'),
                    'ingresos' => $this->mapMonthlyData($monthWindow['keys'], $marketGrowthOrdersRaw, 'ingresos'),
                ],
                'retention' => [
                    'labels' => ['Retención', 'No retenidos'],
                    'data' => [$retentionRate, max(100 - $retentionRate, 0)],
                ],
            ],
            'insights' => $insights,
            'alerts' => $alerts,
        ];
    }

    public function getReportData(string $type, array $filters, ?int $sellerId = null): array
    {
        $from = !empty($filters['from']) ? Carbon::parse($filters['from'])->startOfDay() : null;
        $to = !empty($filters['to']) ? Carbon::parse($filters['to'])->endOfDay() : null;
        $status = $filters['status'] ?? null;
        $selectedSeller = !empty($filters['seller_id']) ? (int) $filters['seller_id'] : null;

        switch ($type) {
            case 'ventas_vendedor':
                return $this->buildSellerSalesReport($from, $to, $selectedSeller, $sellerId);
            case 'global':
                return $this->buildGlobalReport($from, $to);
            case 'pedidos':
                return $this->buildOrdersReport($from, $to, $status, $selectedSeller, $sellerId);
            case 'solicitudes':
                return $this->buildSolicitudesReport($from, $to);
            default:
                return [
                    'title' => 'Reporte',
                    'headers' => ['Mensaje'],
                    'rows' => collect([['Tipo de reporte no válido']]),
                ];
        }
    }

    private function buildSellerSalesReport(?Carbon $from, ?Carbon $to, ?int $selectedSeller, ?int $restrictedSellerId): array
    {
        $query = PedidoDetalle::query()
            ->join('pedidos', 'pedido_detalles.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'pedido_detalles.producto_id', '=', 'productos.id')
            ->join('users as vendedores', 'vendedores.id', '=', 'productos.user_id')
            ->whereNotIn('pedidos.estado', ['cancelado', 'anulado']);

        if ($from && $to) {
            $query->whereBetween('pedidos.created_at', [$from, $to]);
        }

        if ($restrictedSellerId) {
            $query->where('productos.user_id', $restrictedSellerId);
        } elseif ($selectedSeller) {
            $query->where('productos.user_id', $selectedSeller);
        }

        $rows = $query
            ->select('vendedores.name as vendedor')
            ->selectRaw('COUNT(DISTINCT pedidos.id) as pedidos')
            ->selectRaw('SUM(pedido_detalles.cantidad) as unidades')
            ->selectRaw('SUM(pedido_detalles.cantidad * pedido_detalles.precio) as ingresos')
            ->groupBy('vendedores.name')
            ->orderByDesc('ingresos')
            ->get()
            ->map(fn ($row) => [
                $row->vendedor,
                (int) $row->pedidos,
                (int) $row->unidades,
                (int) round((float) $row->ingresos),
            ]);

        return [
            'title' => 'Reporte de ventas por vendedor',
            'headers' => ['Vendedor', 'Pedidos', 'Unidades vendidas', 'Ingresos (COP)'],
            'rows' => $rows,
        ];
    }

    private function buildGlobalReport(?Carbon $from, ?Carbon $to): array
    {
        $query = Pedido::query();

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        $rows = $query
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as periodo")
            ->selectRaw('COUNT(*) as pedidos')
            ->selectRaw('SUM(total) as ingresos')
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get()
            ->map(fn ($row) => [
                $row->periodo,
                (int) $row->pedidos,
                (int) round((float) $row->ingresos),
            ]);

        return [
            'title' => 'Reporte global del sistema',
            'headers' => ['Periodo', 'Total pedidos', 'Ingresos (COP)'],
            'rows' => $rows,
        ];
    }

    private function buildOrdersReport(?Carbon $from, ?Carbon $to, ?string $status, ?int $selectedSeller, ?int $restrictedSellerId): array
    {
        $query = Pedido::query()
            ->leftJoin('users as compradores', 'compradores.id', '=', 'pedidos.user_id')
            ->select('pedidos.id', 'compradores.name as comprador', 'pedidos.estado', 'pedidos.total', 'pedidos.created_at');

        if ($from && $to) {
            $query->whereBetween('pedidos.created_at', [$from, $to]);
        }

        if (!empty($status)) {
            $query->where('pedidos.estado', $status);
        }

        if ($restrictedSellerId || $selectedSeller) {
            $sellerId = $restrictedSellerId ?: $selectedSeller;
            $query->whereExists(function ($exists) use ($sellerId) {
                $exists->select(DB::raw(1))
                    ->from('pedido_detalles')
                    ->join('productos', 'productos.id', '=', 'pedido_detalles.producto_id')
                    ->whereColumn('pedido_detalles.pedido_id', 'pedidos.id')
                    ->where('productos.user_id', $sellerId);
            });
        }

        $rows = $query
            ->orderByDesc('pedidos.created_at')
            ->get()
            ->map(fn ($row) => [
                (int) $row->id,
                $row->comprador ?? 'N/A',
                ucfirst((string) $row->estado),
                (int) round((float) $row->total),
                Carbon::parse($row->created_at)->format('Y-m-d H:i'),
            ]);

        return [
            'title' => 'Reporte de pedidos',
            'headers' => ['Pedido', 'Cliente', 'Estado', 'Total (COP)', 'Fecha'],
            'rows' => $rows,
        ];
    }

    private function buildSolicitudesReport(?Carbon $from, ?Carbon $to): array
    {
        $query = Solicitud::query();

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        $rows = $query
            ->orderByDesc('created_at')
            ->get(['id', 'nombre', 'email', 'estado', 'created_at'])
            ->map(fn ($row) => [
                (int) $row->id,
                $row->nombre,
                $row->email,
                ucfirst((string) $row->estado),
                Carbon::parse($row->created_at)->format('Y-m-d H:i'),
            ]);

        return [
            'title' => 'Reporte de solicitudes de vendedores',
            'headers' => ['ID', 'Nombre', 'Email', 'Estado', 'Fecha'],
            'rows' => $rows,
        ];
    }

    private function buildMonthWindow(int $months): array
    {
        $start = now()->copy()->startOfMonth()->subMonths($months - 1);
        $labels = [];
        $keys = [];

        for ($index = 0; $index < $months; $index++) {
            $date = $start->copy()->addMonths($index);
            $labels[] = $date->locale('es')->translatedFormat('M Y');
            $keys[] = $date->format('Y-m');
        }

        return [
            'startDate' => $start->copy()->startOfMonth(),
            'endDate' => now()->copy()->endOfMonth(),
            'labels' => $labels,
            'keys' => $keys,
        ];
    }

    private function mapMonthlyData(array $keys, Collection $rows, string $field = 'total'): array
    {
        $indexed = $rows->keyBy('ym');

        return collect($keys)
            ->map(function ($key) use ($indexed, $field) {
                $row = $indexed->get($key);
                if (!$row) {
                    return 0;
                }

                return (int) round((float) ($row->{$field} ?? 0));
            })
            ->all();
    }

    private function calculateGrowth(int $current, int $previous): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }

    private function calculateGrowthForCurrentMonth(): array
    {
        $currentStart = now()->startOfMonth();
        $currentEnd = now()->endOfMonth();
        $previousStart = now()->copy()->subMonth()->startOfMonth();
        $previousEnd = now()->copy()->subMonth()->endOfMonth();

        $currentIncome = (int) round((float) Pedido::whereBetween('created_at', [$currentStart, $currentEnd])->sum('total'));
        $previousIncome = (int) round((float) Pedido::whereBetween('created_at', [$previousStart, $previousEnd])->sum('total'));

        $currentOrders = Pedido::whereBetween('created_at', [$currentStart, $currentEnd])->count();
        $previousOrders = Pedido::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        return [
            'income_growth' => $this->calculateGrowth($currentIncome, $previousIncome),
            'orders_growth' => $this->calculateGrowth($currentOrders, $previousOrders),
            'current_income' => $currentIncome,
            'previous_income' => $previousIncome,
            'current_orders' => $currentOrders,
            'previous_orders' => $previousOrders,
        ];
    }

    private function calculateRetentionRate(): float
    {
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        $previousMonthStart = now()->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = now()->copy()->subMonth()->endOfMonth();

        $previousBuyers = Pedido::whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->distinct('user_id')
            ->pluck('user_id');

        if ($previousBuyers->isEmpty()) {
            return 0.0;
        }

        $retained = Pedido::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->whereIn('user_id', $previousBuyers)
            ->distinct('user_id')
            ->count('user_id');

        return round(($retained / max($previousBuyers->count(), 1)) * 100, 2);
    }

    private function usersRegisteredCurrentMonth(): int
    {
        return User::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
    }
}
