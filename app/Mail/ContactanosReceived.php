<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactanosReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->subject('Nueva consulta desde Contactanos: ' . ($this->data['tipo'] ?? 'Contacto'))
                     ->view('emails.contactanos_received')
                     ->with(['data' => $this->data]);

        if (!empty($this->data['email'])) {
            $mail->replyTo($this->data['email']);
        }

        if (!empty($this->data['adjunto_path'])) {
            try {
                $disk = 'public';
                if (\Storage::disk($disk)->exists($this->data['adjunto_path'])) {
                    $mail->attachFromStorageDisk($disk, $this->data['adjunto_path'], basename($this->data['adjunto_path']));
                }
            } catch (\Throwable $e) {
                // No detener el envío si falla el adjunto
                \Log::warning('No se pudo adjuntar archivo en ContactanosReceived: ' . $e->getMessage());
            }
        }

        return $mail;
    }
}
