@extends('layouts.menu2') 
@section('title', 'Manuales de Usuario') 
@section('styles')
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f8;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #1e88e5;
      color: white;
      padding: 20px 40px;
      text-align: center;
    }

    h1 {
      margin: 0;
      font-size: 28px;
    }

    .container {
      max-width: 1000px;
      margin: 30px auto;
      padding: 0 20px;
    }

    .module {
      background-color: white;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .module h2 {
      margin-top: 0;
      font-size: 22px;
      color: #333;
      border-left: 4px solid #1e88e5;
      padding-left: 10px;
    }

    ul.manuals {
      list-style: none;
      padding-left: 0;
    }

    ul.manuals li {
      margin: 10px 0;
    }

    ul.manuals li a {
      text-decoration: none;
      color: #1e88e5;
      font-weight: 500;
      transition: color 0.2s;
    }

    ul.manuals li a:hover {
      color: #1565c0;
    }

    footer {
      text-align: center;
      padding: 20px;
      color: #777;
      font-size: 14px;
    }

    @media (max-width: 600px) {
      .module h2 {
        font-size: 18px;
      }

      h1 {
        font-size: 22px;
      }
    }
  </style>
@endsection
@section('content')
   <header>
    <h1 class="text-white">📚 Manuales de Usuario </h1>
  </header>

  <div class="container">
    @php
        //CUANDO SE AGREGUE UN MANUAL ESTA ES LA RUTA PARA COMPROBAR SI EXISTE
        //Y EN EL BOTON DE DEBE MANDAR COMO GET EL NOMBRE DE LA VARIABLE EJEMPLO /MANUAL_PROGRESO
        $MANUAL_PROGRESO = storage_path('app/public/Manuales/Estaciones/MANUAL_PROGRESO_TRAZABILIDAD.pdf');
        $MANUAL_ESTATUSOF = storage_path('app/public/Manuales/Estaciones/MANUAL_ESTATUSOF_TRAZABILIDAD.pdf');
        $MANUAL_SPLITTEL = storage_path('app/public/Manuales/Estaciones/MANUAL_SPLITTEL_TRAZABILIDAD.pdf');
        $MANUAL_MAQUILA = storage_path('app/public/Manuales/Estaciones/MANUAL_MAQUILA_TRAZABILIDAD.pdf');
        $MANUAL_IMEX = storage_path('app/public/Manuales/Estaciones/MANUAL_IMEX_TRAZABILIDAD.pdf');
        $MANUAL_PLANEACION = storage_path('app/public/Manuales/Estaciones/MANUAL_PLANEACION_TRAZABILIDAD.pdf');
        $MANUAL_CORTE = storage_path('app/public/Manuales/Estaciones/MANUAL_CORTE_TRAZABILIDAD.pdf');
        $MANUAL_SUMINISTRO = storage_path('app/public/Manuales/Estaciones/MANUAL_SUMINISTRO_TRAZABILIDAD.pdf');
        $MANUAL_CLASIFICACION = storage_path('app/public/Manuales/Estaciones/MANUAL_CLASIFICACION_TRAZABILIDAD.pdf');
        $MANUAL_TRANSICION = storage_path('app/public/Manuales/Estaciones/MANUAL_TRANSICION_TRAZABILIDAD.pdf');
        $MANUAL_PREPARADO = storage_path('app/public/Manuales/Estaciones/MANUAL_PREPARADO_TRAZABILIDAD.pdf');
        $MANUAL_RIBONIZADO = storage_path('app/public/Manuales/Estaciones/MANUAL_RIBONIZADO_TRAZABILIDAD.pdf');
        $MANUAL_ENSAMBLE = storage_path('app/public/Manuales/Estaciones/MANUAL_ENSAMBLE_TRAZABILIDAD.pdf');
        $MANUAL_CORTEDEFIBRA = storage_path('app/public/Manuales/Estaciones/MANUAL_CORTEDEFIBRA_TRAZABILIDAD.pdf');
        $MANUAL_PULIDO = storage_path('app/public/Manuales/Estaciones/MANUAL_PULIDO_TRAZABILIDAD.pdf');
        $MANUAL_ARMADO = storage_path('app/public/Manuales/Estaciones/MANUAL_ARMADO_TRAZABILIDAD.pdf');
        $MANUAL_INSPECCION = storage_path('app/public/Manuales/Estaciones/MANUAL_INSPECCION_TRAZABILIDAD.pdf');
        $MANUAL_POLARIDAD = storage_path('app/public/Manuales/Estaciones/MANUAL_POLARIDAD_TRAZABILIDAD.pdf');
        $MANUAL_CRIMPADO = storage_path('app/public/Manuales/Estaciones/MANUAL_CRIMPADO_TRAZABILIDAD.pdf');
        $MANUAL_MEDICION = storage_path('app/public/Manuales/Estaciones/MANUAL_MEDICION_TRAZABILIDAD.pdf');
        $MANUAL_VISUAL = storage_path('app/public/Manuales/Estaciones/MANUAL_VISUAL_TRAZABILIDAD.pdf');
        $MANUAL_MONTAJE = storage_path('app/public/Manuales/Estaciones/MANUAL_MONTAJE_TRAZABILIDAD.pdf');
        $MANUAL_EMPAQUE = storage_path('app/public/Manuales/Estaciones/MANUAL_EMPAQUE_TRAZABILIDAD.pdf');
        $MANUAL_GENERARETIQUETAS = storage_path('app/public/Manuales/Estaciones/MANUAL_GENERARETIQUETAS_TRAZABILIDAD.pdf');
        $MANUAL_USUARIOS = storage_path('app/public/Manuales/Estaciones/MANUAL_USUARIOS_TRAZABILIDAD.pdf');
        $MANUAL_ROLESYPERMISOS = storage_path('app/public/Manuales/Estaciones/MANUAL_ROLESYPERMISOS_TRAZABILIDAD.pdf');
        $MANUAL_LINEAS = storage_path('app/public/Manuales/Estaciones/MANUAL_LINEAS_TRAZABILIDAD.pdf');
    @endphp
    {{--<div class="module">
      <h2>🏠 Módulo Home</h2>
      <ul class="manuals">
        <li><a href="#">📘 Registro de Usuarios</a></li>
        <li><a href="#">📘 Asignación de Roles</a></li>
        <li><a href="#">📘 Recuperación de Contraseña</a></li>
      </ul>
    </div>
    
    @if(Auth::user()->hasPermission("Vista Dashboard"))
    <div class="module">
      <h2>📈 Módulo Dashboard</h2>
      <ul class="manuals">
        <li><a href="#">📘 Ingreso de Productos</a></li>
        <li><a href="#">📘 Actualización de Stock</a></li>
        <li><a href="#">📘 Reportes de Inventario</a></li>
      </ul>
    </div>
    @endif--}}
    @if(Auth::user()->hasPermission("Vista Progreso") OR Auth::user()->hasPermission("Vista Estatus Orden Fabricación"))
        <div class="module">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item border-top">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            <h2>🔍 Módulo B&uacute;squeda</h2>
                        </button>
                    </h2>
                    <div class="accordion-collapse collapse show" id="collapse1" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body pt-0">
                            <ul class="manuals">
                                @if(Auth::user()->hasPermission("Vista Progreso"))
                                    @if (file_exists($MANUAL_PROGRESO))
                                        <li><a href="#">📘 Progreso</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Estatus Orden Fabricación"))
                                    @if (file_exists($MANUAL_ESTATUSOF))
                                        <li><a href="#">📘 Estatus Orden Fabricación</a></li>
                                    @endif
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(Auth::user()->hasPermission("Vista Reportes"))
        <div class="module">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item border-top">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                            <h2>📄 Módulo Reportes</h2>
                        </button>
                    </h2>
                    <div class="accordion-collapse collapse show" id="collapse2" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body pt-0">
                            <ul class="manuals">
                                @if (file_exists($MANUAL_SPLITTEL))
                                    <li><a href="#">📘 SPLITEL</a></li>
                                @endif
                                @if (file_exists($MANUAL_MAQUILA))
                                    <li><a href="#">📘 MAQUILA</a></li>
                                @endif
                                @if (file_exists($MANUAL_IMEX))
                                    <li><a href="#">📘 IMEX</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="module">
        <div class="accordion" id="accordionExample">
                <div class="accordion-item border-top">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                            <h2>⚙️ Módulo Estaciones</h2>
                        </button>
                    </h2>
                    <div class="accordion-collapse collapse show" id="collapse3" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body pt-0">
                            <ul class="manuals">
                                @if(Auth::user()->hasPermission("Vista Planeacion"))
                                    @if (file_exists($MANUAL_PLANEACION))
                                        <li><a href="#">📘 Planeaci&oacute;n</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Corte"))
                                    @if (file_exists($MANUAL_CORTE))
                                        <li><a target="_blank" href="{{route('MostrarManual',['manual' => 'MANUAL_CORTE'])}}">📘 Corte</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Suministro"))
                                    @if (file_exists($MANUAL_SUMINISTRO))
                                        <li><a href="#">📘 Suministro</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Clasificación"))
                                    @if (file_exists($MANUAL_CLASIFICACION))
                                        <li><a href="#">📘 Clasificaci&oacute;n</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Transición"))
                                    @if (file_exists($MANUAL_TRANSICION))
                                        <li><a href="#">📘 Transici&oacute;n</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Preparado"))
                                    @if (file_exists($MANUAL_PREPARADO))
                                        <li><a href="#">📘 Preparado</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Ribonizado"))
                                    @if (file_exists($MANUAL_RIBONIZADO))
                                        <li><a href="#">📘 Ribonizado</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Ensamble"))
                                    @if (file_exists($MANUAL_ENSAMBLE))
                                        <li><a href="#">📘 Ensamble</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Corte de fibra"))
                                    @if (file_exists($MANUAL_CORTEDEFIBRA))
                                        <li><a href="#">📘 Corte de Fibra</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Pulido"))
                                    @if (file_exists($MANUAL_PULIDO))
                                        <li><a href="#">📘 Pulido</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Armado"))
                                    @if (file_exists($MANUAL_ARMADO))
                                        <li><a href="#">📘 Armado</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Inspección"))
                                    @if (file_exists($MANUAL_INSPECCION))
                                        <li><a href="#">📘 Inspecci&oacute;n</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Polaridad"))
                                    @if (file_exists($MANUAL_POLARIDAD))
                                        <li><a href="#">📘 Polaridad</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Crimpado"))
                                    @if (file_exists($MANUAL_CRIMPADO))
                                        <li><a href="#">📘 Crimpado</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Medicion"))
                                    @if (file_exists($MANUAL_MEDICION))
                                        <li><a href="#">📘 Medici&oacute;n</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Visualizacion"))
                                    @if (file_exists($MANUAL_VISUAL))
                                        <li><a href="#">📘 Visual</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Montaje"))
                                    @if (file_exists($MANUAL_MONTAJE))
                                        <li><a href="#">📘 Montaje</a></li>
                                    @endif
                                @endif
                                @if(Auth::user()->hasPermission("Vista Empaquetado"))
                                    @if (file_exists($MANUAL_EMPAQUE))
                                        <li><a href="#">📘 Empaque</a></li>
                                    @endif
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    @if(Auth::user()->hasPermission("Vista Etiquetas"))
        <div class="module">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item border-top">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
                            <h2>🖨️ Módulo Etiquetas</h2>
                        </button>
                    </h2>
                    <div class="accordion-collapse collapse show" id="collapse4" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body pt-0">
                            <ul class="manuals">
                                @if (file_exists($MANUAL_GENERARETIQUETAS))
                                    <li><a href="#">📘 Generar Etiquetas</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(Auth::user()->hasPermission("Vista Usuarios"))
        <div class="module">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item border-top">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="true" aria-controls="collapse5">
                            <h2>👤 Módulo Usuarios</h2>
                        </button>
                    </h2>
                    <div class="accordion-collapse collapse show" id="collapse5" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body pt-0">
                            <ul class="manuals">
                                @if (file_exists($MANUAL_USUARIOS))
                                    <li><a href="#">📘 Usuarios</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(Auth::user()->hasPermission("Vista Roles y permisos"))
        <div class="module">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item border-top">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="true" aria-controls="collapse6">
                            <h2>🛡️ Módulo Roles y Permisos</h2>
                        </button>
                    </h2>
                    <div class="accordion-collapse collapse show" id="collapse6" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body pt-0">
                            <ul class="manuals">
                                @if (file_exists($MANUAL_ROLESYPERMISOS))
                                    <li><a href="#">📘 Roles y Permisos</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(Auth::user()->hasPermission("Vista Lineas"))
        <div class="module">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item border-top">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="true" aria-controls="collapse7">
                            <h2>🔁 Módulo L&iacute;neas</h2>
                        </button>
                    </h2>
                    <div class="accordion-collapse collapse show" id="collapse7" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <ul class="manuals">
                            @if (file_exists($MANUAL_LINEAS))
                                <li><a href="#">📘 L&iacute;neas</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
  </div>

  <footer>
    &copy; 2025 Sistema Trazabilidad - Documentación de Usuario <br> Actualizada al 2025
  </footer>
@endsection
@section('scripts')
@endsection