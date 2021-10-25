<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\proveedor;
use Session;
use Debugbar;
use Illuminate\Support\Facades\Auth;

class proveedorcontrolador extends Controller
{

    private $proveedores;

    private function dameproveedores()
    {
        $p=proveedor::orderBy('empresa_nombre_corto')
                    ->get();

        return $p;
    }

    /**
     * Display a listing  of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $proveedores=$this->dameproveedores();
        return view('manten.proveedor',compact('proveedores'));
    }

    public function dame_transportistas(){ // para las ORDENES DE TRANSPORTE
        $t=proveedor::select('id','empresa_nombre_corto',)
                    ->where('activo',1)
                    ->where('es_transportista',1)
                    ->orderBy('empresa_nombre_corto')
                    ->get()
                    ->toJson();
        $respuesta=['estado'=>'OK','transportistas'=>$t];
        return $respuesta;
    }

    public function dame_proveedores(){ // para las ORDENES DE TRANSPORTE
        $p=proveedor::select('id','empresa_nombre_corto',)
                    ->where('activo',1)
                    ->where('es_transportista',0)
                    ->orderBy('empresa_nombre_corto')
                    ->get()
                    ->toJson();
        $respuesta=['estado'=>'OK','proveedores'=>$p];
        return $respuesta;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('manten.proveedor_crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if(isset($request->btnGuardarProveedor))
        {
            $reglas=array(
                'empresa_codigo'=>'required|max:20|unique:proveedores,empresa_codigo',
                'empresa_nombre'=>'required|max:100',
                'empresa_nombre_corto'=>'required|max:10',
                'empresa_direccion'=>'required|max:150',
                'empresa_web'=>'required|max:30',
                'empresa_telefono'=>'required|max:20',
                'empresa_correo'=>'required|max:30',
                'vendedor_nombres'=>'required|max:50',
                'vendedor_correo'=>'required|max:30',
                'vendedor_telefono'=>'required|max:20'
            );

            $mensajes=array(
                'empresa_codigo.required'=>'Falta RUT del Proveedor',
                'empresa_codigo.max'=>'Máximo 100 caracteres',
                'empresa_codigo.unique'=>'Código de Proveedor ya existe',
                'empresa_nombre.required'=>'Falta Nombre del Proveedor.',
                'empresa_nombre.max'=>'Nombre del Proveedor como máximo 100 caracteres.',
                'empresa_nombre.required'=>'Falta Nombre de la Empresa',
                'empresa_nombre_corto.max'=>'Nombre Corto del Proveedor como máximo 10 caracteres.',
                'empresa_nombre_corto.required'=>'Falta Nombre Corto del Proveedor',
                'empresa_direccion.max'=>'Dirección como máximo 150 caracteres.',
                'empresa_direccion.required'=>'Falta Dirección del Proveedor.',
                'empresa_web.max'=>'Web del Proveedor como máximo 30 caracteres.',
                'empresa_web.required'=>'Falta Web del Proveedor.',
                'empresa_telefono.required'=>'Falta teléfono del proveedor.',
                'empresa_correo.max'=>'Correo del Proveedor como máximo 30 caracteres.',
                'empresa_correo.required'=>'Falta Correo del Proveedor.',
                'vendedor_nombres.required'=>'Falta nombres del vendedor.',
                'vendedor_nombres.max'=>'Nombres del vendedor como máximo 50 caracteres.',
                'vendedor_telefono.required'=>'Falta teléfono del vendedor.',
                'vendedor_correo.required'=>'Falta Correo del vendedor.'
            );

            $this->validate($request,$reglas,$mensajes);

            $proveedor=new proveedor;
            $proveedor->empresa_codigo=$request->empresa_codigo;
            $proveedor->empresa_nombre=$request->empresa_nombre;
            $proveedor->empresa_nombre_corto=$request->empresa_nombre_corto;
            $proveedor->empresa_direccion=$request->empresa_direccion;
            $proveedor->empresa_web=$request->empresa_web;
            $proveedor->empresa_telefono=$request->empresa_telefono;
            $proveedor->empresa_correo=$request->empresa_correo;
            $proveedor->vendedor_nombres=$request->vendedor_nombres;
            $proveedor->vendedor_correo=$request->vendedor_correo;
            $proveedor->vendedor_telefono=$request->vendedor_telefono;
            $proveedor->es_transportista=($request->empresa_transportista=="on") ? 1 : 0 ;
            $proveedor->activo=1;
            $proveedor->usuarios_id=Auth::user()->id;

            $proveedor->save();
            $proveedores=$this->dameproveedores();
            return view('manten.proveedor',compact('proveedores'))->with('msgGuardado','Proveedor Guardado.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        proveedor::destroy($id);
        $proveedores=$this->dameproveedores();
        return view('manten.proveedor',compact('proveedores'))->with('msgGuardado','Proveedor Eliminado.');
    }
}
