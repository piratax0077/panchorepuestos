<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\local;
use App\repuesto;
use App\pais;

class inventario_controlador extends Controller
{
    //
    public function index(){
        $locales=local::where('activo',1)->get();
        if(count($locales)>0){
            return view('inventario.principal',['locales' => $locales]);
        }else{
            return "cero";
        }
        
    }

    public function inventario_por_local($id){
        $repuestos = repuesto::where('repuestos.local_id', $id)
        ->join('paises', 'repuestos.id_pais', 'paises.id')
        ->select('repuestos.*','paises.nombre_pais')
        ->take(40)
        ->get();
        return $repuestos;
    }

    public function traslado(Request $request){
        $local_id = $request->id;
        return $local_id;
    }

    public function damerepuesto($id){
        // $repuesto = repuesto::where('id', $id)
        // ->get();

        $repuesto=repuesto::where('repuestos.id',$id)
                    ->join('proveedores','repuestos.id_proveedor','proveedores.id')
                    ->join('paises', 'repuestos.id_pais', 'paises.id')
                    ->select('proveedores.empresa_nombre','repuestos.*','paises.nombre_pais')
                    ->get();
        
        return $repuesto;
    }

    public function ordenar($local_id, $orden_id){

        $id = $local_id;
        $tipo_orden = $orden_id;
        
        if($tipo_orden == 1){
            $repuestos = repuesto::where('repuestos.local_id', $id)
            ->join('paises', 'repuestos.id_pais', 'paises.id')
            ->select('repuestos.*','paises.nombre_pais')
            ->orderBy('stock_actual','desc')
            ->take(40)
            ->get();
            return $repuestos;
        }else{
            $repuestos = repuesto::where('repuestos.local_id', $id)
            ->join('paises', 'repuestos.id_pais', 'paises.id')
            ->select('repuestos.*','paises.nombre_pais')
            ->orderBy('stock_actual','asc')
            ->take(40)
            ->get();
            return $repuestos;
        }
        
    }

}
