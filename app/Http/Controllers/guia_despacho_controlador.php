<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Debugbar;
use Session;
use App\cliente_modelo;
use App\proveedor;
use App\cotizacion;
use App\cotizacion_detalle;
use App\correlativo;
use App\guia_de_despacho;
use App\guia_de_despacho_detalle;
use App\servicios_sii\ClsSii;
use App\servicios_sii\FirmaElectronica;
use App\servicios_sii\Auto;
use App\servicios_sii\Sii;

use Illuminate\Support\Facades\Auth;

class guia_despacho_controlador extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('ventas.guia_despacho');
    }

    public function traspaso_mercaderia(){
        return view('ventas.traspaso_mercaderia');
    }

    public function dame_cotizacion_num($num_cotizacion)
    {
        try {
            $id_cotizacion=cotizacion::where('num_cotizacion',$num_cotizacion)
                                    ->value('id');
            $cotizacion=cotizacion_detalle::select('repuestos.descripcion','cotizaciones_detalle.cantidad','cotizaciones_detalle.precio_venta','cotizaciones_detalle.subtotal','cotizaciones_detalle.descuento')
                                            ->join('repuestos','cotizaciones_detalle.id_repuestos','repuestos.id')
                                            ->where('id_cotizacion',$id_cotizacion)->get();
            if($cotizacion->count()==0){
                $estado=['estado'=>'ERROR','mensaje'=>'No existe cotización '+$num_cotizacion];
            }else{
                $estado=['estado'=>'OK','cotizacion'=>$cotizacion];
            }

        } catch (\Exception $e) {
            $estado=['estado'=>'ERROR','mensaje'=>$e->getMessage()];
        }
        return json_encode($estado);
    }

    public function dame_cliente($rut){

        //Buscar en Proveedores
        $rut_proveedor=str_replace(".","",$rut);
        $rut_proveedor=str_replace("-","",$rut_proveedor);
        $rut_proveedor=substr($rut_proveedor,0,strlen($rut_proveedor)-1)."-".substr($rut_proveedor,strlen($rut_proveedor)-1);
        $a="nada";
        if(strlen($rut_proveedor)==9){
            //9811490-4
            $rut_proveedor=substr($rut_proveedor,0,1).".".substr($rut_proveedor,1,3).".".substr($rut_proveedor,4,3).substr($rut_proveedor,7);
        }elseif(strlen($rut_proveedor)==10){
            //26775605-8
            $rut_proveedor=substr($rut_proveedor,0,2).".".substr($rut_proveedor,2,3).".".substr($rut_proveedor,5,3).substr($rut_proveedor,8);
        }

        $p=proveedor::where('empresa_codigo',$rut_proveedor)
                        ->where('activo',1)
                        ->first();

        //Buscar en Clientes
        $rut_cliente=str_replace(".","",$rut);
        $rut_cliente=str_replace("-","",$rut_cliente);

        $c=cliente_modelo::where('rut',$rut_cliente)
                            ->where('activo',1)
                            ->first();

        $r=0;
        if(is_null($p) && is_null($c)){ //no hay nada

        }
        if(!is_null($p) && is_null($c)){ //hay solo proveedor
            $r=1;
        }
        if(is_null($p) && !is_null($c)){ //hay solo cliente
            $r=2;
        }
        if(!is_null($p) && !is_null($c)){ //hay ambos
            $r=3;
        }
        $estado['status']=$r;
        $estado['cliente']=$c;
        $estado['proveedor']=$p;
        return json_encode($estado);
    }

    public function cargar_documento($doc)
    {
        $tip_doc=substr($doc,0,2); //bo
        $num_doc=trim(substr($doc,2)); //el número buscado

        if($tip_doc=='bo')
        {
            $documento="Boleta";
            $num_documento=$num_doc;
            //Buscar si no se emitió nota de crédito para este documento
            $hay=nota_de_debito::where('docum_referencia','bo'.$num_documento)->first();
            if(!is_null($hay))
            {
                $h=$hay->toArray();
                return "La boleta N° ".$num_doc." ya tiene nota de débito N° <b>".$h['num_nota_debito']."</b> por un valor de ".$h['total']." de fecha ".Carbon::parse($h['fecha_emision'])->format('d-m-Y')." motivo: ".$h['motivo_correccion'];
            }

            $buscado_doc=boleta::where('num_boleta',$num_doc)->first();
            if(!is_null($buscado_doc))
            {
                $buscado_doc=$buscado_doc->toArray();
                $id_documento=$buscado_doc['id'];
                $fecha_documento=Carbon::parse($buscado_doc['fecha_emision'])->format('d-m-Y');
                $cliente=cliente_modelo::where('id',$buscado_doc['id_cliente'])->first()->toArray();

                $cliente_id=$cliente['id'];
                $cliente_rut=$cliente['rut'];
                $cliente_razon_social=$cliente['razon_social'];
                if(substr($cliente_rut,0,5)=='00000')
                {
                    $cliente_id="0";
                    $cliente_rut="Sin Cliente";
                    $cliente_razon_social="";
                }

                $cliente_direccion=$cliente['direccion']."      Comuna: ".$cliente['direccion_comuna']."     Ciudad: ".$cliente['direccion_ciudad'];
                $detalle=boleta_detalle::select('boletas_detalle.*','repuestos.codigo_interno','repuestos.descripcion')
                                                        ->where('boletas_detalle.id_boleta',$buscado_doc['id'])
                                                        ->join('repuestos','boletas_detalle.id_repuestos','repuestos.id')
                                                        ->get();


                //dd($buscado_doc);
                //return $buscado_doc['fecha_emision'];
                $v = view('fragm.nota_debito_boleta',
                    compact('documento','id_documento',
                                    'num_documento',
                                    'fecha_documento',
                                    'cliente_id',
                                    'cliente_rut',
                                    'cliente_razon_social',
                                    'cliente_direccion',
                                    'detalle'
                                    ))->render();
                return $v;

            }else{
                return "No Existe la Boleta N° ".$num_doc;
            }
        }

        if($tip_doc=='fa')
        {
            $documento="Factura";
            $num_documento=$num_doc;
            //Buscar si no se emitió nota de crédito para este documento
            $hay=nota_de_debito::where('docum_referencia','fa'.$num_documento)->first();
            if(!is_null($hay))
            {
                $h=$hay->toArray();
                return "La factura N° ".$num_doc." ya tiene nota de débito N° <b>".$h['num_nota_debito']."</b> por un valor de ".$h['total']." de fecha ".Carbon::parse($h['fecha_emision'])->format('d-m-Y')." motivo: ".$h['motivo_correccion'];
            }

            $buscado_doc=factura::where('num_factura',$num_doc)->first();
            if(!is_null($buscado_doc))
            {
                $buscado_doc=$buscado_doc->toArray();
                $id_documento=$buscado_doc['id'];
                $fecha_documento=Carbon::parse($buscado_doc['fecha_emision'])->format('d-m-Y');
                $cliente=cliente_modelo::where('id',$buscado_doc['id_cliente'])->first()->toArray();

                $cliente_id=$cliente['id'];
                $cliente_rut=$cliente['rut'];
                $cliente_razon_social=$cliente['razon_social'];
                if(substr($cliente_rut,0,5)=='00000')
                {
                    $cliente_id="0";
                    $cliente_rut="Sin Cliente";
                    $cliente_razon_social="";
                }

                $cliente_direccion=$cliente['direccion']."      Comuna: ".$cliente['direccion_comuna']."     Ciudad: ".$cliente['direccion_ciudad'];
                $detalle=factura_detalle::select('facturas_detalle.*','repuestos.codigo_interno','repuestos.descripcion')
                                                        ->where('facturas_detalle.id_factura',$buscado_doc['id'])
                                                        ->join('repuestos','facturas_detalle.id_repuestos','repuestos.id')
                                                        ->get();


                //dd($buscado_doc);
                //return $buscado_doc['fecha_emision'];
                $v = view('fragm.nota_debito_factura',
                    compact('documento','id_documento',
                                    'num_documento',
                                    'fecha_documento',
                                    'cliente_id',
                                    'cliente_rut',
                                    'cliente_razon_social',
                                    'cliente_direccion',
                                    'detalle'
                                    ))->render();
                return $v;

            }else{
                return "No Existe la Factura N° ".$num_doc;
            }
        }

    }

    private function dame_correlativo()
    {

        $tipo_dte='52';
        $num=-1;
        $id_local = Session::get("local"); // es el local donde se ejecuta el terminal

        $fila=correlativo::where('id_local', $id_local)
                                    ->where('tipo_dte_sii', $tipo_dte)
                                    ->first();
        if(!is_null($fila))
        {
            $corr=$fila->correlativo;
            $max_folio=$fila->hasta;
            if($max_folio>=($corr+1)) $num=$corr;
        }
        return $num;
    }

    private function actualizar_correlativo($num)
    {
        $co = correlativo::where('tipo_dte_sii', '52')
            ->where('id_local', Session::get('local'))
            ->first();
        $co->correlativo = $num;
        $co->save();
    }

    private function dame_cliente_0()
    {
        $rpta=-1; //No esta definido el cliente 0000000
        $c0=cliente_modelo::where('rut','LIKE','00000%')->first();
        if(!is_null($c0))
        {
            $rpta=$c0->id;
        }
        return $rpta;
    }

    public function generar_xml(Request $r)
    {
        $Datos['tipo_despacho']=$r->tipo_despacho;
        $Datos['tipo_traslado']=$r->tipo_traslado;

        $ref1=json_decode($r->ref1);
        $ref2=json_decode($r->ref2);
        $ref3=json_decode($r->ref3);

        $referencias=[];
        if(count($ref1)>0){
            array_push($referencias,$ref1);
        }
        if(count($ref2)>0){
            array_push($referencias,$ref2);
        }
        if(count($ref3)>0){
            array_push($referencias,$ref3);
        }

        $Datos['referencias']=$referencias;

        $datos=json_decode($r -> input('datos'));
        if(count($datos)==0){
            $estado=['estado'=>'ERROR','mensaje'=>'No se enviaron datos'];
            return json_encode($estado);
        }

        $Detalle=[];
        foreach($datos as $i){
            /*
            $item=array('CdgItem'=>['TpoCodigo'=>'INT1','VlrCodigo'=>$i->id_repuestos],
                                    'NmbItem'=>$i->descripcion,
                                    'QtyItem'=>$i->cantidad,
                                    'PrcItem'=>$i->pu_neto);
            */
            $precio_neto=round($i->precio/(1+Session::get('PARAM_IVA')),2);
            $item=array('NmbItem'=>$i->descripcion,
                        'QtyItem'=>$i->cantidad,
                        'PrcItem'=>$precio_neto);
            array_push($Detalle,$item);

        }

        //Obtener cliente
        $cliente=cliente_modelo::find($r->id_cliente);
        $rutCliente_con_guion=substr($cliente->rut,0,strlen($cliente->rut)-1)."-".substr($cliente->rut,strlen($cliente->rut)-1);

        if($cliente->tipo_cliente==0){ //persona natural
            $rz=$cliente->nombres." ".$cliente->apellidos;
        }
        if($cliente->tipo_cliente==1){ //empresa
            $rz=$cliente->razon_social;
        }

        $Receptor=['RUTRecep'=>$rutCliente_con_guion,
                'RznSocRecep'=>$rz,
                'GiroRecep'=>$cliente->giro,
                'DirRecep'=>$cliente->direccion,
                'CmnaRecep'=>$cliente->direccion_comuna,
                'CiudadRecep'=>$cliente->direccion_ciudad
            ];


        $transporte=[
                'Patente'=>$r->patente,
                'RUTTrans'=>$r->rut_transportista,
                'Chofer'=>[
                    'RUTChofer'=>$r->rut_chofer,
                    'NombreChofer'=>$r->nombre_chofer,
                ],
                'DirDest'=>$r->cliente_direccion,
                'CmnaDest'=>$r->comuna,
                'CiudadDest'=>$r->ciudad
        ];
        $Datos['transporte']=$transporte;

        $nume=$this->dame_correlativo();
        if($nume<0) //Se acabó el correlativo autorizado por SII
        {
            $estado=['estado'=>'ERROR_CAF','mensaje'=>"Guía de Despacho: No hay correlativo autorizado por SII. Descargar nuevo CAF"];
            return json_encode($estado);
        }else{
            $nume++;
            $Datos['folio_dte']=$nume;
        }

        $Datos['tipo_dte']='52';

        $estado=ClsSii::generar_xml($Receptor,$Detalle,$Datos); //devuelve array

        if($estado['estado']=='GENERADO'){
            Session::put('xml',$Datos['tipo_dte']."_".$Datos['folio_dte'].".xml");
            Session::put('tipo_dte',$Datos['tipo_dte']);
            Session::put('tipo_dte_nombre','Nota de Débito'); //OJO: Para que se necesita?
            Session::put('folio_dte',$Datos['folio_dte']);
            Session::put('idcliente', $r->id_cliente);
        }else{
            Session::put('xml',0);
            Session::put('tipo_dte',0);
            Session::put('tipo_dte_nombre','');
            Session::put('folio_dte',0);
            Session::put('idcliente',0);
        }

        return json_encode($estado);
    } // fin generar_xml

    public function enviar_sii(Request $r)
    {
        $id_cliente=$r->id_cliente;

        $d=Session::get('xml');
        if($d==0 )
        {
            $estado=['estado'=>'ERROR_XML','mensaje'=>'No se encuentra el XML generado.'];
            return json_encode($estado);
        }

        $RutEnvia = str_replace(".","",Session::get('PARAM_RUT'));
        $RutEmisor = $RutEnvia;

        $tipo_dte=Session::get('tipo_dte');
        $doc=base_path().'/xml/generados/guias_de_despacho/'.$d;

        $tipo_docu="nada";
        $num_docu=0;

       //Recuperar el XML Generado para enviar
        try {
            $envio=file_get_contents($doc);
            $rs=ClsSii::enviar_sii($RutEnvia,$RutEmisor,$envio); //recibe un array asoc, si OK, trackID es $estado['trackid']
            if($rs['estado']=='OK'){
                $resultado_envio=$rs['mensaje'];
                $xml=new \SimpleXMLElement($envio, LIBXML_COMPACT);
                $estado=0;
                $TrackID=$rs['trackid'];
                $estado_sii='RECIBIDO';
            }else{
                return json_encode($rs);
            }
            //guardar guia de despacho

            $gd=new guia_de_despacho;
            $gd->num_guia_despacho=strval($xml->SetDTE->DTE->Documento->Encabezado->IdDoc->Folio);
            $gd->fecha_emision=strval($xml->SetDTE->DTE->Documento->Encabezado->IdDoc->FchEmis);
            $gd->TipoDespacho=strval($xml->SetDTE->DTE->Documento->Encabezado->IdDoc->TipoDespacho);
            $gd->IndTraslado=strval($xml->SetDTE->DTE->Documento->Encabezado->IdDoc->IndTraslado);
            $gd->TpoTranVenta=strval($xml->SetDTE->DTE->Documento->Encabezado->IdDoc->TpoTranVenta);
            $gd->id_cliente=$id_cliente;
            $gd->neto=intval($xml->SetDTE->DTE->Documento->Encabezado->Totales->MntNeto);
            $gd->exento=0.0;
            $gd->iva=intval($xml->SetDTE->DTE->Documento->Encabezado->Totales->IVA);
            $gd->total=intval($xml->SetDTE->DTE->Documento->Encabezado->Totales->MntTotal); //incluye el iva
            $gd->patente=strval($xml->SetDTE->DTE->Documento->Encabezado->Transporte->Patente);
            $gd->RUTTrans=strval($xml->SetDTE->DTE->Documento->Encabezado->Transporte->RUTTrans);
            $gd->RUTChofer=strval($xml->SetDTE->DTE->Documento->Encabezado->Transporte->Chofer->RUTChofer);
            $gd->NombreChofer=strval($xml->SetDTE->DTE->Documento->Encabezado->Transporte->Chofer->NombreChofer);
            $gd->DirDest=strval($xml->SetDTE->DTE->Documento->Encabezado->Transporte->DirDest);
            $gd->CmnaDest=strval($xml->SetDTE->DTE->Documento->Encabezado->Transporte->CmnaDest);
            $gd->CiudadDest=strval($xml->SetDTE->DTE->Documento->Encabezado->Transporte->CiudadDest);
            $gd->trackid=$TrackID;
            $gd->url_xml=$d;
            $gd->estado = $estado;
            $gd->estado_sii=$estado_sii;
            $gd->resultado_envio=$resultado_envio;

            $gd->activo=1;
            $gd->usuarios_id=Auth::user()->id;
            $gd->save();

            //detalle guia despacho

            foreach($xml->SetDTE->DTE->Documento->Detalle as $Det){
                $pu=round(intval($Det->PrcItem)*(1+Session::get('PARAM_IVA')),2);
                $total_item=round(intval($Det->MontoItem)*(1+Session::get('PARAM_IVA')),2);
                $num_item_xml=$Det->NroLinDet;
                $gdd=new guia_de_despacho_detalle;
                $gdd->id_guia_despacho=$gd->id;
                $gdd->id_repuestos=0;
                $gdd->id_unidad_venta=0;
                $gdd->id_local=Session::get('local');
                $gdd->precio_venta=round(intval($Det->PrcItem)*(1+Session::get('PARAM_IVA')),2);
                $gdd->cantidad=intval($Det->QtyItem);
                $gdd->subtotal=$total_item;
                $gdd->descuento=0;
                $gdd->total=$gdd->subtotal-$gdd->descuento;
                $gdd->activo=1;
                $gdd->usuarios_id=Auth::user()->id;
                $gdd->save();

                //actualizar saldos FALTA: Poner en la GUI y Traer el codigo del repuesto para poder actualizar el inventario
                //$rc = new repuestocontrolador();
                //$rc->actualiza_saldos("E", $gdd->id_repuestos, $gdd->id_local, $gdd->cantidad);
            }
            $this->actualizar_correlativo($gd->num_guia_despacho);

        } catch (\Exception $e) {
            $ee=substr($e->getMessage(),0,300);
            $estado=['estado'=>'ERROR','mensaje'=>$ee];
            return json_encode($estado);
        }

        return json_encode($rs);
    } // fin enviar SII

    public function actualizar_estado(Request $r){
        //viene TrackID, estado
        $gd=guia_de_despacho::where('trackid',$r->TrackID)->first();
        if(!is_null($gd)){
            $gd->estado_sii=$r->estado;
            $gd->save();
            $estado=['estado'=>'OK','mensaje'=>'Estado actualizado...'];
        }else{
            $estado=['estado'=>'ERROR','mensaje'=>'No se pudo actualizar estado'];
        }
        return json_encode($estado);
    }

    public function existe_nc($nc){
        $hay=nota_de_debito::where('docum_referencia','LIKE','nc*'.$nc.'%')->first();
        if(is_null($hay)){
            $estado=['estado'=>'NO','mensaje'=>'No existe...'];
        }else{
            $estado=['estado'=>'SI','mensaje'=>'La Nota de Crédito N° '.$nc.' YA TIENE Nota de Débito N° '.$hay->num_nota_debito.' de fecha '.$hay->fecha_emision];
        }
        return json_encode($estado);
    }
}
