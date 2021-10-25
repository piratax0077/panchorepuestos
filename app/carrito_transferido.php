<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class carrito_transferido extends Model
{
    protected $table='carrito_transferido';
    protected $fillable=[
        'nombre_carrito',
        'usuarios_id',
        'cajeros_id',
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

    public function dame_total()
    {
        $total = carrito_transferido::where('cajeros_id', Auth::user()->id)->sum('total_item');
        return $total;
    }

}