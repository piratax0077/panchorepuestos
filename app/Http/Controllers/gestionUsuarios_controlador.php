<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\rol;
use App\permisos;
use App\role_has_permissions;
use App\users_has_permissions;

class gestionUsuarios_controlador extends Controller
{
    public function index(){

        $users = User::orderBy('name')->get();
        $roles = rol::all();
        // $opts = ["Ventas (Fact-Boleta)","Notas de crédito","Notas de débito","Facturas de Compra","Listar Facturas (Compras)","Cargar Folios","Estado de Envíos","Ambiente Certificación","RCOF Boletas","Libro Ventas","Libro Compras"];
        $permisos = permisos::all();
        $role_has_permissions = role_has_permissions::all();
        
        if(Auth::user()->rol->nombrerol == "Administrador"){
            return view('manten.usuarios_gestion_nueva',['users' => $users, 'roles' => $roles,'permisos' => $permisos,'role_has_permissions' => $role_has_permissions]);
        }else{
            return redirect('/home');
        }
        
        
    }

    public function saveUser(Request $request){

        $reglas=array(
            'rut'=>'required|max:10|unique:users,rut'
        );

        $mensajes=array(
            'rut.required'=>'Debe Ingresar un rut',
            'rut.max'=>'El rut debe tener como máximo 10 caracteres.',
            'rut.unique'=>'El rut ya existe.'
        );

        $name = $request->input('name');
        $rut = $request->input('rut');
        $telefono = $request->input('telefono');
        $email = $request->input('email');
        $password = $request->input('password');
        $image_path = $request->file('avatar');
        $role_id = $request->input('role');

        $password_encrypt = Hash::make($password);

        if($image_path){

            //Poner nombre único
            $image_path_name = time().$image_path->getClientOriginalName();
            // Guardar en la carpeta storage
            Storage::disk('users')->put($image_path_name, File::get($image_path));
        
        }

        $this->validate($request,$reglas,$mensajes);

        $user =  User::create([
            'name' => $name,
            'rut' => $rut,
            'telefono' => $telefono,
            'email' => $email,
            'password' => $password_encrypt,
            'image_path' => $image_path_name,
            'role_id' => $role_id
        ]);

        return redirect('/usuarios');
    }

    //Función pública para recuperar avatar del usuario

    public function getAvatar($filename){
        
        $file = Storage::disk('users')->get($filename);

        return new Response($file,200);
    }

    public function edit($id){
        $user = User::find($id);
        $roles = rol::all();
        return view('users.edit',['user' => $user, 'roles' => $roles]);
    }

    public function update(Request $request){
        $image_path = $request->file('avatar');
        
        if($image_path){

            //Poner nombre único
            $image_path_name = time().$image_path->getClientOriginalName();
            
            // Guardar en la carpeta storage
            Storage::disk('users')->put($image_path_name, File::get($image_path));
    
        }
        $name = $request->input('name');
        $rut = $request->input('rut');
        $telefono = $request->input('telefono');
        $email = $request->input('email');
        $userId = $request->input('userId');

        $user = User::find($userId);

        $user->name = $name;
        $user->rut = $rut;
        $user->telefono = $telefono;
        $user->email = $email;
        $user->image_path = $image_path_name;

        $user->save();

        return redirect('home')->with('status', 'Profile updated!');;
    }

    public function create(){
        $roles = rol::all();
        return view('users.register',['roles' => $roles]);
    }

    public function getUser($id){
        $user = User::find($id);
        $permisos = permisos::all();
        $rol = $user->rol->nombrerol;
        // variable donde se almacena los permisos de cada usuario
        $u_h_p = users_has_permissions::where('user_id',$user->id)->get();
        $values = [$user, $rol, $permisos,$u_h_p];
        return $values;
    }

    public function delete($id){
        $user = User::find($id);

        $user->delete();

        return $user->name;
    }

    public function cambioRol(Request $request){
        $user = User::find($request->user_id);

        $user->role_id = $request->role_id;

        $user->save();

        return ($user->rol);
    }

    public function userUp($id){
        $user = User::find($id);
        $user->activo = 1;
        $user->save();
        return $user->name;
    }

    public function userDown($id){
        $user = User::find($id);
        $user->activo = 0;
        $user->save();
        return $user->name;
    }

    public function agregarPermisos(Request $request){
        $values = $request->input("permiso");
        $user_id = $request->input("user_id");
         
            for($i=0; $i < count($values); $i++){
                try {
                    $nuevoPermiso = new users_has_permissions;
                    $nuevoPermiso->user_id = intval($user_id);
                    $nuevoPermiso->permission_id = intval($values[$i]);
                    $nuevoPermiso->save();
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
                
            }
         

        return redirect('/usuarios');
    }

    public function quitarPermisos(Request $request){
        $id = $request->id;
        $user_id = $request->user_id;

        try {
            $permiso_eliminar = users_has_permissions::where('user_id', $user_id)
                ->where('permission_id', $id)
                ->delete();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $permisos_del_usuario = users_has_permissions::where('user_id',$user_id)->get();

        return redirect('/usuarios');


    }
}
