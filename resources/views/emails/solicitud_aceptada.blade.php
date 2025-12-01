<!doctype html>
<html>
<body>
    <p>Hola {{ $solicitud->nombre }},</p>
    <p>Nos complace informarte que tu solicitud ha sido <strong>aceptada</strong>.</p>
    @if(!empty($user))
        <p>Se te ha otorgado el rol de administrador en la plataforma con el usuario: <strong>{{ $user->email }}</strong></p>
    @else
        <p>Pronto nos pondremos en contacto para completar los detalles de acceso.</p>
    @endif
    <p>Gracias por tu interés en StartPlace.</p>
</body>
</html>
