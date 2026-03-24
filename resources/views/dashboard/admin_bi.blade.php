@extends('plantilla.app')

@section('contenido')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h3 class="mb-0">Dashboard BI del Marketplace</h3>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">Volver al dashboard general</a>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Solicitudes aprobadas</small><h4 class="mb-0">{{ $metrics['solicitudes_aprobadas'] }}</h4></div></div></div>
        <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Solicitudes pendientes</small><h4 class="mb-0">{{ $metrics['solicitudes_pendientes'] }}</h4></div></div></div>
        <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Solicitudes rechazadas</small><h4 class="mb-0">{{ $metrics['solicitudes_rechazadas'] }}</h4></div></div></div>
        <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Vendedores exitosos</small><h4 class="mb-0">{{ $metrics['successful_sellers'] }}</h4></div></div></div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-6"><div class="card shadow-sm h-100"><div class="card-header"><strong>Flujo de usuarios</strong></div><div class="card-body"><canvas id="biFlowChart" height="140"></canvas></div></div></div>
        <div class="col-12 col-lg-6"><div class="card shadow-sm h-100"><div class="card-header"><strong>Conversión a compradores</strong></div><div class="card-body"><canvas id="biConversionChart" height="140"></canvas></div></div></div>
        <div class="col-12 col-lg-6"><div class="card shadow-sm h-100"><div class="card-header"><strong>Evolución de solicitudes</strong></div><div class="card-body"><canvas id="biSolicitudesChart" height="140"></canvas></div></div></div>
        <div class="col-12 col-lg-6"><div class="card shadow-sm h-100"><div class="card-header"><strong>Crecimiento marketplace</strong></div><div class="card-body"><canvas id="biMarketGrowthChart" height="140"></canvas></div></div></div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><strong>Panel de insights</strong></div>
                <div class="card-body">
                    <ul class="mb-0">
                        @foreach($insights as $insight)
                            <li class="mb-2">{{ $insight }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><strong>Alertas</strong></div>
                <div class="card-body">
                    @foreach($alerts as $alert)
                        <div class="alert alert-{{ $alert['type'] }} mb-2 py-2">{{ $alert['text'] }}</div>
                    @endforeach
                    <div class="small text-muted">Retención actual: {{ $metrics['retention_rate'] }}%</div>
                    <div class="small text-muted">Comparativa ingresos mes actual/anterior: {{ $metrics['period_comparison']['income_growth'] }}%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header"><strong>Reportes BI</strong></div>
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
                <div class="col-6 col-md-2 d-grid"><button type="submit" name="format" value="pdf" class="btn btn-outline-danger">PDF</button></div>
                <div class="col-6 col-md-2 d-grid"><button type="submit" name="format" value="excel" class="btn btn-outline-success">Excel</button></div>
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

    buildChart('biFlowChart', {
        type: 'bar',
        data: {
            labels: initialCharts.flow.labels,
            datasets: [{ label: 'Usuarios', data: initialCharts.flow.data, backgroundColor: ['#1e88e5','#43a047','#8e24aa'] }],
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    buildChart('biConversionChart', {
        type: 'doughnut',
        data: {
            labels: initialCharts.conversion.labels,
            datasets: [{ data: initialCharts.conversion.data, backgroundColor: ['#1e88e5','#b0bec5'] }],
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    buildChart('biSolicitudesChart', {
        type: 'line',
        data: {
            labels: initialCharts.solicitudes_evolution.labels,
            datasets: [
                { label: 'Aprobadas', data: initialCharts.solicitudes_evolution.aprobadas, borderColor: '#43a047', tension: 0.25 },
                { label: 'Pendientes', data: initialCharts.solicitudes_evolution.pendientes, borderColor: '#fb8c00', tension: 0.25 },
                { label: 'Rechazadas', data: initialCharts.solicitudes_evolution.rechazadas, borderColor: '#e53935', tension: 0.25 },
            ],
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    buildChart('biMarketGrowthChart', {
        type: 'bar',
        data: {
            labels: initialCharts.market_growth.labels,
            datasets: [
                { label: 'Pedidos', data: initialCharts.market_growth.pedidos, backgroundColor: 'rgba(30,136,229,.75)' },
                { label: 'Ingresos', data: initialCharts.market_growth.ingresos, backgroundColor: 'rgba(67,160,71,.75)' },
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
