<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Debugbar;
use Session;
use Auth;
use App\User;
use App\parametro;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->cargar_parametros_sistema();
        return view('home');
    }

    public function dame_sesion()
    {
       return Session::all();
    }

    public function autenticado(){
        return Auth::check();
    }

    public function dame_clave(Request $r){
        if($r->clave=='pan831'){
            return "OK";
        }else{
            return "NEGATIVO";
        }
    }

    public function form_cambiar_clave()
    {
        return view('auth.passwords.cambiar')->with('mensaje','');
    }

    public function cambiar_clave(Request $r)
    {
        $reglas=['antiguaclave'=>'required|password',
                        'nuevaclave'=>'required|min:5',
                       'repetirnuevaclave'=>'same:nuevaclave'
                    ];

        $mensajes=['antiguaclave.required'=>'Ingrese su clave anterior',
                            'antiguaclave.password'=>'No es la clave actual',
                            'nuevaclave.required'=>'Ingrese su nueva clave',
                            'nuevaclave.min'=>'Ingrese mínimo 5 caracteres',
                            'repetirnuevaclave.same'=>'Nueva clave vacia o no coincide'
        ];

        $this->validate($r,$reglas,$mensajes);

        User::find(auth()->user()->id)->update(['password'=> Hash::make($r->nuevaclave)]);

        return view('auth.passwords.cambiar')->with('mensaje','Cambio de Clave Correcto...');
    }

    private function cargar_parametros_sistema()
    {
        $user = Auth::user();
        Session::put('usuario_id',$user->id);
        Session::put('local',1); //FALTA: tabla locales id = 1; El usuario debe estar "amarrado" a un local,modificar despues.
        Session::put('acceso','SI');
        Session::put('usuario_nombre',$user->name);
        $email=Auth::user()->email;
        Session::put('usuario_email',$email);
        if($email=='josefranciscott@gmail.com' || $email=='jesus@gmail.com'){
            Session::put('rol','S');
        }else if($email=='llancor.ltda@gmail.com'){
            Session::put('rol','C');
        }else if($email=='maralfa14@gmail.com' || $email=='gsus@gsus.cl'){
            Session::put('rol','J');
        }else{
            Session::put('rol','Z');
        }


        Session::put('token',Auth::user()->token);
        $parametros=parametro::select('codigo','valor')->get();
        foreach($parametros as $p)
        {
            Session::put($p->codigo,$p->valor);
        }
    }
    
}
