<!doctype html>
<html>
<body>
    <p>Hola {{ $solicitud->nombre }},</p>
    <p>Hemos recibido tu solicitud de emprendimiento en StartPlace. Nuestro equipo revisará tu propuesta y te contactaremos pronto.</p>
    <p><strong>Título:</strong> {{ $solicitud->titulo ?? '-' }}</p>
    <p><strong>Idea:</strong></p>
    <p>{{ nl2br(e($solicitud->idea)) }}</p>
    <p>Gracias por confiar en StartPlace.</p>
    <p>--<br/>StartPlace</p>
</body>
</html>
