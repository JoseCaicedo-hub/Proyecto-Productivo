<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Solicitud;

class SolicitudRechazada extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;

    public function __construct(Solicitud $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function build()
    {
        return $this->subject('Tu solicitud ha sido procesada')
                    ->view('emails.solicitud_rechazada')
                    ->with(['solicitud' => $this->solicitud]);
    }
}
