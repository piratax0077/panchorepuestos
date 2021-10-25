<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class repuesto extends Model
{
    protected $table='repuestos';
    //FALTA PAIS DE ORIGEN Y STOCK
    protected $fillable=[
        'codigo_interno',
        'descripcion',
        'medidas',
        'version_vehiculo',
        'cod_repuesto_proveedor',
        'codigo_OEM_repuesto',
        'precio_compra',
        'pu_neto',
        'precio_venta',
        'exento_iva',
        'stock_minimo',
        'stock_maximo',
        'stock_actual',
        'fecha_actualiza_precio',
        'codigo_barras',
        'id_unidad_venta',
        'id_familia',
        'id_marca_repuesto',
        'id_proveedor',
        'id_pais',
        'usuarios_id',
        'activo'
    ];

  	public function familia()
  	{
  		return $this->belongsTo('App\familia','id_familia');
  	}

    public function marcarepuesto()
    {
    	return $this->belongsTo('App\marcarepuesto','id_marca_repuesto');
    }

    public function proveedor()
    {
    	return $this->belongsTo('App\proveedor','id_proveedor');
    }

    public function pais()
    {
        return $this->belongsTo('App\pais','id_pais');
    }

    public function carrito_compra(){
        return $this->hasMany('App\carrito_compra');
    }

}
