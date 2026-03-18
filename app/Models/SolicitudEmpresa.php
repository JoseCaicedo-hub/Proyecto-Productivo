<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudEmpresa extends Model
{
    protected $table = 'solicitud_empresas';

    protected $fillable = [
        'user_id',
        'nombre',
        'logo',
        'descripcion',
        'contacto',
        'documento_pdf',
        'estado',
        'admin_id',
        'motivo_rechazo',
        'revisado_en',
    ];

    protected $casts = [
        'revisado_en' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
