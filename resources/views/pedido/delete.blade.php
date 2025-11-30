<div class="modal fade" id="modal-cancelar-{{$reg->id}}" tabindex="-1" role="dialog" aria-labelledby="modalCancelarLabel{{$reg->id}}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(90deg,#ff6b6b,#ff4757); color:#fff; border-bottom:0;">
                <div class="d-flex align-items-center">
                    <div class="me-3 bg-white rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:22px"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="modalCancelarLabel{{$reg->id}}">Cancelar pedido #{{$reg->id}}</h5>
                        <small class="text-white-50">Acción irreversible: el pedido pasará a estado <strong>Cancelado</strong></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form action="{{ route('pedido.cancelar', $reg->id) }}" method="POST">
                @csrf
                <div class="modal-body py-4" style="background:linear-gradient(180deg,#fff,#fff);">
                    <p class="mb-2">¿Estás seguro que deseas cancelar el pedido realizado el <strong>{{ $reg->created_at->format('d/m/Y H:i') }}</strong> por un total de <strong>${{ number_format($reg->total,2) }}</strong>?</p>
                    <p class="text-muted small">Si cancelas el pedido, ya no podrá procesarse. Si prefieres, contacta soporte para asistencia.</p>
                    <div class="mt-3 p-3 rounded" style="background:#fff;border:1px solid #f1f1f1;">
                        <strong>Productos:</strong>
                        <ul class="small mb-0">
                            @foreach($reg->detalles as $detalle)
                                <li>{{ $detalle->producto->nombre }} — x{{ $detalle->cantidad }} — ${{ number_format($detalle->precio,2) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="modal-footer" style="background:linear-gradient(180deg,#fff,#fff);border-top:0;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Cancelar pedido</button>
                </div>
            </form>
        </div>
    </div>
</div>
