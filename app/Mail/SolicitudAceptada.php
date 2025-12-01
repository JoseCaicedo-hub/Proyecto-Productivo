<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Solicitud;

class SolicitudAceptada extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;
    public $user;

    public function __construct(Solicitud $solicitud, $user = null)
    {
        $this->solicitud = $solicitud;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Tu solicitud ha sido aceptada')
                    ->view('emails.solicitud_aceptada')
                    ->with(['solicitud' => $this->solicitud, 'user' => $this->user]);
    }
}
