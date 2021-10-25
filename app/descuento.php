<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class descuento extends Model
{
    protected $table='descuentos';
    protected $fillable=[
    	'id_cliente',
        'id_familia',
        'porcentaje',
        'activo',
        'usuarios_id',
    ];
}
