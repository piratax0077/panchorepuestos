<!--Si existen permisos es un usuario con permisos especiales -->
@if(count(Auth::user()->permisos) > 0)
<nav class="navbar navbar-expand-lg navbar-light bg-light" id="navHeader" >
  <a href="/home"><img src="{{asset('storage/imagenes/logoOficial.jpeg')}}" alt="" srcset="" class="logoHeader"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      @foreach (Auth::user()->permisos as $permiso )
      @if ($permiso->name === "Ventas")
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ventas</a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="/ventas">Ventas (Fact-Bole)</a>
          <a class="dropdown-item" href="/notacredito">Nota de Crédito</a>
          <a class="dropdown-item" href="/notadebito">Nota de Débito</a>
          <a class="dropdown-item" href="/bienvenida">Bienvenida con rutas</a>
          </div>
        </li>
        @endif
        @if ($permiso->name === "Inventarios")
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Inventarios</a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="/factuprodu/crear">Facturas de Compra</a>
          <a class="dropdown-item" href="/compras/listar">Listar Facturas (Compras)</a>
          <a class="dropdown-item" href="/compras/listar">Orden de transporte</a>
          </div>
        </li>
        @endif
        @if ($permiso->name === "SII")
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">SII</a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="/sii/cargarfolios">Cargar Folios</a>
          {{-- <a class="dropdown-item" href="/sii">Estado de Envíos</a> --}}
          <a class="dropdown-item" href="/sii/ambiente">Ambiente Certificación</a>
          <a class="dropdown-item" href="#">Otra opción</a>
          </div>
        </li>
        @endif
        @if($permiso->name === "Libros")
        <li class="nav-item dropdown">
          <a href="javascript:void(0)" class="nav-link dropdown-toggle" id="navbarDropdownLibroLink" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">Libros</a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownLibroLink">
            <a class="dropdown-item" href="/rcof/">RCOF Boletas</a>
            <a class="dropdown-item" href="/libro/ventas">Libro Ventas</a>
            <a class="dropdown-item" href="/libro/compras">Libro Compras</a>
          </div>
        </li>
        @endif
        @if($permiso->name === "Reportes")
        <li class="nav-item dropdown">
          <a href="javascript:void(0)" class="nav-link dropdown-toggle" id="navbarDropdownReportesLink" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">Reportes</a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownReportesLink">
            <a class="dropdown-item" href="/reportes/ventasdiarias">Ventas diarias</a>
            <a class="dropdown-item" href="/reportes/documentosgenerados">Documentos generados</a>
            <a class="dropdown-item" href="/reportes/documentosgenerados">Buscar documentos</a>
            <a class="dropdown-item" href="/reportes/documentosgenerados">Operaciones transbank</a>
          </div>
        </li>
        @endif
        
      @endforeach
      @if(Auth::user()->rol->nombrerol !== "Administrador")
      <li class="nav-item dropdown">
        <a href="" class="nav-link dropdown-toggle" id="navbarDroprdownPruebaLink" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">Repuestos</a>
        <div class="dropdown-menu" aria-labelledby="navbarDroprdownPruebaLink">
          <a href="/repuesto/buscar" class="dropdown-item">Busqueda</a>
        </div>
      </li>
      @endif
    </ul>
    @if(Auth::user()->image_path)
    
        <img src="{{url('usuarios/avatar/'.Auth::user()->image_path)}}" alt="" id="logo">
      
      @endif
    <ul class="navbar-nav">
        
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdownMenuUser" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{Auth::user() -> name}} ({{Auth::user()->rol->nombrerol}})</a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuUser">
            {{-- <a href="{{url('usuarios/edit/'.Auth::user()->id)}}" class="dropdown-item">Editar usuario</a> --}}
            <a href="/cambiarclave" class="dropdown-item">Cambiar clave</a>
            <a class="dropdown-item" href="javascript:void(0)" onclick="salir()">Salir</a>
            
          </div>
      </li>
      </ul>
    </div>
    
</nav>
<!--Sino es un usuario con un rol definido -->
@else
  <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navHeader" >
    <a href="/home"><img src="{{asset('storage/imagenes/logoOficial.jpeg')}}" alt="" srcset="" class="logoHeader"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" style="border-color: white;">
      <span class="navbar-toggler-icon"></span>
    </button>
  
    <div class="collapse navbar-collapse" id="navbarSupportedContent" style="background: black; z-index: 100;">
      <ul class="navbar-nav mr-auto">
       @if (Auth::user()->rol->nombrerol !== "contabilidad" && Auth::user()->rol->nombrerol !== "jefe de bodega")
       <li class="nav-item dropdown">
        @if(Auth::user()->rol->nombrerol !== "Bodeguer@") <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ventas</a> @endif
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="/ventas">Ventas (Fact-Bole)</a>
          @if(Auth::user()->rol->nombrerol === "Cajer@" || Auth::user()->rol->nombrerol === "Administrador")
          <a class="dropdown-item" href="/notacredito">Nota de Crédito</a>
          <a class="dropdown-item" href="/notadebito">Nota de Débito</a>
          <a class="dropdown-item" href="/ventas/arqueocaja">Arqueo de caja</a>
          @endif
        </div>
    </li>  
       @endif
        

      
          @if (Auth::user()->rol->nombrerol == "Administrador" || Auth::user()->rol->nombrerol == "Bodeguer@" || Auth::user()->rol->nombrerol == "bodega-venta" || Auth::user()->rol->nombrerol == "jefe de bodega" )
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Inventarios</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="/factuprodu/crear">Facturas de Compra</a>
            <a class="dropdown-item" href="/compras/listar">Listar Facturas (Compras)</a>
            @if(Auth::user()->rol->nombrerol == "Administrador") <a href="/repuesto" class="dropdown-item">Buscar repuesto</a> @endif
            <a href="/guiadespacho" class="dropdown-item">Guía de despacho</a>
            <a href="" class="dropdown-item">Recepción de guía de despacho</a>
            <a href="/guiadespacho/traspaso_mercaderia" class="dropdown-item">Traspaso de mercadería</a>
            <a class="dropdown-item" href="/ot">Orden de transporte</a>
            @if(Auth::user()->rol->nombrerol == "Administrador")<a class="dropdown-item" href="/inventario">Inventario por tienda</a>@endif
            </div>
          </li>
          @endif
          
          @if (Auth::user()->rol->nombrerol == "Administrador" || Auth::user()->rol->nombrerol == "contabilidad")
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">SII</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="/sii/cargarfolios">Cargar Folios</a>
            <a class="dropdown-item" href="/sii/anularfolios">Anulación de folios</a>
            <a class="dropdown-item" href="/sii/estadodte">Estado DTE</a>
            {{-- <a class="dropdown-item" href="/sii/verestado">Estado de Envíos</a> --}}
            <a class="dropdown-item" href="/sii/ambiente">Ambiente Certificación</a>
            </div>
        </li>
          @endif
          
          @if(Auth::user()->rol->nombrerol == "Administrador")
          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Mantenimiento</a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item" href="/marcavehiculo">Marca Vehículos</a>
                  <a class="dropdown-item" href="/modelovehiculo">Modelo Vehículos</a>
                  <a class="dropdown-item" href="/rol">Roles de Usuario</a>
                  <a class="dropdown-item" href="/familia">Familia de Repuestos</a>
                  <a class="dropdown-item" href="/marcarepuesto">Marca de Repuestos</a>
                  <a class="dropdown-item" href="/repuesto/modificar">Modificar Repuestos</a>
                  <a class="dropdown-item" href="/repuesto">Catálogo de Repuestos</a>
                  <a class="dropdown-item" href="/pais">Países</a>
                  <a class="dropdown-item" href="/proveedor">Proveedores</a>
                  <a class="dropdown-item" href="/formapago">Formas de Pago</a>
                  <a class="dropdown-item" href="/limitecredito">Límites de Crédito</a>
                  <a class="dropdown-item" href="/diascredito">Días de Crédito</a>
                  <a class="dropdown-item" href="/clientes">Agregar Clientes</a>
                  <a class="dropdown-item" href="/clientes/xpress">Clientes Xpress</a>
                  <a class="dropdown-item" href="/parametros">Parámetros</a>
                  <a class="dropdown-item" href="/relacionados">Repuestos Relacionados</a>
                  <div class="dropdown-divider"></div>
                  <div class="dropdown-header">Sistema</div>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="/usuarios">Usuarios</a>
                  
              </div>
          </li>
          @endif
          @if(Auth::user()->rol->nombrerol == "bodega-venta" || Auth::user()->rol->nombrerol == "vendedor" || Auth::user()->rol->nombrerol == "Cajer@")
          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Mantenimiento</a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  
                  <a class="dropdown-item" href="/clientes">Agregar Clientes</a>
                  
                  
              </div>
          </li>
          @endif
          @if(Auth::user()->rol->nombrerol == "Administrador" || Auth::user()->rol->nombrerol == "contabilidad")
          <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="nav-link dropdown-toggle" id="navbarDropdownLibroLink" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">Libros</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownLibroLink">
              <a class="dropdown-item" href="/rcof/">RCOF Boletas</a>
              <a class="dropdown-item" href="/libro/ventas">Libro Ventas</a>
              <a class="dropdown-item" href="/libro/compras">Libro Compras</a>
            </div>
          </li>
          @endif
          @if(Auth::user()->rol->nombrerol == "Administrador")
          <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="nav-link dropdown-toggle" id="navbarDropdownReportesLink" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">Reportes</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownReportesLink">
              <a class="dropdown-item" href="/reportes/ventasdiarias">Ventas diarias</a>
              <a class="dropdown-item" href="/reportes/documentosgenerados">Documentos generados</a>
              <a class="dropdown-item" href="/reportes/documentosgenera2">Buscar documentos</a>
              <a class="dropdown-item" href="/reportes/transbank">Documentos Transbank</a>
            </div>
          </li>
          @endif

          <li class="nav-item dropdown">
            <a href="javascript:void(0)" class="nav-link dropdown-toggle" id="navbarDropdownBusquedaNueva" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">Repuestos</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownBusquedaNueva">
              <a class="dropdown-item" href="/repuesto/buscar">Buscar repuesto</a>
              
            </div>
          </li>
          
      </ul>
      @if(Auth::user()->image_path)
    
        <img src="{{url('usuarios/avatar/'.Auth::user()->image_path)}}" alt="" id="logo">
      
      @endif
      <ul class="navbar-nav">
        
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdownMenuUser" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{Auth::user() -> name}} ({{Auth::user()->rol->nombrerol}})</a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuUser">
            @if(Auth::user()->rol->nombrerol === "Administrador") <a href="{{url('usuarios/edit/'.Auth::user()->id)}}" class="dropdown-item">Editar usuario</a> @endif
            <a href="/cambiarclave" class="dropdown-item">Cambiar clave</a>
            <form action="/logout" method="post">
            @csrf
              <button class="dropdown-item" type="submit" style="color: white;">Salir</button>
            </form>
            
          </div>
      </li>
      </ul>
      
    </div>
    
  </nav>
  
  

@endif

