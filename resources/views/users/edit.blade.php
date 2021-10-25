@extends('plantillas.app')
<style>
    .card{
        margin-top: 30px;
    }
</style>
@section('contenido')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Editar') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('user.update') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nombre') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="rut" class="col-md-4 col-form-label text-md-right">{{__('Rut')}}</label>
                            <div class="col-md-6">
                                <input type="text" name="rut" id="rut" class="form-control @error('rut') is-invalid @enderror" value="{{ $user->rut }}" required autocomplete="rut" placeholder="xxxxxxxx-x" autofocus>
                                @error('rut')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="rut" class="col-md-4 col-form-label text-md-right">{{__('Telefono')}}</label>
                            <div class="col-md-6">
                                <input type="text" name="telefono" id="telefono" value="+569" class="form-control @error('telefono') is-invalid @enderror" value="{{ $user->telefono }}" required autocomplete="telefono" autofocus>
                                @error('telefono')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $user->email }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div> --}}
                        @if($user->rol->nombrerol == 'Administrador')
                        <div class="form-group row">
                            <label for="role" class="col-md-4 col-form-label text-md-right">{{__('Rol')}}</label>
                            <div class="col-md-6">
                                <select name="roles" id="roles" class="form-control">
                                    @foreach($roles as $rol)
                                    <option value="{{$rol->id}}">{{$rol->nombrerol}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <label for="avatar" class="col-md-4 col-form-label text-md-right">{{__('Avatar')}}</label>
                            
                            <div class="col-md-6">
                                <input type="file" id="avatar" value="{{$user->image_path}}" class="form-control @error('avatar') is-invalid @enderror" name="avatar" required autocomplete="avatar_user">
                                @error('avatar')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            @if($user->image_path)
  
                                <img src="{{url('usuarios/avatar/'.$user->image_path)}}" alt="" id="logo">
                            
                            @endif
                        </div>
                        <input type="hidden" id="userId" name="userId" value="{{ $user->id }}">
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Editar') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection