<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage; //lo agregué
use Illuminate\Support\Facades\File; //lo agregué
use App\repuesto;
use App\saldo;
use App\familia;
use App\local;
use App\marcavehiculo;
use App\modelovehiculo;
use App\marcarepuesto;
use App\proveedor;
use App\similar;
use App\repuestofoto;
use App\pais;
use App\oem;
use App\fabricante;

use Illuminate\Support\Facades\Auth;

use Session;
use Debugbar;

class repuestocontrolador extends Controller
{

    private $repuestos;
    private $familias;
    private $marcas;
    private $modelos;
    private $marcarepuestos;
    private $proveedores;
    private $similares;

    public function dame_repuesto_x_cod_int($codint){
        $hay=repuesto::where('codigo_interno',$codint)->first();
        if(is_null($hay)){ //No hay
            $estado=['estado'=>'ERROR','mensaje'=>'El código '.$codint. ' no existe o está desactivado'];
        }else{
            $repuesto=$this->dame_un_repuesto($hay->id);
            $estado=['estado'=>'OK','repuesto'=>$repuesto];
        }

        return json_encode($estado);
    }

    private function dame_un_repuesto($id)
    {
        $repuesto=repuesto::select('repuestos.id',
            'repuestos.id_familia',
            'repuestos.id_marca_repuesto',
            'repuestos.id_pais',
            'repuestos.descripcion',
            'repuestos.observaciones',
            'repuestos.medidas',
            'repuestos.cod_repuesto_proveedor',
            'repuestos.stock_minimo',
            'repuestos.stock_maximo',
            'repuestos.codigo_barras'
            )
        ->where('repuestos.id',$id)
        ->where('repuestos.activo',1)
        ->join('familias','repuestos.id_familia','familias.id')
        ->join('marcarepuestos','repuestos.id_marca_repuesto','marcarepuestos.id')
        ->join('proveedores','repuestos.id_proveedor','proveedores.id')
        ->join('paises','repuestos.id_pais','paises.id')
        ->get();

        return $repuesto;
    }

    private function damesimilares($id_repuesto)
    {
        $s=similar::select('marcavehiculos.marcanombre','modelovehiculos.modelonombre','modelovehiculos.zofri','similares.id','similares.anios_vehiculo')
        ->where('similares.id_repuestos',$id_repuesto)
        ->where('similares.activo',1)
        ->join('marcavehiculos','similares.id_marca_vehiculo','marcavehiculos.idmarcavehiculo')
        ->join('modelovehiculos','similares.id_modelo_vehiculo','modelovehiculos.id')
        ->orderBy('similares.id','DESC')
        ->get();
        return $s;
    }

    private function damefotosrepuesto($id_repuesto)
    {
        $rf=repuestofoto::select('urlfoto')
        ->where('id_repuestos',$id_repuesto)
        ->where('activo',1)
        ->get();
        return $rf;
    }

    private function damerepuestos($f)
    {
        $r=repuesto::where('id_familia',$f)
                    ->where('repuestos.activo',1)
                    ->orderBy('id','desc')
                    ->get();
        return $r;
        //->orderByraw('substr(codigo_interno,1,3)')
    }



    private function damefamilias()
    {
        //$f=familia::orderBy('nombrefamilia')->get();
        try{
            $s="SELECT repuestos.id_familia, familias.id, familias.nombrefamilia,COUNT(repuestos.id_familia) as total FROM repuestos inner join familias on repuestos.id_familia=familias.id  GROUP by repuestos.id_familia order by familias.nombrefamilia";
            $familias=\DB::select($s);
            return $familias;

        }catch (\Exception $error){
            $debug=$error;
            $v=view('errors.debug_ajax',compact('debug'))->render();
            return $v;
        }

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

    private function damemarcarepuestos()
    {
        $m=marcarepuesto::orderBy('marcarepuesto')->get();
        return $m;
    }

    private function dameproveedores()
    {
        $p=proveedor::orderBy('empresa_nombre')->get();
        return $p;
    }

    private function damepaises()
    {
        $p=pais::orderBy('nombre_pais')->get();
        return $p;
    }



/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {


        $repuestos=$this->damerepuestos(0,0,0);
        $familias=$this->damefamilias();
        $marcas=$this->damemarcas();
        $modelos=$this->damemodelos();
        $marcarepuestos=$this->damemarcarepuestos();
        $proveedores=$this->dameproveedores();
        $paises=$this->damepaises();

        $datos=array('id_repuesto'=>'',
                          'familia'=>'',
                          'marcavehiculo'=>'',
                          'modelovehiculo'=>'',
                          'marcarepuesto'=>'',
                          'proveedor'=>'',
                          'pais'=>'',
                          'descripcion'=>'',
                          'medidas'=>'',
                          'anios_vehiculo'=>'',
                          'cod_repuesto_proveedor'=>'',
                          'precio_compra'=>'',
                          'precio_venta'=>'',
                          'stock_minimo'=>'',
                          'stock_maximo'=>'',
                          'codigo_barras'=>''
            );
        $guardado="NO";
        return view('manten.repuestos_crear',compact('guardado','repuestos','familias','marcas','modelos','marcarepuestos','proveedores','paises','datos'));
    }


    public function datos(Request $request)
    {


        if(isset($request->btnGuardarRepuesto))
        {
            $stockmin=$request->stock_minimo+1;
            $preciocompra=$request->precio_compra+1;
            $familia_datos=familia::find($request->cboFamilia);
            $newval=$familia_datos->correlativo+1;
            $codinterno=$familia_datos->prefijo.$newval;
            $reglas=array(
                'descripcion'=>'required|max:50',
                'medidas'=>'required|max:500',
                'anios_vehiculo'=>'required|max:50',
                'cod_repuesto_proveedor'=>'required|max:50',
                'precio_compra'=>'required|numeric',
                'precio_venta'=>'required|numeric|min:'.$preciocompra.'\'',
                'stock_minimo'=>'required|integer',
                'stock_maximo'=>'required|integer'
            );

            //'stock_maximo'=>'required|integer|min:'.$stockmin.'\''
            //FALTA PONER LOS MENSAJES DE ERROR
            $mensajes=array(
                'nombrefamilia.required'=>'Debe Ingresar un nombre de familia',
                'nombrefamilia.max'=>'El nombre de la familia debe tener como máximo 30 caracteres.',
                'nombrefamilia.unique'=>'El nombre de la familia ya existe.',
                'porcentaje.required'=>'Falta Ingresar el Porcentaje',
                'porcentaje.min'=>'El porcentaje debe ser mayor a 0.',
                'porcentaje.max'=>'El porcentaje debe ser menor a 100.',
                'porcentaje.numeric'=>'El porcentaje debe ser un número entero.',
                'prefijo.required'=>'Debe ingresar el prefijo.',
                'prefijo.alpha'=>'El prefijo debe ser caracteres.',
                'prefijo.size'=>'El prefijo debe tener 3 caracteres.'
            );


            $this->validate($request,$reglas);
            //$this->validate($request,$reglas,$mensajes);


            $repuesto=new repuesto;
            $repuesto->codigo_interno=$codinterno;
            $repuesto->descripcion=$request->descripcion;
            $repuesto->medidas=$request->medidas;
            $repuesto->anios_vehiculo=$request->anios_vehiculo;
            $repuesto->version_vehiculo="---";
            $repuesto->cod_repuesto_proveedor=$request->cod_repuesto_proveedor;
            $repuesto->version_vehiculo=$request->version_vehiculo; // Es el cod rep2
            $repuesto->codigo_OEM_repuesto="---";
            $repuesto->precio_compra=$request->precio_compra;
            $repuesto->precio_venta=$request->precio_venta;
            $repuesto->stock_minimo=$request->stock_minimo;
            $repuesto->stock_maximo=$request->stock_maximo;
            $repuesto->codigo_barras=$request->codigo_barras;
            //$repuesto->id_unidad_venta=$request->cboUnidadVenta; // Implementar...
            $repuesto->id_familia=$request->cboFamilia;
            $repuesto->id_marca_vehiculo=$request->cboMarca;
            $repuesto->id_modelo_vehiculo=$request->cboModelo;
            $repuesto->id_marca_repuesto=$request->cboMarcaRepuesto;
            $repuesto->id_proveedor=$request->cboProveedor;
            $repuesto->id_pais=$request->cboPais;
            $repuesto->usuarios_id=Auth::user()->id;
            $repuesto->activo=1;
            $repuesto->save();

            //Luego de guardar, actualizar el correlativo de la familia
            $familia_datos->correlativo=$newval;
            $familia_datos->save();


            //Preparamos los datos para enviar a los controles de los datos ya guardados
            //y mostrarlos desactivados

            $familia=familia::find($repuesto->id_familia);
            $marcavehiculo=marcavehiculo::find($repuesto->id_marca_vehiculo);
            $modelovehiculo=modelovehiculo::find($repuesto->id_modelo_vehiculo);
            $marcarepuesto=marcarepuesto::find($repuesto->id_marca_repuesto);
            $proveedor=proveedor::find($repuesto->id_proveedor);
            $pais=pais::find($repuesto->id_pais);

            $datos=array('guardado'=>'SI',
                          'id_repuesto'=>$repuesto->id,
                          'familia'=>$familia->nombrefamilia,
                          'marcavehiculo'=>$marcavehiculo->marcanombre,
                          'modelovehiculo'=>$modelovehiculo->modelonombre,
                          'marcarepuesto'=>$marcarepuesto->marcarepuesto,
                          'proveedor'=>$proveedor->empresa_nombre,
                          'pais'=>$pais->nombre_pais,
                          'descripcion'=>$repuesto->descripcion,
                          'medidas'=>$repuesto->medidas,
                          'anios_vehiculo'=>$repuesto->anios_vehiculo,
                          'cod_repuesto_proveedor'=>$repuesto->cod_repuesto_proveedor,
                          'version_vehiculo'=>$repuesto->version_vehiculo,
                          'precio_compra'=>$repuesto->precio_compra,
                          'precio_venta'=>$repuesto->precio_venta,
                          'stock_minimo'=>$repuesto->stock_minimo,
                          'stock_maximo'=>$repuesto->stock_maximo,
                          'codigo_barras'=>$repuesto->codigo_barras
            );

            Session::put('datos',$datos);

            return redirect('repuesto/crea_fotos');
            //return view('manten.repuestos_crear',compact('guardado','datos','familias','marcas','modelos','marcarepuestos','proveedores'))->with('msgGuardado','Agregue Fotos');
        }



    }

    public function guardar_repuesto_modificado(Request $item)
    {

        $resp=-1;
        try{
            $repuesto=repuesto::find($item->idrep);
            $id_familia_old=$repuesto->id_familia;
            $repuesto->id_familia=$item->idFamilia;
            $repuesto->id_marca_repuesto=$item->idMarcaRepuesto;
            $repuesto->id_pais=$item->idPais;
            $repuesto->descripcion=strtoupper($item->descripcion);
            $repuesto->observaciones=strlen(trim($item->observaciones))>0?trim($item->observaciones):"";
            $repuesto->medidas=$item->medidas;
            $repuesto->cod_repuesto_proveedor=$item->cod_repuesto_proveedor;
            $repuesto->version_vehiculo="---";
            $repuesto->codigo_OEM_repuesto='modificado';
            $repuesto->precio_compra=$item->pu;
            $repuesto->precio_venta=$item->preciosug;
            $repuesto->stock_minimo=$item->stockmin;
            $repuesto->stock_maximo=$item->stockmax;
            $repuesto->codigo_barras=$item->codbar;
            $repuesto->usuarios_id=Auth::user()->id;
            $repuesto->activo=$item->activo;

            //Verificamos: si cambió de familia entonces le asignamos nuevo codigo_interno
            $modificóFamilia=false;
            if($id_familia_old!=$item->idFamilia)
            {
                $familia_datos=familia::find($item->idFamilia);
                $newval=$familia_datos->correlativo+1;
                $codinterno=$familia_datos->prefijo.$newval;
                $repuesto->codigo_interno=$codinterno;
                $modificóFamilia=true;
            }

            $s=$repuesto->save();
            if($s)
            {
                $resp=$repuesto->id;
                //Actualizar el correlativo de la familia si se modificó familia
                if($modificóFamilia)
                {
                    $familia_datos->correlativo=$newval;
                    $familia_datos->save();
                    $resp=$repuesto->codigo_interno;
                }

            }
            return $resp;

        }catch (\Exception $error){
            $debug=$error;
            $v=view('errors.debug_ajax',compact('debug'))->render();
            return $v;
        }

    }

    public function guardar_precio_venta($dato)
    {
        $rpta="XUXA";
        try{
            $d=explode("&",$dato);
            $idrep=$d[0];
            $nuevo_precio=$d[1];
            $rep=repuesto::find($idrep);
            $rep->precio_venta=$nuevo_precio;
            $rep->save();
            $rpta=number_format($nuevo_precio,0,',','.');
        }catch (\Exception $error){
            $debug=$error;
            $v=view('errors.debug_ajax',compact('debug'))->render();
            return $v;
        }
        return $rpta;
    }

    public function crea_fotos()
    {


        $datos=Session::get('datos');
        $fotos=Session::get('fotos');

        return view('manten.repuestos_agregar_fotos',compact('datos','fotos'));

    }




    public function fotos(Request $request)
    {


        $idrepuestos=$request->id_repuesto;
        $elusuarioID=Auth::user()->id;

        if(isset($request->btnGuardarFotos))
        {
            $reglas=array(
                'archivo'=>'required|max:200|mimes:jpeg,png' //maximo 200 kilobytes
            );

            $mensajes=array(
                'archivo.required'=>'Debe elegir una imagen.',
                'archivo.mimes'=>'El tipo de archivo debe ser una imagen jpg o png.',
                'archivo.max'=>'El tamaño de archivo no debe ser mayor a 200Kb.'
            );

            $this->validate($request,$reglas,$mensajes);



            $archivo=$request->file('archivo');
            $repuestofoto= new repuestofoto;
            $repuestofoto->urlfoto=$archivo->store('fotozzz','public');
            $repuestofoto->usuarios_id=$elusuarioID;
            $repuestofoto->id_repuestos=$idrepuestos;
            $repuestofoto->activo=1;

            $repuestofoto->save();


            $fotos=repuestofoto::where('id_repuestos','=',$idrepuestos)->get();



            return redirect('repuesto/crea_fotos')->with('fotos',$fotos);

            //return view('manten.repuestos_agregar_similares',compact('datos'));

            //return view('manten.repuestos_crear',compact('guardado','datos','familias','marcas','modelos','marcarepuestos','proveedores','fotos'))->with('msgGuardado','Foto Agregada.');
        }

        if(isset($request->btnAgregarSimilares))
        {


            $datos=Session::get('datos');
            $idrepuestos=$datos['id_repuesto'];

            $fotos=repuestofoto::select('urlfoto')->where('id_repuestos','=',$idrepuestos)->get()->toArray();
            Session::put('fotos',$fotos);


            return redirect('repuesto/crea_similares')->with('fotos',$fotos);
        }

    }

    public function crea_similares()
    {

        $datos=Session::get('datos');
        $fotos=Session::get('fotos');
        $marcas=$this->damemarcas();
        $modelos=$this->damemodelos();
        $similares=$this->damesimilares($datos['id_repuesto']);
        return view('manten.repuestos_agregar_similares',compact('datos','fotos','similares','marcas','modelos'));
    }


public function similares(Request $request)
    {


        $idrepuestos=$request->id_repuesto;
        $elusuarioID=Auth::user()->id;
        $repuesto=repuesto::find($idrepuestos);
        if(isset($request->btnGuardarSimilar))
        {

            $reglas=array(
                'anios_vehiculo_sim'=>'required|max:20'
            );

            $mensajes=array(
                'anios_vehiculo_sim.required'=>'Falta años.',
                'anios_vehiculo_sim.max'=>'Máximo 20 caracteres.'
            );

            $this->validate($request,$reglas,$mensajes);

            $similar=new similar;
            $similar->codigo_OEM_repuesto="---";
            $similar->anios_vehiculo=$request->anios_vehiculo_sim;
            $similar->activo=1;
            $similar->id_repuestos=$idrepuestos;
            $similar->id_marca_vehiculo=$request->cboMarcaSim;
            $similar->id_modelo_vehiculo=$request->cboModeloSim;
            $similar->usuarios_id=$elusuarioID;
            $similar->save();

            $similares=$this->damesimilares($idrepuestos);
            $fotos=repuestofoto::where('id_repuestos','=',$idrepuestos)->get();

            return redirect('repuesto/crea_similares')->with('similares',$similares)->with('fotos',$fotos);
            //return redirect('repuesto/crea_similares')->with('datos',$datos)->with('fotos',$fotos)->with('similares',$similares);
         }

    }

    public function actualizar_anio_similares($dato){
        list($id_similar,$anio_nuevo)=explode("_",$dato);
        $similar=similar::where('id',$id_similar)->update(['anios_vehiculo'=>$anio_nuevo]);
        if($similar>0){
            return "OK";
        }else{
            return "NO";
        }
    }

    //Viene de repuestos_agregar_similares.blade AJAX function agregar_OEM()
    public function oems(Request $r)
    {


        //Comprobar que no esté repetido con validaciones


        $oem=new oem;
        $oem->codigo_oem=$r->cod_OEM;
        $oem->id_repuestos=$r->id_repuesto;
        $oem->usuarios_id=Auth::user()->id;
        $oem->activo=1;

        try{
            $oem->save();
        }catch (\Exception $error){
            $debug=$error;
            $v=view('errors.debug_ajax',compact('debug'))->render();
            return $v;
        }


        //devolver vista oems
        $oems=$this->dame_oems($r->id_repuesto);


        return $oems;

    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() //Listar repuestos
    {


        $familias=$this->damefamilias();
        $proveedores=$this->dameproveedores();
        return view('manten.repuestos',compact('familias','proveedores'));
    }

    public function buscarepuestos(Request $r)
    {

        $fam=$r->idFa;
        $repuestos=$this->damerepuestos($fam);
        $vista=view('fragm.buscarepuesto',compact('repuestos'))->render();
        return $vista;
    }

    public function buscar(){
        return view('manten.repuesto_buscar');
    }

    public function dame_repuestos_x_proveedor($id_prov)
    {
        try{

            /*
            $repuestos=repuesto::where('repuestos.id_proveedor',$id_prov)
            ->join('familias','repuestos.id_familia','familias.id')
            ->join('marcarepuestos','repuestos.id_marca_repuesto','marcarepuestos.id')
            ->join('proveedores','repuestos.id_proveedor','proveedores.id')
            ->join('paises','repuestos.id_pais','paises.id')
            ->get();
            */
            $repuestos=repuesto::where('repuestos.id_proveedor',$id_prov)
                        ->where('repuestos.activo',1)->paginate(20);
            //$vista=view('fragm.buscarepuesto',compact('repuestos'))->render();
            //return $vista;
            $provv=$id_prov;
            $familias=$this->damefamilias();
            $proveedores=$this->dameproveedores();
            return view('manten.repuestos',compact('familias','proveedores','repuestos'))->with('id_prov',$provv)->render();

        }catch (\Exception $error){
            $debug=$error;
            $v=view('errors.debug_ajax',compact('debug'))->render();
            return $v;
        }
    }

    public function buscar_por_codigo($dato)
    {

        return 'hola';
        
        $quien=substr($dato,0,1);
        $codigo=substr($dato,1);
        if($quien=='1')
            $repuestos=repuesto::where('repuestos.codigo_interno',$codigo)
                    ->join('proveedores','repuestos.id_proveedor','proveedores.id')
                    ->select('proveedores.empresa_nombre','repuestos.*')
                    ->get();

        if($quien=='2')
        {
            $repuestos = repuesto::where('repuestos.cod_repuesto_proveedor', $codigo)
                ->join('proveedores', 'repuestos.id_proveedor', 'proveedores.id')
                ->select('proveedores.empresa_nombre','repuestos.*')
                ->get();
        }


        if($repuestos->count()>0)
        {
            return $repuestos->toJson();
        }else{
            return "-1";
        }


    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // el codigo_interno procesarlo aquí según el prefijo y correlativo de la familia


        if(isset($request->btnGuardarRepuesto))
        {
            $stockmin=$request->stock_minimo+1;
            $preciocompra=$request->precio_compra+1;
            $familia_datos=familia::find($request->cboFamilia);
            $newval=$familia_datos->correlativo+1;
            $codinterno=$familia_datos->prefijo.$newval;
            $reglas=array(
                'descripcion'=>'required|max:50',
                'medidas'=>'required|max:500',
                'anios_vehiculo'=>'required|max:50',
                'version_vehiculo'=>'required|max:20',
                'cod_repuesto_proveedor'=>'required|max:50',
                'codigo_OEM_repuesto'=>'required|max:50',
                'precio_compra'=>'required|numeric',
                'precio_venta'=>'required|numeric|min:'.$preciocompra.'\'',
                'stock_minimo'=>'required|integer',
                'stock_maximo'=>'required|integer'



            );

            //'stock_maximo'=>'required|integer|min:'.$stockmin.'\''
            //FALTA PONER LOS MENSAJES DE ERROR
            $mensajes=array(
                'nombrefamilia.required'=>'Debe Ingresar un nombre de familia',
                'nombrefamilia.max'=>'El nombre de la familia debe tener como máximo 30 caracteres.',
                'nombrefamilia.unique'=>'El nombre de la familia ya existe.',
                'porcentaje.required'=>'Falta Ingresar el Porcentaje',
                'porcentaje.min'=>'El porcentaje debe ser mayor a 0.',
                'porcentaje.max'=>'El porcentaje debe ser menor a 100.',
                'porcentaje.numeric'=>'El porcentaje debe ser un número entero.',
                'prefijo.required'=>'Debe ingresar el prefijo.',
                'prefijo.alpha'=>'El prefijo debe ser caracteres.',
                'prefijo.size'=>'El prefijo debe tener 3 caracteres.'
            );


            $this->validate($request,$reglas);
            //$this->validate($request,$reglas,$mensajes) ;


            $repuesto=new repuesto;
            $repuesto->codigo_interno=$codinterno;
            $repuesto->descripcion=$request->descripcion;
            $repuesto->medidas=$request->medidas;
            $repuesto->anios_vehiculo=$request->anios_vehiculo;
            $repuesto->version_vehiculo=$request->version_vehiculo;
            $repuesto->cod_repuesto_proveedor=$request->cod_repuesto_proveedor;
            $repuesto->codigo_OEM_repuesto=$request->codigo_OEM_repuesto;
            $repuesto->precio_compra=$request->precio_compra;
            $repuesto->precio_venta=$request->precio_venta;
            $repuesto->stock_minimo=$request->stock_minimo;
            $repuesto->stock_maximo=$request->stock_maximo;
            $repuesto->codigo_barras=$request->codigo_barras;
            //$repuesto->id_unidad_venta=$request->cboUnidadVenta; // Implementar...
            $repuesto->id_familia=$request->cboFamilia;
            $repuesto->id_marca_vehiculo=$request->cboMarca;
            $repuesto->id_modelo_vehiculo=$request->cboModelo;
            $repuesto->id_marca_repuesto=$request->cboMarcaRepuesto;
            $repuesto->id_proveedor=$request->cboProveedor;
            $repuesto->id_pais=$request->cboPais;
            $repuesto->usuarios_id=Auth::user()->id; ;
            $repuesto->activo=1;
            $repuesto->save();

            //Luego de guardar, falta actualizar el correlativo de la familia
            $repuestos=$this->damerepuestos();
            $familias=$this->damefamilias();
            $marcas=$this->damemarcas();
            $modelos=$this->damemodelos();
            $marcarepuestos=$this->damemarcarepuestos();
            $proveedores=$this->dameproveedores();
            $paises=$this->damepaises();

            return view('manten.repuestos_crear',compact('codinterno','repuestos','familias','marcas','modelos','marcarepuestos','proveedores','paises'))->with('msgGuardado','Repuesto Guardado '.'('.$codinterno.")");

        }

    }

    public function guardar_xpress(Request $r){
        //llega idcliente, codigo, descripcion, precio
        //desde ventas_principal.blade function agregar_repuesto_xpress
        
        try{
            
            $repuesto=new repuesto;
            $fsd=familia::where('prefijo','FSD')->value('id');
            $repuesto->id_familia=$fsd;
            $msd=marcarepuesto::where('marcarepuesto','SIN DEFINIR')->value('id');
            $repuesto->id_marca_repuesto=$msd;
            $psd=proveedor::where('empresa_codigo','13.412.179-3')->value('id'); //pancho
            $repuesto->id_proveedor=$psd;
            $ppsd=pais::where('nombre_pais','SIN DEFINIR')->value('id');
            $repuesto->id_pais=$ppsd;
            $repuesto->descripcion=strtoupper($r->descripcion);
            $repuesto->medidas="No definidas";
            
            if(empty($r->codigo)){
                $repuesto->cod_repuesto_proveedor="P".time();
            }else{
                $repuesto->cod_repuesto_proveedor=$r->codigo;
            }
            
            $repuesto->version_vehiculo="---"; // $item->cod2_repuesto_proveedor;
            $repuesto->codigo_OEM_repuesto="XPRESS";
            $repuesto->precio_compra=0;
            $repuesto->precio_venta=$r->precio;
            $repuesto->pu_neto=round($repuesto->precio_venta/(1+Session::get('PARAM_IVA')),2);
            $repuesto->stock_minimo=3;
            $repuesto->stock_maximo=10;
            $repuesto->stock_actual=20;
            $repuesto->codigo_barras=0;

            

            $familia_datos=familia::find($fsd);
            $newval=$familia_datos->correlativo+1;
            $codinterno=$familia_datos->prefijo.$newval;
            $repuesto->codigo_interno=$codinterno;

            

            $repuesto->usuarios_id=Auth::user()->id;
            $repuesto->activo=1;

           
        try {
            $repuesto->save();
                    //Luego de guardar, actualizar el correlativo de la familia
                    $familia_datos->correlativo=$newval;
                    $familia_datos->save();

                    return $repuesto->id;
        } catch (\Exception $error) {
            return $error;
        }
            

        }catch (\Exception $error){
            $debug=$error;
            $v=view('errors.debug_ajax',compact('debug'))->render();

            return $v;
        }
    }

    public function actualiza_saldos($operacion,$idrep,$idlocal,$cantidad)
    {


        try{
            $b=saldo::where('id_repuestos',$idrep)
                                ->where('id_local',$idlocal)
                                ->first();
            //En caso de que sea la primera vez que se agrega a saldos.
            if(is_null($b))
            {
                $sald=new saldo;
                $sald->id_repuestos=$idrep;
                $sald->id_local=$idlocal;
                $sald->saldo=$cantidad;
                $sald->activo=1;
                $sald->usuarios_id=Auth::user()->id;
                $sald->save();
            }else{
                //Actualiza saldos por local
                switch ($operacion)
                {
                    case "I":  //Ingresos
                        $b->saldo=$b->saldo+$cantidad;

                    break;
                    case "E": //Egresos
                        $dif=$b->saldo-$cantidad;
                        if($dif<0) $dif=0; //prevenir saldos negativos
                        $b->saldo=$dif;
                    break;
                    default:
                }
                $b->save();
            }



            //Actualizamos saldos en repuestos (sumatoria de locales)
            $sumita=saldo::where('id_repuestos',$idrep)
                                    ->sum('saldo');
            $r=repuesto::find($idrep);
            $r->stock_actual=$sumita;
            $r->save();
        }catch (\Exception $error){
            $debug=$error;
            $v=view('errors.debug_ajax',compact('debug'))->render();
            return $v;
        }
    }

    /**
     * Era el método show, le cambié de nombre
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function dame_datos_repuesto($id)
    {


        $repuesto=$this->dame_un_repuesto($id);
        //Devuelve la vista renderizada para que la petición
        //AJAX en repuestos.blade.php pueda recibirlo y
        //mostrarlo.

        $view=view('fragm.repuesto_datos',compact('repuesto'))->render();
        return $view;
    }

    public function dame_similares($id)
    {


        $similares=$this->damesimilares($id);
      //dd($similares->toJson());
      $view=view('fragm.repuesto_similares',compact('similares'))->render();
      return $view;
      //return $similares->toJSON();
    }

    public function dame_similares_modificar($id_repuesto)
    {
        $similares=$this->damesimilares($id_repuesto);
        /*
        $similares=similar::select('marcavehiculos.marcanombre','modelovehiculos.modelonombre','similares.anios_vehiculo','similares.id')
                            ->where('similares.id_repuestos',$id_repuesto)
                            ->where('similares.activo',1)
                            ->join('marcavehiculos','similares.id_marca_vehiculo','marcavehiculos.idmarcavehiculo')
                            ->join('modelovehiculos','similares.id_modelo_vehiculo','modelovehiculos.id')
                            ->orderBy('similares.id','DESC')
                            ->get();
        */
        $v=view('fragm.factuprodu_similares',compact('similares'))->render();
        return $v;

    }

    public function fotos_repuesto($id)
    {

        $fotos=$this->damefotosrepuesto($id);
        $view=view('fragm.repuesto_fotos',compact('fotos'))->render();
        return $view;
    }

    public function dame_fotos_modificar($id_repuesto)
    {
        $fotos=repuestofoto::select('id','urlfoto')
                    ->where('id_repuestos',$id_repuesto)
                    ->where('activo',1)
                    ->orderBy('id','DESC')
                    ->get();
        $v=view('fragm.factuprodu_fotos',compact('fotos'))->render();
        return $v;
    }

    public function dame_oems($id)
    {

        $oems=oem::where('id_repuestos',$id)->get();
        $v=view('fragm.repuesto_oems',compact('oems'))->render();
        return $v;
    }

    public function dame_oems_modificar($id_repuesto)
    {

        $oems=oem::select('id','codigo_oem')
                    ->where('activo',1)
                    ->where('id_repuestos',$id_repuesto)
                    ->orderBy('id','DESC')
                    ->get();
        $v=view('fragm.factuprodu_oems',compact('oems'))->render();
        return $v;
    }

    public function dame_fabricantes($id)
    {

        $fabs=fabricante::select('codigo_fab','marcarepuesto')
                                    ->where('repuestos_fabricantes.id_repuestos',$id)
                                    ->join('marcarepuestos','repuestos_fabricantes.id_marcarepuestos','marcarepuestos.id')
                                    ->get();
        $v=view('fragm.repuesto_fabs',compact('fabs'))->render();
        return $v;
    }

    public function dame_fabricantes_modificar($id_repuesto)
    {

        $fabs=fabricante::select('repuestos_fabricantes.id','repuestos_fabricantes.codigo_fab','marcarepuestos.marcarepuesto')
                            ->join('marcarepuestos','repuestos_fabricantes.id_marcarepuestos','marcarepuestos.id')
                            ->where('repuestos_fabricantes.activo',1)
                            ->where('repuestos_fabricantes.id_repuestos',$id_repuesto)
                            ->orderBy('repuestos_fabricantes.id','DESC')
                            ->get();
        $v=view('fragm.factuprodu_fabs',compact('fabs'))->render();
        return $v;
    }

    private function damelocales()
    {
    	$l=local::all();
    	return $l;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
    	$locales=$this->damelocales();
        $marcas=$this->damemarcas();
        $repuesto=Collect();
        return view('manten.repuestos_modificar',compact('locales','marcas','repuesto'));
    }

    public function editar($id_repuesto)
    {
    	$locales=$this->damelocales();
        $marcas=$this->damemarcas();
        $repuesto=repuesto::find($id_repuesto);
        return view('manten.repuestos_modificar',compact('locales','marcas','repuesto'));
    }

    public function cambiaprecio($id,$precio_compra,$precio_venta)
    {

        $r=repuesto::find($id);
        $r->precio_compra=$precio_compra;
        $r->precio_venta=$precio_venta;
        $r->save();
        return "OK";
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        /* Borrar los hijos
        similares y fotos

        */
        similar::where('id_repuestos',$id)->delete();
        repuestofoto::where('id_repuestos',$id)->delete();
        repuesto::destroy($id);

        $familias=$this->damefamilias();
        $marcas=$this->damemarcas();
        $modelos=$this->damemodelos();
        $paises=$this->damepaises();
        return view('manten.repuestos',compact('familias','marcas','modelos','paises'))->with('msgGuardado','Repuesto Eliminado...');


        //return redirect()->action('repuestocontrolador@index');

        //return view('fragm.mensajes')->with('msgGuardado','DESTROY ID: '.$id);
    }
}
