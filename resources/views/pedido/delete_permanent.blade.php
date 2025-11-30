<div class="modal fade" id="modal-eliminar-{{$reg->id}}" tabindex="-1" role="dialog" aria-labelledby="modalEliminarLabel{{$reg->id}}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-sm" style="overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(90deg,#343a40,#212529); color:#fff; border-bottom:0;">
                <h5 class="modal-title mb-0" id="modalEliminarLabel{{$reg->id}}">Eliminar pedido #{{$reg->id}}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form action="{{ route('pedido.eliminar.permanente', $reg->id) }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <p class="mb-2">Esta acción eliminará permanentemente el pedido realizado el <strong>{{ $reg->created_at->format('d/m/Y H:i') }}</strong> por <strong>${{ number_format($reg->total,2) }}</strong>.</p>
                    <p class="text-muted small">No se podrá recuperar. Si necesitas un registro, descarga los detalles antes de continuar.</p>

                    <div class="mt-3 p-3 rounded" style="background:#f8f9fa;border:1px solid #e9ecef;">
                        <strong>Resumen del pedido</strong>
                        <ul class="small mb-0 mt-2">
                            @foreach($reg->detalles as $detalle)
                                <li>{{ $detalle->producto->nombre }} — x{{ $detalle->cantidad }} — ${{ number_format($detalle->precio,2) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="modal-footer" style="border-top:0;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Eliminar permanentemente</button>
                </div>
            </form>
        </div>
    </div>
</div>
