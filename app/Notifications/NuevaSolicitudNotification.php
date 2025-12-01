<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Solicitud;

class NuevaSolicitudNotification extends Notification
{
    use Queueable;

    protected $solicitud;

    public function __construct(Solicitud $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'solicitud_id' => $this->solicitud->id,
            'nombre' => $this->solicitud->nombre,
            'email' => $this->solicitud->email,
            'titulo' => $this->solicitud->titulo,
            'idea' => 
                strlen($this->solicitud->idea) > 120 ? substr($this->solicitud->idea, 0, 117) . '...' : $this->solicitud->idea,
        ];
    }

    public function toMail($notifiable)
    {
        $url = route('admin.solicitudes.index');
        return (new MailMessage)
                    ->subject('Nueva solicitud de emprendimiento')
                    ->line('Se ha recibido una nueva solicitud de emprendimiento de ' . $this->solicitud->nombre)
                    ->action('Ver solicitudes', $url)
                    ->line('Revisa y procesa la solicitud desde el panel de administración.');
    }
}
