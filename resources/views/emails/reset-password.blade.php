<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<title>Restablecer contraseña - StartPlace</title>
	</head>
	<body style="font-family:Arial,Helvetica,sans-serif;background:#f6f8fa;margin:0;padding:20px;">
		<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
			<tr>
				<td align="center">
					<table width="600" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 6px 18px rgba(0,0,0,0.06);">
						<tr>
							<td style="padding:18px 24px;background:linear-gradient(90deg,#0b5ed7,#0a58ca);color:#fff;text-align:left;">
								<h2 style="margin:0;font-size:20px;">StartPlace</h2>
							</td>
						</tr>
						<tr>
							<td style="padding:24px;">
								<p style="margin:0 0 12px 0;color:#333;font-size:15px;">Hola,</p>
								<p style="margin:0 0 16px 0;color:#555;line-height:1.5;">Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>StartPlace</strong>. Si fuiste tú, haz clic en el botón de abajo para elegir una nueva contraseña.</p>

								<div style="text-align:center;margin:22px 0;">
									<a href="{{ url('/password/reset/'.$token) }}" style="display:inline-block;padding:12px 22px;background:#0b5ed7;color:#fff;border-radius:6px;text-decoration:none;font-weight:600;">Restablecer mi contraseña</a>
								</div>

								<p style="color:#777;font-size:13px;">Si el botón no funciona, copia y pega la siguiente URL en tu navegador:</p>
								<p style="word-break:break-all;color:#0b5ed7;font-size:13px;margin-bottom:18px;">{{ url('/password/reset/'.$token) }}</p>

								<p style="color:#777;font-size:13px;margin-top:20px;">Si no solicitaste el restablecimiento de contraseña, puedes ignorar este correo; la contraseña permanecerá sin cambios.</p>

								<p style="color:#777;font-size:13px;margin-top:6px;">Saludos,<br><strong>El equipo de StartPlace</strong></p>
							</td>
						</tr>
						<tr>
							<td style="padding:12px 24px;background:#fafbfd;color:#9aa4b2;font-size:12px;text-align:center;">© {{ date('Y') }} StartPlace. Todos los derechos reservados.</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>