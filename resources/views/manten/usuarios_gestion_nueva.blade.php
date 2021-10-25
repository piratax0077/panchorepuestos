@extends('plantillas.usuarios')
@section('titulo','Usuarios')
@section('contenido')

    <script>
        function verUsuario(evt){
            evt.preventDefault();
            $('ul li a').on('click',function(event){
                console.log(event);
                let id = this.id;
                let url = '/usuarios/user/'+id;
                $.ajax({
                    type:'get',
                    url: url,
                    beforeSend: function(){
                        $('#rolesUser').html('<p>Buscando... </p>');
                    },
                    success: function(data){
                        
                        let urlImage = "usuarios/avatar/"+data[0].image_path;
                        let permisos = data[2];
                        let u_h_p = data[3];
                        var user = data[0];
                        
                        $('#rolesUser').empty();
                        $('#rolesUser').append("<img src='"+urlImage+"' alt='' class='imgUserRol'>");
                        $('#rolesUser').append("<label class='lbl-info-user' for='nameUser'>Nombre de usuario: </label>");
                        $('#rolesUser').append('<p>'+user.name+' </p>');
                        $('#rolesUser').append("<label class='lbl-info-user' for='cargoUser'>Cargo: </label>");
                        $('#rolesUser').append("<p id='roleName'>"+data[1]+" </p>");
                        $('#rolesUser').append("<label class='lbl-info-user' for='emailUser'>Email: </label>");
                        $('#rolesUser').append("<p id='roleEmail'>"+user.email+" </p>");
                        $('#rolesUser').append("<label class='lbl-info-user' for='rutUser'>Rut: </label>");
                        $('#rolesUser').append("<p id='roleRut'>"+user.rut+" </p>");
                        $('#rolesUser').append("<label class='lbl-info-user' for='telefonoUser'>Telefono: </label>");
                        $('#rolesUser').append("<p id='roleTelefono'>"+user.telefono+" </p>");
                        $('#rolesUser').append("<label class='lbl-info-user' for='estadoUser'>Estado: </label>");
                        if(user.activo === 1){
                            $('#rolesUser').append("<p id='roleEstado'>Activo </p>");
                        }else{
                            $('#rolesUser').append("<p id='roleEstado'>Inactivo </p>");
                        }
                        $('#rolesUser').append("<button class='btn btn-warning' data-toggle='modal' data-target='#exampleModal"+user.id+"'>Cambiar rol </button>");
                        
                        $('#btnActivar').attr({'href':'usuarios/up/'+user.id});
                        $('#btnActivar').removeClass('disabled');
                        $('#btnDesactivar').attr('href','usuarios/down/'+user.id);
                        $('#btnDesactivar').removeClass('disabled');
                        $('#btnEliminar').attr('href','usuarios/delete/'+user.id);
                        $('#btnEliminar').removeClass('disabled');

                        $('#listado-permisos').empty();

                        
                        
                        permisos.forEach(permiso => {
                            
                            u_h_p.forEach(data => {
                                
                                if(permiso.id === data.permission_id){
                                    $('#listado-permisos').append(`
                                 <table class='table'>
                                    <thead class='thead-dark'> 
                                        <tr> <th>Permiso </th> <th>Eliminar </th> </tr>
                                    </thead>
                                    <tbody>
                                        <tr> 
                                            <td>`+permiso.name+` </td>
                                            <td class='text-center'><button class='btn btn-danger btn-xs' onclick='eliminarPermiso(`+permiso.id+`,`+user.id+`)' style='border-radius: 100px'> x </button> </td>
                                        </tr>
                                    </tbody>
                                     
                                     
                                 </table>`);
                                 
                                }
                            })
                            
                            
                        });
                        
                        $('#listado-permisos').append("<button class='btn btn-success btn-xs' data-toggle='modal' data-target='#agregarPermisosModal"+user.id+"' disabled>Agregar permisos </button>")
                        
                    },
                    error: function(err){
                        console.log(err);
                    },
                    complete: function(){
                        console.log('Completada');
                    }
                });
            });
            
        }

        function cambiarRolUser(user){
            
            let idNuevoRol = $('input[name="flexRadioDefault"]:checked').val();
            console.log(idNuevoRol);
            let data = {'user_id': user.id, 'role_id': idNuevoRol};
            let url = '/usuarios/cambiar-rol';
    
            $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });

            $.ajax({
                type:'POST',
                url:url,
                data: data,
                success: function(data){
                    console.log(data);
                    $('#roleName').empty();
                    $('#roleName').append(data.nombrerol);
                    Vue.swal({
                        title: 'Felicidades',
                        text: "Rol actualizado",
                        icon: 'success',
                    });
                },
                error: function(err){
                            Vue.swal({
                                title:'Error!',
                                text:err.responseText,
                                icon:'error'
                            });
                }
            })
            
        }

        function activarUsuario(event){
            event.preventDefault();
            let url = $('#btnActivar').attr('href');
            console.log(url);
            // Con substring extraigo el id del usuario que quiero activar
            let id = url.substring(12,14);
            $.ajax({
                type:'get',
                url: url,
                data: id,
                success: function(data){
                    $('#userDesactive').empty();
                    $('#userDesactive').removeClass('info-estado-usuario-inactivo');
                    $('#userDesactive').append('<span id="userActive">Activo</span>');
                    $('#userDesactive').addClass('info-estado-usuario-activo');
                    $('#roleEstado').empty();
                    $('#roleEstado').append("<h3 id='roleEstado'>Activo </h3>");
                    Vue.swal({
                        title: 'Felicidades',
                        text: "Usuario "+data+" activado con éxito",
                        icon: 'success',
                    });
                }
            });
        }

        function desactivarUsuario(event){
            event.preventDefault();
            let url = $('#btnDesactivar').attr('href');
            console.log(url);
            // Con substring extraigo el id del usuario que quiero activar
            let id = url.substring(12,14);
            $.ajax({
                type:'get',
                url: url,
                data: id,
                success: function(data){
                    $('#userActive').empty();
                    $('#userActive').removeClass('info-estado-usuario-activo');
                    $('#userActive').append('<span id="userDesactive">Inactivo</span>');
                    $('#userActive').addClass('info-estado-usuario-inactivo');
                    $('#roleEstado').empty();
                    $('#roleEstado').append("<h3 id='roleEstado'>Inactivo </h3>");
                    Vue.swal({
                        title: 'Felicidades',
                        text: "Usuario "+data+" desactivado con éxito",
                        icon: 'success',
                    });
                }
            });
        }

        function eliminarUsuario(event){
            event.preventDefault();
            Vue.swal({
                title: '¿Esta seguro de eliminar al usuario?',
                text: "¡No podrá revertir esta acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminalo!'
                }).then((result) => {
                if (result.isConfirmed) {
                    
                    let url = $('#btnEliminar').attr('href');
                    let id = url.substring(16,19);
                    $.ajax({
                        type:'get',
                        url: url,
                        data: id,
                        success: function(data){
                            console.log(data);
                            Vue.swal({
                                title:'Eliminado!',
                                text:'El usuario'+data.name+ 'ha sido eliminado',
                                icon:'success'
                            });
                        },
                        error: function(e){
                            Vue.swal({
                                title:'Error!',
                                text:e.responseText,
                                icon:'error'
                            })
                        }
                    });
                    setTimeout(function(){ window.location.href = '/usuarios'; },3000);
                }else{
                    console.log('No eliminado');
                }
                });
            
        }

        function editarUsuario(id){
            alert(id);
        }

        function eliminarPermiso(id,user_id){

            Vue.swal({
                title: '¿Esta seguro de eliminar el permiso del usuario?',
                text: "¡Precaución!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminalo!'
                }).then((result) => {
                if (result.isConfirmed) {
                    
                    let data = {'id':id,'user_id': user_id};
                    let url = "/usuarios/permisos/delete";

                    $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                    });
            
                    $.ajax({
                        type:'post',
                        data: data,
                        url: url,
                        success: function(response){
                            Vue.swal({
                                title:'Eliminado!',
                                text:'El permiso ha sido eliminado',
                                icon:'success'
                            });
                            setTimeout(function(){ window.location.href = '/usuarios'; },3000);
                        },
                        error: function(err){
                            console.log(err);
                        }
                    })
                        }else{
                            console.log('No eliminado');
                        }
                        });
            
           
        }

    </script>

    <h2><center>GESTIÓN DE USUARIOS</center></h2>
    <div class="row" style="width: 100%;">
        <div class="col-md-4">
            <h3>Listado de usuarios</h3>
            <div class="button_group">
                <a href="/usuarios/crear" class="btn btn-primary">Registrar</a>
                <a class="btn btn-success disabled" id="btnActivar" href="" onclick="activarUsuario(event)"  >Activar</a>
                <a class="btn btn-warning disabled" id="btnDesactivar" href="" onclick="desactivarUsuario(event)" >Desactivar</a>
                <a class="btn btn-danger disabled" id="btnEliminar" href="" onclick="eliminarUsuario(event)" >Borrar</a>
            </div>
            <ul>
                @foreach($users as $user)
                @if ($user->id !== Auth::user()->id && $user->rol->nombrerol !== "Administrador") 
                
                <li class="mb-3 mt-3" ><img src="{{url('usuarios/avatar/'.$user->image_path)}}" alt="" class="logoInicio"> 
                    <a href="" class="ml-4" style="text-decoration: none !important;" id="{{$user->id}}" onclick="verUsuario(event)">
                        <span style="font-size: 16px; ">{{$user->name}}</span>
                    </a>
                    <a class="btn btn-xs btn-warning" style="float: right; margin-top: 25px;" href="{{url('usuarios/edit/'.$user->id)}}"><i class="fas fa-edit"></i></a>
                </li> 
                @endif
                  
                @endforeach
            </ul>
        
        </div>
        <div class="col-md-4">
            <h3>Roles de usuario</h3>
            <div id="rolesUser">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th>Cod.</th>
                            <th>Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $rol)
                        <tr>
                            <th>{{$rol->id}}</th>
                            <th>{{$rol->nombrerol}}</th>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <h3>Listado de Permisos</h3>
            <div id="listado-permisos">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th>Cod.</th>
                            <th>Permiso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permisos as $permiso)
                        <tr>
                            <th>{{$permiso->id}}</th>
                            <th>{{$permiso->name}}</th>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
 
    <!-- Modal -->
    @foreach ($users as $user )
    <div class="modal fade" id="exampleModal{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">{{$user->name}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="" method="post">
            <div class="modal-body">
              @foreach ($roles as $rol )
              <div class="form-check">
                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault{{$rol->id}}" value="{{$rol->id}}">
                <label class="form-check-label" for="flexRadioDefault">
                    {{$rol->nombrerol}}
                </label>
              </div>
                  
              @endforeach
             
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-primary" onclick="cambiarRolUser({{$user}})" data-dismiss="modal">Guardar cambios</button>
            </div>
        </form>
          </div>
        </div>
      </div>
      <div class="modal fade" id="agregarPermisosModal{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="agregarPermisosModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('user.agregarPermiso') }}">
                {{ csrf_field() }}
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="agregarPermisosModalLabel">{{$user->name}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                
                @foreach($permisos as $permiso)
                <div class="form-check ">
                    <input class="form-check-input" name="permiso[]" type="checkbox" value="{{$permiso->id}}" id="flexCheckPermisos">
                    <label class="form-check-label " for="flexCheckPermisos" >
                        {{$permiso->name}}
                    </label>
                </div>
                @endforeach
                
                <input type="hidden" name="user_id" value="{{$user->id}}">

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
              </div>
            
            </div>
            </form>
          </div>
    </div>
    @endforeach
    
@endsection