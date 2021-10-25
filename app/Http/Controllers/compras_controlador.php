<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\compras_cab;
use App\compras_det;
use App\repuesto;
use App\proveedor;
use App\local;
use App\saldo;
use App\familia;
use App\marcavehiculo;
use App\modelovehiculo;
use Session;

use Illuminate\Support\Facades\Auth;

class compras_controlador extends Controller
{

    public function dame_factura($id)
    {
//25310-3k090
//rmo24 58000
//gwm-52a
//llamar x cod_int

        $cabecera=compras_cab::find($id);
// id_factura_cab
        $items=compras_det::select('compras_det.cantidad','repuestos.cod_repuesto_proveedor','repuestos.descripcion','compras_det.pu','compras_det.costos as flete','compras_det.subtotal','compras_det.precio_sugerido')
            ->where('compras_det.id_factura_cab',$id)
            ->join('repuestos','compras_det.id_repuestos','repuestos.id')
            ->get();

        $suma_flete=0;
        foreach($items as $item){
            $suma_flete+=$item->cantidad*$item->flete;
        }

        $v=view('fragm.compras_listar_factura',compact('cabecera','items','suma_flete'))->render();
        return $v;

    }

    public function dame_facturas_por_proveedor($idproveedor)
    {


        $facturas=compras_cab::select('id','factura_fecha','factura_numero')
        ->where('factura_id_proveedor',$idproveedor)
        ->orderBy('factura_fecha','desc')
        ->get();
        $totalfacturas=$facturas->count();
        $v=view('fragm.facturas_por_proveedor',compact('facturas','totalfacturas'))->render();
        return $v;
    }

    private function dameultimoitem($id_factura)
    {

        $ultimo=compras_det::where('id_factura_cab',$id_factura)->latest()->value('item');
        if(is_null($ultimo))
        {
            $ultimo=0;
        }
        return $ultimo;
    }


    private function dame_items($id_factura)
    {
        $items=compras_det::select('compras_det.id',
                                    'compras_det.item',
                                    'repuestos.codigo_interno',
                                    'repuestos.descripcion',
                                    'marcavehiculos.marcanombre',
                                    'modelovehiculos.modelonombre',
                                    'repuestos.anios_vehiculo',
                                    'compras_det.cantidad',
                                    'compras_det.pu',
                                    'compras_det.subtotal',
                                    'compras_det.costos',
                                    'compras_det.precio_sugerido',
                                    'locales.local_nombre')
                                ->where('compras_det.id_factura_cab','=',$id_factura)
                                ->join('repuestos','compras_det.id_repuestos','repuestos.id')
                                ->join('marcavehiculos','repuestos.id_marca_vehiculo','marcavehiculos.idmarcavehiculo')
                                ->join('modelovehiculos','repuestos.id_modelo_vehiculo','modelovehiculos.id')
                                ->join('locales','compras_det.id_local','locales.id')
                                ->get();

        return $items;

    }

    private function damerepuestos($f,$m,$n)
    {
        $r=repuesto::where('id_familia',$f)
                    ->where('id_marca_vehiculo',$m)
                    ->where('id_modelo_vehiculo',$n)
                    ->orderByraw('substr(codigo_interno,1,3)')
                    ->get();
        return $r;
    }

    private function damerepuestosprov($codprov)
    {
        $r=repuesto::where('cod_repuesto_proveedor','LIKE','%'.$codprov.'%')
                    ->orWhere('version_vehiculo','LIKE','%'.$codprov.'%')
                    ->get();
        return $r;
    }

    private function damefamilias()
    {
        $f=familia::orderBy('nombrefamilia')->get();
        return $f;
    }

    private function damemarcas()
    {
        $m=marcavehiculo::where('activo','=',1)->select('idmarcavehiculo','marcanombre','urlfoto')->orderBy('marcanombre')->get();
        return $m;
    }

    private function damemodelos()
    {
        $m=modelovehiculo::where('activo','=',1)->get();
        return $m;
    }

    private function dameproveedores()
    {
        $p=proveedor::where('activo',1)
                    ->where('es_transportista',0)
                    ->orderBy('empresa_nombre_corto')
                    ->get();
        return $p;
    }

    private function damelocales()
    {
    	$l=local::all();
    	return $l;
    }

    public function dameporcentaje($id_familia)
    {

        $f=familia::find($id_familia);
        $porcentaje=$f->porcentaje;
        return $porcentaje;
    }

    public function crear()
    {

    	$proveedores=$this->dameproveedores();
    	$locales=$this->damelocales();
        $familias=$this->damefamilias();
        $marcas=$this->damemarcas();
        $modelos=$this->damemodelos();

    	//No envío los repuestos pues en la búsqueda del repuesto a ingresar,
    	//será con AJAX para no recargar mucho la página puesto que la cantidad
    	//de registros de repuestos es y será bastante.
    	return view('inventario.compras_ingreso',compact('proveedores','locales','familias','marcas','modelos'));
    }


    public function buscarepuestos(Request $r)
    {

        $fam=$r->idFa;
        $mar=$r->idMa;
        $mod=$r->idMo;

        $repuestos=$this->damerepuestos($fam,$mar,$mod);
        $vista=view('fragm.dame_repuesto',compact('repuestos'))->render();
        return $vista;
    }

    public function buscarepuestosprov($codprov)
    {
        //
        $repuestos=$this->damerepuestosprov($codprov);
        $vista=view('fragm.dame_repuesto',compact('repuestos'))->render();
        return $vista;
    }

    public function guardarcabecera(Request $r)
    {



        $cabecera=new compras_cab;
        $cabecera->factura_id_proveedor=$r->idproveedor;
        $cabecera->factura_numero=$r->numerofactura;
        $cabecera->factura_fecha=$r->fechafactura;
        $cabecera->factura_es_credito=($r->escredito=="true") ? 1:0; //checkbox

        if($cabecera->factura_es_credito==1)
        {
            $cabecera->factura_fecha_venc=$r->vencefactura;
        }else{
            $cabecera->factura_fecha_venc=null;
        }

        $cabecera->factura_subtotal=0.0;
        $cabecera->factura_iva=0.0;
        $cabecera->factura_total=0.0;
        $cabecera->factura_observaciones="";
        $cabecera->activo=1;
        $cabecera->usuarios_id=Auth::user()->id;

        try{
            $cabecera->save();
        }catch (\Exception $error){
            $debug=$error;
            $v=view('errors.debug_ajax',compact('debug'))->render();
            return $v;
        }



        $r=$cabecera->id;
        return $r;
    }

    public function guardaritem(Request $r)
    {



// Validaciones : https://styde.net/como-trabajar-con-form-requests-en-laravel/
        $reglas=array(
            'pu'=>'required',
            'cantidad'=>'required'


                    );

        $mensajes=array(
            'pu.required'=>'Debe elegir un repuesto',
            'cantidad.required'=>'Ingrese la cantidad'
                    );

        try{
            $this->validate($r,$reglas,$mensajes);
        }catch (\Exception $error){
            $errores=$error->validator->getMessageBag();
            $v=view('errors.validacion',compact('errores'))->render();
            return $v;
        }

        $detalle=new compras_det;
        $detalle->id_factura_cab=$r->idFactura;
        $item=$this->dameultimoitem($r->idFactura);
        $detalle->item=$item+1;
        $detalle->id_repuestos=$r->idrepuesto;
        $detalle->cantidad=$r->cantidad;
        $detalle->pu=$r->pu;
        $detalle->subtotal=$r->subtotalitem;
        $detalle->costos=$r->costos;
        $detalle->costos_descripcion=$r->costosdesc;
        $detalle->precio_sugerido=$r->preciosug;
        $detalle->id_local=$r->idLocal;
        $detalle->activo=1;
        $detalle->usuarios_id=Auth::user()->id;

        try{
            $detalle->save();
        }catch (\Exception $error){
            $debug=$error;
            $v=view('errors.debug_ajax',compact('debug'))->render();
            return $v;
        }

        // Actualizar tabla saldos considerando el local
        // id_repuestos,id_local,saldo,activo,usuarios_id
        // En ves de llamar un controlador desde otro controlador,
        // es mejor usar traits: https://www.php.net/manual/es/language.oop5.traits.php
        $r=new repuestocontrolador();
        $r->actualiza_saldos('I',$detalle->id_repuestos,$detalle->id_local,$detalle->cantidad);


        //Calculamos para actualizar cabecera de factura con subtotal, iva y total

        try{
            $subt=compras_det::where('id_factura_cab',$detalle->id_factura_cab)->sum('subtotal');
            $iva=$subt*Session::get('PARAM_IVA'); //parametro iva
            $tot=$subt+$iva;

            $cc=compras_cab::find($detalle->id_factura_cab);
            $cc->factura_subtotal=$subt;
            $cc->factura_iva=$iva;
            $cc->factura_total=$tot;
            $cc->save();




            }catch (\Exception $error){
                $debug=$error;
                $v=view('errors.debug_ajax',compact('debug'))->render();

                return $v;
            }

        $resp="Guardado Item ".$detalle->item." Puede agregar más Items si desea";

        return $resp;

    }

    public function dameitemsfactura($id_factura)
    {


        $items=$this->dame_items($id_factura);
        $st=0.0;

        foreach($items as $item)
        {
            $st=$st+$item->subtotal;
        }
        $iva=$st*Session::get('PARAM_IVA');
        $total=$st+$iva;
        $view=view('fragm.compras_items',compact('items','st','iva','total'))->render();
        return $view;
    }

    public function eliminaritem($id) //Es el id de la tabla compras_det, no es de la factura.
    {


        try{
            $c=compras_det::find($id);
            $operacion="E"; //Egreso
            $idrep=$c->id_repuestos;
            $idlocal=$c->id_local;
            $cantidad=$c->cantidad;

            $r=new repuestocontrolador();
            $r->actualiza_saldos($operacion,$idrep,$idlocal,$cantidad);


            compras_det::destroy($id);
            return "<strong>Item Eliminado...</strong>";

        }catch (\Exception $error){
            $debug=$error;
            $v=view('errors.debug_ajax',compact('debug'))->render();
            return $v;
        }

    }

    public function listar()
    {
        $proveedores=$this->dameproveedores();
        return view('inventario/compras_listar',compact('proveedores'));
    }

}
