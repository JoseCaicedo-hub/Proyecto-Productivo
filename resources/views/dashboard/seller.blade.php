@extends('plantilla.app')

@section('contenido')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h3 class="mb-0">Dashboard de Vendedor</h3>
        <span class="badge text-bg-primary">Crecimiento mensual: {{ $metrics['growth'] }}%</span>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Pedidos completados</small>
                    <h4 class="mb-0">{{ $metrics['completed_orders'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Ingresos generados</small>
                    <h4 class="mb-0">@formatCOP($metrics['total_revenue'])</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Productos vendidos</small>
                    <h4 class="mb-0">{{ $metrics['products_sold'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Ticket promedio</small>
                    <h4 class="mb-0">@formatCOP($metrics['average_ticket'])</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><strong>Ventas por mes</strong></div>
                <div class="card-body"><canvas id="sellerSalesByMonthChart" height="140"></canvas></div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><strong>Ingresos por mes</strong></div>
                <div class="card-body"><canvas id="sellerIncomeByMonthChart" height="140"></canvas></div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><strong>Top 5 productos vendidos</strong></div>
                <div class="card-body"><canvas id="sellerTopProductsChart" height="140"></canvas></div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><strong>Pedidos por estado</strong></div>
                <div class="card-body"><canvas id="sellerOrdersStatusChart" height="140"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-xl-5">
            <div class="card shadow-sm h-100">
                <div class="card-header"><strong>Notificaciones</strong></div>
                <div class="card-body">
                    @foreach($notifications as $notification)
                        <div class="alert alert-{{ $notification['type'] }} mb-2 py-2">{{ $notification['text'] }}</div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-7">
            <div class="card shadow-sm h-100">
                <div class="card-header"><strong>Ranking de productos</strong></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-end">Unidades</th>
                                <th class="text-end">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($productRanking as $row)
                            <tr>
                                <td>{{ $row->nombre }}</td>
                                <td class="text-end">{{ (int) $row->unidades }}</td>
                                <td class="text-end">@formatCOP((int) $row->ingresos)</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Sin datos por ahora</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm h-100">
                <div class="card-header"><strong>Últimos pedidos recientes</strong></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Comprador</th>
                                <th>Estado</th>
                                <th class="text-end">Subtotal vendedor</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->comprador ?? 'N/A' }}</td>
                                <td><span class="badge text-bg-secondary">{{ ucfirst((string) $order->estado) }}</span></td>
                                <td class="text-end">@formatCOP((int) $order->subtotal_vendedor)</td>
                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">Sin pedidos recientes</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"><strong>Reportes</strong></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard.reports.export') }}" class="row g-2">
                        <div class="col-12">
                            <label class="form-label">Tipo</label>
                            <select name="type" class="form-select" required>
                                <option value="ventas_vendedor">Ventas por vendedor</option>
                                <option value="pedidos">Pedidos</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Desde</label>
                            <input type="date" name="from" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="to" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Estado pedido</label>
                            <select name="status" class="form-select">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="enviado">Enviado</option>
                                <option value="entregado">Entregado</option>
                                <option value="cancelado">Cancelado</option>
                                <option value="anulado">Anulado</option>
                            </select>
                        </div>
                        <div class="col-6 d-grid">
                            <button type="submit" name="format" value="pdf" class="btn btn-outline-danger">Descargar PDF</button>
                        </div>
                        <div class="col-6 d-grid">
                            <button type="submit" name="format" value="excel" class="btn btn-outline-success">Descargar Excel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    const initialCharts = @json($initialCharts);

    function buildChart(id, config) {
        const canvas = document.getElementById(id);
        if (!canvas) return null;
        return new Chart(canvas, config);
    }

    buildChart('sellerSalesByMonthChart', {
        type: 'line',
        data: {
            labels: initialCharts.sales_by_month.labels,
            datasets: [{
                label: 'Pedidos',
                data: initialCharts.sales_by_month.data,
                borderColor: '#0b63d6',
                backgroundColor: 'rgba(11,99,214,0.12)',
                tension: 0.25,
                fill: true,
            }],
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    buildChart('sellerIncomeByMonthChart', {
        type: 'bar',
        data: {
            labels: initialCharts.income_by_month.labels,
            datasets: [{
                label: 'Ingresos (COP)',
                data: initialCharts.income_by_month.data,
                backgroundColor: 'rgba(30,136,229,0.8)'
            }],
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    buildChart('sellerTopProductsChart', {
        type: 'bar',
        data: {
            labels: initialCharts.top_products.labels,
            datasets: [{
                label: 'Unidades',
                data: initialCharts.top_products.data,
                backgroundColor: 'rgba(0,200,83,0.75)'
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
        }
    });

    buildChart('sellerOrdersStatusChart', {
        type: 'doughnut',
        data: {
            labels: initialCharts.orders_by_status.labels,
            datasets: [{
                data: initialCharts.orders_by_status.data,
                backgroundColor: ['#0b63d6', '#ff9800', '#43a047', '#e53935', '#6d4c41']
            }],
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
})();
</script>
<script>
    document.getElementById('mnuDashboard')?.classList.add('active');
</script>
@endpush
