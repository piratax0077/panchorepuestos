<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\familia;
use Illuminate\Support\Facades\Auth;
use Session;

class familiacontrolador extends Controller
{
    private $familias;


    private function validaSesion()
    {
        //Valida sesión: Revisar Handler.php en app\Exception, método render()
        // repuestos/Exceptions/Handler.php
        abort_if(Auth::user()->rol->nombrerol !== "Administrador", 403);
    }


    private function damefamilias()
    {
        $f=familia::orderBy('nombrefamilia')->get();
        return $f;
    }

    public function dame_familias()
    {
        //$this->validaSesion();
        return $this->damefamilias()->toJson();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $this->validaSesion();

        $familias=$this->damefamilias();
        return view('manten.familia',compact('familias'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return "create";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $this->validaSesion();

        if(isset($request->btnGuardarFamilia))
        {
            if($request->modificando==0){ //es nuevo
                $reglas=array(
                    'nombrefamilia'=>'required|max:50|unique:familias,nombrefamilia',
                    'porcentaje'=>'required|min:1|max:99|numeric',
                    'porcentaje_flete'=>'required|min:1|max:99|numeric',
                    'prefijo'=>'required|max:4|alpha_num|unique:familias,prefijo'
                );
            }else{
                $reglas=array(
                    'nombrefamilia'=>'required|max:50',
                    'porcentaje'=>'required|min:1|max:99|numeric',
                    'porcentaje_flete'=>'required|min:1|max:99|numeric'
                );
            }


            $mensajes=array(
                'nombrefamilia.required'=>'Debe Ingresar un nombre de familia',
                'nombrefamilia.max'=>'El nombre de la familia debe tener como máximo 50 caracteres.',
                'nombrefamilia.unique'=>'El nombre de la familia ya existe.',
                'porcentaje.required'=>'Falta Ingresar el Porcentaje',
                'porcentaje.min'=>'El porcentaje debe ser mayor a 0.',
                'porcentaje.max'=>'El porcentaje debe ser menor a 100.',
                'porcentaje.numeric'=>'El porcentaje debe ser un número entero.',
                'porcentaje_flete.required'=>'Falta Ingresar el Porcentaje del Flete',
                'porcentaje_flete.min'=>'El porcentaje del Flete debe ser mayor a 0.',
                'porcentaje_flete.max'=>'El porcentaje del Flete debe ser menor a 100.',
                'porcentaje_flete.numeric'=>'El porcentaje del Flete debe ser un número entero.',
                'prefijo.required'=>'Debe ingresar el prefijo.',
                'prefijo.alpha_num'=>'Debe ingresar combinación de texto y números.',
                'prefijo.max'=>'El prefijo debe tener hasta 4 caracteres.',
                'prefijo.unique'=>'El :attribute ya existe.'
            );

            $this->validate($request,$reglas,$mensajes);

            if($request->modificando==1){
                $familia=familia::find($request->id_familia);
                $msg="Familia Modificada: ";
            }else{
                $msg="Familia Guardada: ";
                $familia=new familia;
            }
            $familia->nombrefamilia=$request->nombrefamilia;
            $familia->porcentaje=$request->porcentaje;
            $familia->porcentaje_flete=$request->porcentaje_flete;
            if($request->modificando==0){ //es nuevo
                $familia->prefijo=strtoupper($request->prefijo);
            }
            $familia->save();
            $familias=$this->damefamilias();
            if($request->donde=="familia")
                    return view('manten.familia',compact('familias'))->with('msgGuardado',$msg.$familia->nombrefamilia.' ('.$familia->id.')');
            if($request->donde=="factuprodu") return "OK";
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
        return "show";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return "edit";
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
        return "update";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->validaSesion();

        familia::destroy($id);
        $familias=$this->damefamilias();
            return view('manten.familia',compact('familias'))->with('msgGuardado','Familia Eliminada.');
    }
}
