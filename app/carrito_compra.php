<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;
use App\repuesto;
use Debugbar;
use Illuminate\Support\Facades\Auth;

class carrito_compra extends Model
{
    protected $table='carrito_compras';
    protected $fillable=[
        'usuarios_id',
        'item',
        'id_repuestos',
        'id_local',
        'id_unidad_venta',
        'cantidad',
        'pu_neto',
        'pu',
        'subtotal_item',
        'descuento_item',
        'total_item'
    ];

    public function dame_total()
    {
        $total = carrito_compra::where('usuarios_id', Auth::user()->id)->sum('total_item');
        return $total;
    }

    public function dame_neto()
    {
        $total = carrito_compra::where('usuarios_id', Auth::user()->id)->sum('total_item');
        $neto=$total/ (1 + Session::get('PARAM_IVA'));
        return $neto;
    }

    public function dame_iva()
    {
        $total = carrito_compra::where('usuarios_id', Auth::user()->id)->sum('total_item');
        $neto=$total/ (1 + Session::get('PARAM_IVA'));
        $iva=$total-$neto;
        //Debugbar::addMessage('carrito_compra->dame_iva','depurador');
        return $iva;
    }

    public function dame_todo_carrito()
    {
        // OLD $todo_el_carrito=carrito_compra::where('usuarios_id', $c->usuarios_id)->get();
        $todo_el_carrito=carrito_compra::select('carrito_compras.*',
                                    'repuestos.codigo_interno',
                                    'repuestos.descripcion')
                                    ->where('carrito_compras.usuarios_id',Auth::user()->id)
                                    ->join('repuestos','carrito_compras.id_repuestos','repuestos.id')
                                    ->orderBy('carrito_compras.item','ASC')
                                    ->get();
        //Debugbar::addMessage('carrito_compra->dame_todo_carrito','depurador');
        return $todo_el_carrito;
    }

    public function carrito_transferido(){
        return $this->hasMany('App\carrito_transferido');
    }

    public function repuesto(){
        return $this->belongsTo('App\repuesto','id_repuesto');
    }

}
