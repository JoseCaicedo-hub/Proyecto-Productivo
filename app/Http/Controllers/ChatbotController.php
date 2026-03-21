<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function message(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $message = mb_strtolower(trim($data['message']));

        if ($this->containsAny($message, ['estado', 'pedido', 'pedidos', 'compra', 'envio', 'enviado'])) {
            return response()->json([
                'reply' => $this->replyPedidos(),
            ]);
        }

        if ($this->containsAny($message, ['comprar', 'compra', 'carrito', 'checkout', 'pagar'])) {
            return response()->json([
                'reply' => "Para comprar en StartPlace:\n1) Entra a Tienda\n2) Agrega productos al carrito\n3) Ve al carrito y confirma\n4) Completa dirección y pago\n5) Revisa tu estado en Mis pedidos.",
            ]);
        }

        if ($this->containsAny($message, ['vender', 'vendedor', 'emprendimiento', 'solicitud'])) {
            return response()->json([
                'reply' => $this->replyVender(),
            ]);
        }

        if ($this->containsAny($message, ['empresa', 'empresas', 'tienda', 'vendedores'])) {
            return response()->json([
                'reply' => "Puedes consultar empresas y vendedores desde Tienda, filtrando por categorías y empresa. Si deseas ser vendedor, inicia en /solicitud.",
            ]);
        }

        if ($this->containsAny($message, ['soporte', 'contacto', 'ayuda', 'correo', 'email'])) {
            return response()->json([
                'reply' => "Para soporte, usa el formulario en /contactanos. También puedes escribirnos por la sección de contacto para casos de pedidos o incidencias.",
            ]);
        }

        if ($this->containsAny($message, ['problema', 'error', 'reclamo', 'devolucion', 'cancelar'])) {
            return response()->json([
                'reply' => $this->replyProblemasPedido(),
            ]);
        }

        return response()->json([
            'reply' => "Puedo ayudarte con: estado de pedidos, cómo comprar, cómo vender, empresas/vendedores y soporte. Escríbeme una de esas opciones 😊",
        ]);
    }

    private function replyPedidos(): string
    {
        if (!auth()->check()) {
            return "Puedo darte información general de pedidos. Para consultar tu estado exacto, inicia sesión y entra a /perfil/pedidos.";
        }

        $lastPedido = Pedido::where('user_id', auth()->id())->latest()->first();

        if (!$lastPedido) {
            return "Aún no tienes pedidos registrados. Puedes empezar en /tienda y agregar productos al carrito.";
        }

        return "Tu pedido más reciente es #{$lastPedido->id} y su estado actual es: {$lastPedido->estado}. Puedes ver el detalle completo en /perfil/pedidos.";
    }

    private function replyVender(): string
    {
        if (!auth()->check()) {
            return "Para vender en StartPlace, primero crea una cuenta y luego envía tu solicitud en /solicitud. El equipo admin revisará y aprobará tu acceso como vendedor.";
        }

        if (auth()->user()->hasRole('vendedor')) {
            return "Tu cuenta ya está aprobada como vendedor ✅. Puedes gestionar productos y pedidos desde tu panel en /dashboard y /almacen.";
        }

        return "Para convertirte en vendedor, completa tu solicitud en /solicitud. Mientras esté pendiente no podrás enviar otra.";
    }

    private function replyProblemasPedido(): string
    {
        if (!auth()->check()) {
            return "Si tienes problemas con un pedido, inicia sesión y usa /contactanos indicando número de pedido y detalle del caso.";
        }

        return "Si tienes problemas con un pedido, revisa primero /perfil/pedidos. Si el problema continúa, envía el caso por /contactanos con el ID del pedido.";
    }

    private function containsAny(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
