<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Solicitud;

class SolicitudRecibida extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;

    public function __construct(Solicitud $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function build()
    {
        return $this->subject('Hemos recibido tu solicitud en StartPlace')
                    ->view('emails.solicitud_recibida')
                    ->with(['solicitud' => $this->solicitud]);
    }
}
