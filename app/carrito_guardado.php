<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class carrito_guardado extends Model
{
    protected $table='carrito_guardado';
    protected $fillable=[
        'nombre_carrito',
        'usuarios_id',
        'item',
        'id_repuestos',
        'id_local',
        'id_unidad_venta',
        'cantidad',
        'pu',
        'subtotal_item',
        'descuento_item',
        'total_item'
    ];

}
