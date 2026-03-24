@extends('plantilla.app')

@section('contenido')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h3 class="mb-0">Dashboard Administrador</h3>
        <a href="{{ route('dashboard.admin.bi') }}" class="btn btn-primary">Ver BI Avanzado</a>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted">Pedidos del sistema</small><h4 class="mb-0">{{ $metrics['total_orders'] }}</h4></div></div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted">Ventas globales</small><h4 class="mb-0">@formatCOP($metrics['global_sales'])</h4></div></div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted">Vendedores</small><h4 class="mb-0">{{ $metrics['total_sellers'] }}</h4></div></div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><small class="text-muted">Clientes</small><h4 class="mb-0">{{ $metrics['total_clients'] }}</h4></div></div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-6"><div class="card shadow-sm h-100"><div class="card-header"><strong>Ventas globales por mes</strong></div><div class="card-body"><canvas id="adminSalesByMonthChart" height="140"></canvas></div></div></div>
        <div class="col-12 col-lg-6"><div class="card shadow-sm h-100"><div class="card-header"><strong>Ingresos por mes</strong></div><div class="card-body"><canvas id="adminIncomeByMonthChart" height="140"></canvas></div></div></div>
        <div class="col-12 col-lg-6"><div class="card shadow-sm h-100"><div class="card-header"><strong>Pedidos por estado</strong></div><div class="card-body"><canvas id="adminOrdersStatusChart" height="140"></canvas></div></div></div>
        <div class="col-12 col-lg-6"><div class="card shadow-sm h-100"><div class="card-header"><strong>Crecimiento de usuarios</strong></div><div class="card-body"><canvas id="adminUsersGrowthChart" height="140"></canvas></div></div></div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header"><strong>Reportes del sistema</strong></div>
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.reports.export') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="type" class="form-select" required>
                        <option value="global">Global del sistema</option>
                        <option value="ventas_vendedor">Ventas por vendedor</option>
                        <option value="pedidos">Pedidos</option>
                        <option value="solicitudes">Solicitudes de vendedores</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="from" class="form-control">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="to" class="form-control">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Vendedor</label>
                    <select name="seller_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="enviado">Enviado</option>
                        <option value="entregado">Entregado</option>
                        <option value="cancelado">Cancelado</option>
                        <option value="anulado">Anulado</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 d-grid">
                    <button type="submit" name="format" value="pdf" class="btn btn-outline-danger">Descargar PDF</button>
                </div>
                <div class="col-6 col-md-2 d-grid">
                    <button type="submit" name="format" value="excel" class="btn btn-outline-success">Descargar Excel</button>
                </div>
            </form>
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
        if (!canvas) return;
        new Chart(canvas, config);
    }

    buildChart('adminSalesByMonthChart', {
        type: 'line',
        data: {
            labels: initialCharts.global_sales_by_month.labels,
            datasets: [{ label: 'Pedidos', data: initialCharts.global_sales_by_month.data, borderColor: '#0b63d6', backgroundColor: 'rgba(11,99,214,.15)', fill: true, tension: 0.25 }],
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    buildChart('adminIncomeByMonthChart', {
        type: 'bar',
        data: {
            labels: initialCharts.global_income_by_month.labels,
            datasets: [{ label: 'Ingresos', data: initialCharts.global_income_by_month.data, backgroundColor: 'rgba(67,160,71,.78)' }],
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    buildChart('adminOrdersStatusChart', {
        type: 'pie',
        data: {
            labels: initialCharts.orders_by_status.labels,
            datasets: [{ data: initialCharts.orders_by_status.data, backgroundColor: ['#1e88e5','#f9a825','#43a047','#e53935','#8e24aa'] }],
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    buildChart('adminUsersGrowthChart', {
        type: 'line',
        data: {
            labels: initialCharts.users_growth.labels,
            datasets: [
                { label: 'Clientes', data: initialCharts.users_growth.clientes, borderColor: '#0b63d6', backgroundColor: 'rgba(11,99,214,.10)', tension: 0.25 },
                { label: 'Vendedores', data: initialCharts.users_growth.vendedores, borderColor: '#43a047', backgroundColor: 'rgba(67,160,71,.10)', tension: 0.25 }
            ],
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
})();
</script>
<script>
    document.getElementById('mnuDashboard')?.classList.add('active');
</script>
@endpush
