<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nueva consulta</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f6f8fb; color:#222; margin:0; padding:20px;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width:700px; margin:0 auto; background:#ffffff; border-radius:8px; overflow:hidden;">
    <tr style="background:#0d6efd; color:#fff;">
      <td style="padding:18px 24px;">
        <h2 style="margin:0; font-size:18px;">StartPlace - Nueva consulta recibida</h2>
      </td>
    </tr>
    <tr>
      <td style="padding:18px 24px;">
        <p style="margin:0 0 12px 0;">Se ha recibido una nueva consulta desde el formulario <strong>Contactanos</strong>. A continuación los detalles:</p>

        <table style="width:100%; border-collapse:collapse;">
          <tr>
            <td style="padding:6px 0; width:140px; color:#555;">Tipo:</td>
            <td style="padding:6px 0;">{{ $data['tipo'] ?? 'N/A' }}</td>
          </tr>
          <tr style="background:#fafafa;">
            <td style="padding:6px 0; color:#555;">Vendedor:</td>
            <td style="padding:6px 0;">{{ $data['vendedor_name'] ?? ($data['vendedor'] ?? 'Todos') }}</td>
          </tr>
          <tr>
            <td style="padding:6px 0; color:#555;">Nombre:</td>
            <td style="padding:6px 0;">{{ $data['nombre'] ?? 'N/A' }}</td>
          </tr>
          <tr style="background:#fafafa;">
            <td style="padding:6px 0; color:#555;">Email:</td>
            <td style="padding:6px 0;"><a href="mailto:{{ $data['email'] ?? '' }}">{{ $data['email'] ?? 'N/A' }}</a></td>
          </tr>
          <tr>
            <td style="padding:6px 0; color:#555;">Teléfono:</td>
            <td style="padding:6px 0;">{{ $data['telefono'] ?? 'N/A' }}</td>
          </tr>
          <tr style="background:#fafafa;">
            <td style="padding:6px 0; color:#555;">ID Pedido:</td>
            <td style="padding:6px 0;">{{ $data['pedido_id'] ?? 'N/A' }}</td>
          </tr>
          <tr>
            <td style="padding:6px 0; color:#555;">Producto:</td>
            <td style="padding:6px 0;">{{ $data['producto'] ?? 'N/A' }}</td>
          </tr>
        </table>

        <h4 style="margin-top:16px; margin-bottom:8px;">Mensaje</h4>
        <div style="padding:12px; background:#f4f6f9; border-radius:6px; color:#333; white-space:pre-wrap;">{{ $data['mensaje'] ?? '' }}</div>

        @if(!empty($data['adjunto_path']))
          <p style="margin-top:14px; color:#444;">Se ha incluido un adjunto con el envío. Si no aparece en el correo, revisa el almacenamiento en disco.</p>
        @endif

        <p style="margin-top:18px; color:#777; font-size:13px;">Este correo fue enviado desde el formulario público del sitio. Responder a este correo enviará un mensaje al usuario (si el correo está disponible).</p>
      </td>
    </tr>
    <tr>
      <td style="background:#f1f5f9; padding:12px 24px; text-align:center; color:#6b7280; font-size:12px;">StartPlace • Soporte</td>
    </tr>
  </table>
</body>
</html>
