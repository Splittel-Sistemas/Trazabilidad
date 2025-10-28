<!DOCTYPE html>
<html lang="es-MX" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
        <meta name="viewport" content="width=device-width, initial-scale=0.8, maximum-scale=0.8, user-scalable=no">
        <!-- ===============================================-->
        <!--    Document Title-->
        <!-- ===============================================-->
        <title>@yield('title', 'splittel')</title>
        <!-- ===============================================-->
        <!--    Favicons-->
        <!-- ===============================================-->
        <link rel="shortcut icon" type="image/x-icon" href="{{asset('imagenes/Trazabilidad.png') }}" sizes="10x1">
        <link rel="manifest" href="{{asset('menu2/assets/img/favicons/manifest.json')}}">
        <meta name="msapplication-TileImage" content="{{asset('menu2/assets/img/favicons/mstile-150x150.png')}}">
        <meta name="theme-color" content="#ffffff">
        <script src="{{asset('menu2/vendors/imagesloaded/imagesloaded.pkgd.min.js')}}"></script>
        <script src="{{asset('menu2/vendors/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('menu2/assets/js/config.js')}}"></script>
        <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- ===============================================-->
        <!--    Stylesheets-->
        <!-- ===============================================-->
        
        <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
        <link rel="preconnect" href="https://fonts.googleapis.com/">
        <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
        <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
        <link href="{{asset('menu2/vendors/simplebar/simplebar.min.css')}}" rel="stylesheet">
        <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
        <!-- Datatable -->
            <link rel="stylesheet" href="{{asset('css/DataTable/dataTables.bootstrap5.min.css')}}">
        <!--<link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.min.css">-->
        <link href="{{asset('menu2/assets/css/theme-rtl.min.css')}}" type="text/css" rel="stylesheet" id="style-rtl">
        <link href="{{asset('menu2/assets/css/theme.min.css')}}" type="text/css" rel="stylesheet" id="style-default">
        <link href="{{asset('menu2/assets/css/user-rtl.min.css')}}" type="text/css" rel="stylesheet" id="user-style-rtl">
        <link href="{{asset('menu2/assets/css/user.min.css')}}" type="text/css" rel="stylesheet" id="user-style-default">
        @yield('styles')
        <style>
            .Apuntarbox{
                border: 4px solid transparent;
                border-radius:0.5rem;
                padding: 3px;
                animation: borderBlink 1s infinite alternate;
            }
            @keyframes borderBlink {
                0% {
                    border-color: transparent;
                }
                50% {
                    border-color: #0000ff;
                }
                100% {
                    border-color: transparent;
                }
            }
            .badge-scale {
                position: relative;
                right: 2rem;
                transform: scale(1.3);
                transition: transform 0.2s ease;
            }
            @media (min-width: 576px) {
                .badge-scale {
                    right: 2.6rem;
                    transform: scale(1.7);
                }
            }

        </style>
        <script>
            var phoenixIsRTL = window.config.config.phoenixIsRTL;
            if (phoenixIsRTL) {
                var linkDefault = document.getElementById('style-default');
                var userLinkDefault = document.getElementById('user-style-default');
                linkDefault.setAttribute('disabled', true);
                userLinkDefault.setAttribute('disabled', true);
                document.querySelector('html').setAttribute('dir', 'rtl');
            } else {
                var linkRTL = document.getElementById('style-rtl');
                var userLinkRTL = document.getElementById('user-style-rtl');
                linkRTL.setAttribute('disabled', true);
                userLinkRTL.setAttribute('disabled', true);
            }
        </script>
        <link href="{{asset('menu2/vendors/leaflet/leaflet.css')}}" rel="stylesheet">
        <link href="{{asset('menu2/vendors/leaflet.markercluster/MarkerCluster.css')}}" rel="stylesheet">
        <link href="{{asset('menu2/vendors/leaflet.markercluster/MarkerCluster.Default.css')}}" rel="stylesheet">
    </head>
    <body>
        <!-- ===============================================-->
        <!--NOTIFICACIONES-->
        <div id="Notificaciones" class="position-fixed top-4 end-0 pt-10 px-2" style="z-index: 2055;display: flex; flex-direction: column; gap: 0.5rem;">
        </div>
        <!-- ===============================================-->
        <!--    Main Content-->
        <!-- ===============================================-->
        <main class="main" id="top">
            <!--Menu Izquerda--->
            <nav class="navbar navbar-vertical navbar-expand-lg" >
                <script>
                    var navbarStyle = window.config.config.phoenixNavbarStyle;
                    if (navbarStyle && navbarStyle !== 'transparent') {
                        document.querySelector('body').classList.add(`navbar-${navbarStyle}`);
                    }
                </script>
                <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
                    <!-- scrollbar removed-->
                    <div class="navbar-vertical-content">
                        <ul class="navbar-nav flex-column" id="navbarVerticalNav">
                            <li class="nav-item"><!-- dashboard-->
                                <p class="navbar-vertical-label">Trazabilidad</p>
                                <hr class="navbar-vertical-line"/>
                                <div class="nav-item-wrapper">
                                    <a class="nav-link label-1 {{ Route::is('index.operador') ? 'active' : '' }}"  href="{{route('index.operador')}}" role="button">
                                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="home"></span> </span><span class="nav-link-text-wrapper"><span class="nav-link-text">Home</span></span></div>
                                    </a>
                                </div> 
                                @if(Auth::user()->hasPermission("Vista Dashboard"))
                                    <div class="nav-item-wrapper">
                                        <a class="nav-link label-1 {{ Route::is('Home') ? 'active' : '' }}" href="{{route('Home')}}" role="button" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="pie-chart"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Dashboard</span></span></div>
                                        </a>
                                    </div>
                                @endif                           
                            </li>
                            @if(Auth::user()->hasPermission("Vista Progreso") OR Auth::user()->hasPermission("Vista Estatus Orden Fabricación"))
                                <li class="nav-item"><!-- Busqueda-->
                                    <!-- label-->
                                    <p class="navbar-vertical-label">B&uacute;squeda</p>
                                    <hr class="navbar-vertical-line" />
                                    <div class="nav-item-wrapper">
                                        <a class="nav-link dropdown-indicator label-1 {{ Route::is('Busquedas.OV') ? 'nav-tabs active' : '' }} ? 'nav-tabs active' : '' }}" href="#nv-e-busqueda" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-e-busqueda">
                                            <div class="d-flex align-items-center">
                                            <div class="dropdown-indicator-icon"><span class="fas fa-caret-right"></span></div><span class="nav-link-icon"><span data-feather="search"></span></span><span class="nav-link-text">B&uacute;squeda</span>
                                            </div>
                                        </a>
                                        <div class="parent-wrapper label-1">
                                            <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-e-busqueda">
                                                <li class="collapsed-nav-item-title d-none">B&uacute;squeda</li>
                                                @if(Auth::user()->hasPermission("Vista Progreso"))
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ Route::is('Busquedas.OV') ? 'nav-tabs active' : '' }}" href="{{route('Busquedas.OV')}}" data-bs-toggle="" aria-expanded="false">
                                                            <div class="d-flex align-items-center"><span class="nav-link-text">Progreso</span></div>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(Auth::user()->hasPermission("Vista Estatus Orden Fabricación"))
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ Route::is('EstatusOrdenesFabricacion') ? 'nav-tabs active' : '' }}" href="{{route('EstatusOrdenesFabricacion')}}" data-bs-toggle="" aria-expanded="false">
                                                            <div class="d-flex align-items-center"><span class="nav-link-text">Estatus Orden Fabricaci&oacute;n</span></div>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission("Vista Reportes"))
                                <li class="nav-item"><!-- reportes-->
                                    <p class="navbar-vertical-label">Reportes</p>
                                    <hr class="navbar-vertical-line">
                                    <div class="nav-item-wrapper">
                                        <a class="nav-link dropdown-indicator label-1 collapsed" href="#nv-e-reportes" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-e-reportes">
                                            <div class="d-flex align-items-center">
                                                <div class="dropdown-indicator-icon">
                                                    <svg class="svg-inline--fa fa-caret-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg="">
                                                        <path fill="currentColor" d="M118.6 105.4l128 127.1C252.9 239.6 256 247.8 256 255.1s-3.125 16.38-9.375 22.63l-128 127.1c-9.156 9.156-22.91 11.9-34.88 6.943S64 396.9 64 383.1V128c0-12.94 7.781-24.62 19.75-29.58S109.5 96.23 118.6 105.4z"></path>
                                                    </svg>
                                                </div>
                                                <span class="nav-link-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text text-900 fs-3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                                </span><span class="nav-link-text">Reportes</span>
                                            </div>
                                        </a>
                                        <div class="parent-wrapper label-1">
                                            <ul class="nav parent collapse" data-bs-parent="#navbarVerticalCollapse" id="nv-e-reportes" style="">
                                                <li class="collapsed-nav-item-title d-none">Reportes</li>
                                                <li class="nav-item">
                                                    <a class="nav-link dropdown-indicator collapsed" href="#nv-splittel" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-splittel">
                                                        <div class="d-flex align-items-center">
                                                            <div class="dropdown-indicator-icon">
                                                                <svg class="svg-inline--fa fa-caret-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="clipb" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg="">
                                                                    <path fill="currentColor" d="M118.6 105.4l128 127.1C252.9 239.6 256 247.8 256 255.1s-3.125 16.38-9.375 22.63l-128 127.1c-9.156 9.156-22.91 11.9-34.88 6.943S64 396.9 64 383.1V128c0-12.94 7.781-24.62 19.75-29.58S109.5 96.23 118.6 105.4z"></path>
                                                                </svg>
                                                            </div>
                                                            <span class="nav-link-text">SPLITTEL</span>
                                                        </div>
                                                    </a>
                                                    <div class="parent-wrapper">
                                                        <ul class="nav parent collapse" data-bs-parent="#e-commerce" id="nv-splittel" style="">
                                                            <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="" aria-expanded="false">
                                                                <div class="d-flex align-items-center"><span class="nav-link-text">Reporte 1</span></div>
                                                            </a><!-- more inner pages-->
                                                            </li>
                                                            <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="" aria-expanded="false">
                                                                <div class="d-flex align-items-center"><span class="nav-link-text">Reporte 2</span></div>
                                                            </a><!-- more inner pages-->
                                                            </li>
                                                            <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="" aria-expanded="false">
                                                                <div class="d-flex align-items-center"><span class="nav-link-text">Reporte 3</span></div>
                                                            </a><!-- more inner pages-->
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link dropdown-indicator collapsed" href="#nv-maquila" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-maquila">
                                                        <div class="d-flex align-items-center">
                                                            <div class="dropdown-indicator-icon">
                                                                <svg class="svg-inline--fa fa-caret-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="clipb" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg="">
                                                                    <path fill="currentColor" d="M118.6 105.4l128 127.1C252.9 239.6 256 247.8 256 255.1s-3.125 16.38-9.375 22.63l-128 127.1c-9.156 9.156-22.91 11.9-34.88 6.943S64 396.9 64 383.1V128c0-12.94 7.781-24.62 19.75-29.58S109.5 96.23 118.6 105.4z"></path>
                                                                </svg>
                                                            </div>
                                                            <span class="nav-link-text">MAQUILA</span>
                                                        </div>
                                                    </a>
                                                    <div class="parent-wrapper">
                                                        <ul class="nav parent collapse" data-bs-parent="#e-commerce" id="nv-maquila" style="">
                                                            <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="" aria-expanded="false">
                                                                <div class="d-flex align-items-center"><span class="nav-link-text">Reporte 1</span></div>
                                                            </a><!-- more inner pages-->
                                                            </li>
                                                            <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="" aria-expanded="false">
                                                                <div class="d-flex align-items-center"><span class="nav-link-text">Reporte 2</span></div>
                                                            </a><!-- more inner pages-->
                                                            </li>
                                                            <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="" aria-expanded="false">
                                                                <div class="d-flex align-items-center"><span class="nav-link-text">Reporte 3</span></div>
                                                            </a><!-- more inner pages-->
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link dropdown-indicator collapsed" href="#nv-imex" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-imex">
                                                        <div class="d-flex align-items-center">
                                                            <div class="dropdown-indicator-icon">
                                                                <svg class="svg-inline--fa fa-caret-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="clipb" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg="">
                                                                    <path fill="currentColor" d="M118.6 105.4l128 127.1C252.9 239.6 256 247.8 256 255.1s-3.125 16.38-9.375 22.63l-128 127.1c-9.156 9.156-22.91 11.9-34.88 6.943S64 396.9 64 383.1V128c0-12.94 7.781-24.62 19.75-29.58S109.5 96.23 118.6 105.4z"></path>
                                                                </svg>
                                                            </div>
                                                            <span class="nav-link-text">IMEX</span>
                                                        </div>
                                                    </a>
                                                    <div class="parent-wrapper">
                                                        <ul class="nav parent collapse" data-bs-parent="#e-commerce" id="nv-imex" style="">
                                                            <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="" aria-expanded="false">
                                                                <div class="d-flex align-items-center"><span class="nav-link-text">Reporte 1</span></div>
                                                            </a><!-- more inner pages-->
                                                            </li>
                                                            <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="" aria-expanded="false">
                                                                <div class="d-flex align-items-center"><span class="nav-link-text">Reporte 2</span></div>
                                                            </a><!-- more inner pages-->
                                                            </li>
                                                            <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="" aria-expanded="false">
                                                                <div class="d-flex align-items-center"><span class="nav-link-text">Reporte 3</span></div>
                                                            </a><!-- more inner pages-->
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            <li class="nav-item"><!-- Areas-->
                                <!-- label-->
                                <p class="navbar-vertical-label">Estaciones</p>
                                <hr class="navbar-vertical-line" />
                                <div class="nav-item-wrapper">
                                    <a class="nav-link dropdown-indicator label-1 {{ Route::is('Planeacion') ? 'nav-tabs active' : '' }}{{ Route::is('corte.index') ? 'nav-tabs active' : '' }}{{ Route::is('Suministro') ? 'nav-tabs active' : '' }}" href="#nv-e-areas" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-e-areas">
                                        <div class="d-flex align-items-center">
                                        <div class="dropdown-indicator-icon"><span class="fas fa-caret-right"></span></div><span class="nav-link-icon"><span data-feather="command"></span></span><span class="nav-link-text">Estaciones</span>
                                        </div>
                                    </a>
                                    <div class="parent-wrapper label-1">
                                        <ul class="nav collapse parent" data-bs-parent="#navbarVerticalCollapse" id="nv-e-areas">
                                            <li class="collapsed-nav-item-title d-none">Estaciones</li>
                                            @if(Auth::user()->hasPermission("Vista Planeacion"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Planeacion') ? 'nav-tabs active' : '' }}" href="{{route('Planeacion')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Planeaci&oacute;n</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Corte"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('corte.index') ? 'nav-tabs active' : '' }}" href="{{route('corte.index')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Corte</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Suministro"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Suministro') ? 'nav-tabs active' : '' }}" href="{{route('Suministro')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Suministro</span></div>
                                                    </a>
                                                    <hr class="p-0 m-1">
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Clasificación"))
                                            <li class="nav-item"><a class="nav-link {{ Route::is('Clasificacion') ? 'nav-tabs active' : '' }}" href="{{route('Clasificacion')}}" data-bs-toggle="" aria-expanded="false">
                                                <div class="d-flex align-items-center"><span class="nav-link-text">Clasificaci&oacute;n</span></div>
                                                </a>
                                            </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Transición"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Transicion') ? 'nav-tabs active' : '' }}" href="{{route('Transicion')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Transici&oacute;n</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Preparado"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Preparado') ? 'nav-tabs active' : '' }}" href="{{route('Preparado')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Preparado</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Ribonizado"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Ribonizado') ? 'nav-tabs active' : '' }}" href="{{route('Ribonizado')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Ribonizado</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Ensamble"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Ensamble') ? 'nav-tabs active' : '' }}" href="{{route('Ensamble')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Ensamble</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Corte de fibra"))
                                            <li class="nav-item"><a class="nav-link {{ Route::is('Cortedefibra') ? 'nav-tabs active' : '' }}" href="{{route('Cortedefibra')}}" data-bs-toggle="" aria-expanded="false">
                                                <div class="d-flex align-items-center"><span class="nav-link-text">Corte de fibra</span></div>
                                                </a>
                                            </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Pulido"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Pulido') ? 'nav-tabs active' : '' }}" href="{{route('Pulido')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Pulido</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            
                                            @if(Auth::user()->hasPermission("Vista Armado"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Armado') ? 'nav-tabs active' : '' }}" href="{{route('Armado')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Armado</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Inspección"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Inspeccion') ? 'nav-tabs active' : '' }}" href="{{route('Inspeccion')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Inspecci&oacute;n</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Polaridad"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Polaridad') ? 'nav-tabs active' : '' }}" href="{{route('Polaridad')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Polaridad</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Crimpado"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Crimpado') ? 'nav-tabs active' : '' }}" href="{{route('Crimpado')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Crimpado</span></div>
                                                    </a>
                                                    <hr class="p-0 m-1">
                                                </li>
                                            @endif

                                            @if(Auth::user()->hasPermission("Vista Medicion"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Medicion') ? 'nav-tabs active' : '' }}" href="{{route('Medicion')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Medici&oacute;n</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Visualizacion"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Visualizacion') ? 'nav-tabs active' : '' }}" href="{{route('Visualizacion')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Visual</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Montaje"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Montaje') ? 'nav-tabs active' : '' }}" href="{{route('Montaje')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Montaje</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Auth::user()->hasPermission("Vista Empaquetado"))
                                                <li class="nav-item"><a class="nav-link {{ Route::is('Empacado') ? 'nav-tabs active' : '' }}" href="{{route('Empacado')}}" data-bs-toggle="" aria-expanded="false">
                                                    <div class="d-flex align-items-center"><span class="nav-link-text">Empaque</span></div>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <!--Etiquetas-->
                                @if(Auth::user()->hasPermission("Vista Etiquetas"))
                                <div class="nav-item-wrapper">
                                    <a class="nav-link label-1 {{ Route::is('Etiquetas.index') ? 'active' : '' }}" href="{{route('Etiquetas.index')}}" role="button" data-bs-toggle="" aria-expanded="false">
                                        <div class="d-flex align-items-center"><span class="nav-link-icon"><i class="fas fa-print"></i></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Etiquetas</span></span></div>
                                    </a>
                                </div>
                                @endif
                            </li>
                            @if(Auth::user()->hasPermission("Vista Usuarios"))
                                <li class="nav-item"><!-- usuarios-->
                                    <p class="navbar-vertical-label">Usuarios</p>
                                    <hr class="navbar-vertical-line" /><!-- parent pages-->
                                    <div class="nav-item-wrapper">
                                        <a class="nav-link label-1 {{ Route::is('registro.index') ? 'active' : '' }}" href="{{route('registro.index')}}" role="button" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="user"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Usuarios</span></span></div>
                                        </a>
                                    </div>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission("Vista Roles y permisos"))
                                <li class="nav-item"><!-- Roles y permisos-->
                                    <div class="nav-item-wrapper">
                                        <a class="nav-link label-1 {{ Route::is('RolesPermisos.index') ? 'active' : '' }}" href="{{route('RolesPermisos.index')}}" role="button" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="key"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Roles y permisos</span></span></div>
                                        </a>
                                    </div>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission("Vista Lineas"))
                                <li class="nav-item"><!-- Lineas-->
                                    <p class="navbar-vertical-label">Configuraci&oacute;nes</p>
                                    <hr class="navbar-vertical-line" /><!-- parent pages-->
                                    <div class="nav-item-wrapper">
                                        <a class="nav-link label-1 {{ Route::is('index.linea') ? 'active' : '' }}" href="{{route('index.linea')}}" role="button" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-icon"> <span><i class="fa-solid fa-list-ol"></i></span></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">L&iacute;neas</span></span></div>
                                        </a>
                                    </div>
                                </li>
                            @endif
                                <li class="nav-item"><!-- Lineas-->
                                    <p class="navbar-vertical-label">Manuales de Usuario</p>
                                    <hr class="navbar-vertical-line" /><!-- parent pages-->
                                    <div class="nav-item-wrapper">
                                        <a class="nav-link label-1 {{ Route::is('ManualesUsuario') ? 'active' : '' }}" href="{{route('ManualesUsuario')}}" role="button" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-icon"> <span><i class="fa-solid fa-book"></i></span></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Manuales de Usuario</span></span></div>
                                        </a>
                                    </div>
                                </li>
                        </ul>
                    </div>
                </div>
                <div class="navbar-vertical-footer bg-white">
                    <button class="btn navbar-vertical-toggle border-0 fw-semi-bold w-100 white-space-nowrap d-flex align-items-center">
                        <span class="uil uil-left-arrow-to-left fs-0"></span>
                        <span class="uil uil-arrow-from-right fs-0"></span>
                        <span class="navbar-vertical-footer-text ms-2">Cerrar Men&uacute;</span>
                    </button>
                </div>
            </nav>
            <nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault" style="display:none;">
                <div class="collapse navbar-collapse justify-content-between">
                <div class="navbar-logo">
                    <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
                    <a class="navbar-brand m-0 ">
                        <div class="d-flex align-items-start">
                            <div class="d-flex align-items-center">
                                <a class="navbar-brand p-0" href="./">
                                    <img src="{{asset('imagenes/splittel.png') }}" alt="Splittel" class="img-fluid" style="max-width: 100px;">
                                </a>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="search-box navbar-top-search-box d-none d-lg-block" data-list='{"valueNames":["title"]}' style="width:25rem;">
                    <div class="dropdown-menu border border-300 font-base start-0 py-0 overflow-hidden w-100">
                    <div class="scrollbar-overlay" style="max-height: 30rem;">
                        <div class="list pb-3">
                        <h6 class="dropdown-header text-1000 fs--2 py-2">24 <span class="text-500">results</span></h6>
                        <hr class="text-200 my-0" />
                        <h6 class="dropdown-header text-1000 fs--1 border-bottom border-200 py-2 lh-sm">Recently Searched </h6>
                        <div class="py-2"><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                            <div class="d-flex align-items-center">
                                <div class="fw-normal text-1000 title"><span class="fa-solid fa-clock-rotate-left" data-fa-transform="shrink-2"></span> Store Macbook</div>
                            </div>
                            </a>
                            <a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                            <div class="d-flex align-items-center">
                                <div class="fw-normal text-1000 title"> <span class="fa-solid fa-clock-rotate-left" data-fa-transform="shrink-2"></span> MacBook Air - 13″</div>
                            </div>
                            </a>
                        </div>
                        <hr class="text-200 my-0" />
                        <h6 class="dropdown-header text-1000 fs--1 border-bottom border-200 py-2 lh-sm">Products</h6>
                        <div class="py-2"><a class="dropdown-item py-2 d-flex align-items-center" href="apps/e-commerce/landing/product-details.html">
                            <div class="flex-1">
                                <h6 class="mb-0 text-1000 title">MacBook Air - 13″</h6>
                                <p class="fs--2 mb-0 d-flex text-700"><span class="fw-medium text-600">8GB Memory - 1.6GHz - 128GB Storage</span></p>
                            </div>
                            </a>
                            <a class="dropdown-item py-2 d-flex align-items-center" href="apps/e-commerce/landing/product-details.html">
                            <div class="flex-1">
                                <h6 class="mb-0 text-1000 title">MacBook Pro - 13″</h6>
                                <p class="fs--2 mb-0 d-flex text-700"><span class="fw-medium text-600 ms-2">30 Sep at 12:30 PM</span></p>
                            </div>
                            </a>
                        </div>
                        <hr class="text-200 my-0" />
                        <h6 class="dropdown-header text-1000 fs--1 border-bottom border-200 py-2 lh-sm">Quick Links</h6>
                        <div class="py-2"><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                            <div class="d-flex align-items-center">
                                <div class="fw-normal text-1000 title"><span class="fa-solid fa-link text-900" data-fa-transform="shrink-2"></span> Support MacBook House</div>
                            </div>
                            </a>
                            <a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                            <div class="d-flex align-items-center">
                                <div class="fw-normal text-1000 title"> <span class="fa-solid fa-link text-900" data-fa-transform="shrink-2"></span> Store MacBook″</div>
                            </div>
                            </a>
                        </div>
                        <hr class="text-200 my-0" />
                        <h6 class="dropdown-header text-1000 fs--1 border-bottom border-200 py-2 lh-sm">Files</h6>
                        <div class="py-2"><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                            <div class="d-flex align-items-center">
                                <div class="fw-normal text-1000 title"><span class="fa-solid fa-file-zipper text-900" data-fa-transform="shrink-2"></span> Library MacBook folder.rar</div>
                            </div>
                            </a>
                            <a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                            <div class="d-flex align-items-center">
                                <div class="fw-normal text-1000 title"> <span class="fa-solid fa-file-lines text-900" data-fa-transform="shrink-2"></span> Feature MacBook extensions.txt</div>
                            </div>
                            </a>
                            <a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                            <div class="d-flex align-items-center">
                                <div class="fw-normal text-1000 title"> <span class="fa-solid fa-image text-900" data-fa-transform="shrink-2"></span> MacBook Pro_13.jpg</div>
                            </div>
                            </a>
                        </div>
                        <hr class="text-200 my-0" />
                        <h6 class="dropdown-header text-1000 fs--1 border-bottom border-200 py-2 lh-sm">Members</h6>
                        <div class="py-2"><a class="dropdown-item py-2 d-flex align-items-center" href="pages/members.html">
                            <div class="avatar avatar-l status-online  me-2 text-900">
                                <!--<img class="rounded-circle " src="assets/img/team/40x40/10.webp" alt="" />-->
                            </div>
                            <div class="flex-1">
                                <h6 class="mb-0 text-1000 title">Carry Anna</h6>
                                <p class="fs--2 mb-0 d-flex text-700">anna@technext.it</p>
                            </div>
                            </a>
                            <a class="dropdown-item py-2 d-flex align-items-center" href="pages/members.html">
                            <div class="avatar avatar-l  me-2 text-900">
                            </div>
                            <div class="flex-1">
                                <h6 class="mb-0 text-1000 title">John Smith</h6>
                                <p class="fs--2 mb-0 d-flex text-700">smith@technext.it</p>
                            </div>
                            </a>
                        </div>
                        <hr class="text-200 my-0" />
                        <h6 class="dropdown-header text-1000 fs--1 border-bottom border-200 py-2 lh-sm">Related Searches</h6>
                        <div class="py-2"><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                            <div class="d-flex align-items-center">
                                <div class="fw-normal text-1000 title"><span class="fa-brands fa-firefox-browser text-900" data-fa-transform="shrink-2"></span> Search in the Web MacBook</div>
                            </div>
                            </a>
                            <a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                            <div class="d-flex align-items-center">
                                <div class="fw-normal text-1000 title"> <span class="fa-brands fa-chrome text-900" data-fa-transform="shrink-2"></span> Store MacBook″</div>
                            </div>
                            </a>
                        </div>
                        </div>
                        <div class="text-center">
                        <p class="fallback fw-bold fs-1 d-none">No Result Found.</p>
                        </div>
                    </div>
                    </div>
                </div>
                <ul class="navbar-nav navbar-nav-icons flex-row">
                    <li class="nav-item">
                         @if(app()->environment('local', 'staging'))
                            {{--Solo Ambiente de pruebas--}}
                            <span class="badge bg-danger badge-scale">⚠️ Ambiente de pruebas</span>
                        @endif
                    </li>
                    <li class="nav-item">
                    <div class="theme-control-toggle fa-icon-wait px-2"><input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" /><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="moon"></span></label><label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="sun"></span></label></div>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar avatar-l ">
                        <img class="rounded-circle " src="{{asset('imagenes/user.png') }}" alt="" />
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border border-300" aria-labelledby="navbarDropdownUser">
                        <div class="card position-relative border-0">
                        <div class="card-body bg-white p-0">
                            <div class="text-center pt-4 pb-3">
                            <div class="avatar avatar-xl ">
                                <img class="rounded-circle " src="{{asset('imagenes/user.png') }}" alt="" />
                            </div>
                            @if (Auth::check())
                            <h6 class="mt-2 text-black">{{ Auth::user()->name }}</h6>
                            @endif
                            </div>
                            
                        <div class="overflow-auto scrollbar" style="height: 5rem;">
                            <ul class="nav d-flex flex-column mb-2 pb-1">
                                <li class="nav-item"><a class="nav-link px-3" href="{{route('index.perfil')}}"> <span class="me-2 text-900" data-feather="user"></span><span>Perfil</span></a></li>
                                <li class="nav-item"><a class="nav-link px-3" href="{{route('Home')}}"><span class="me-2 text-900" data-feather="pie-chart"></span>Dashboard</a></li>
                            </ul>
                        </div>
                        <div class="card-footer p-0 border-top">
                            <div class="px-3"> <a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="{{route('logout')}}"> <span class="me-2" data-feather="log-out"> </span>Cerrar sesi&oacute;n</a></div>
                            
                        </div>
                        </div>
                    </div>
                    </li>
                </ul>
                </div>
            </nav>
            <nav class="navbar navbar-top navbar-slim fixed-top navbar-expand" id="topNavSlim" style="display:none;">
                <div class="collapse navbar-collapse justify-content-between">
                <div class="navbar-logo">
                    <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
                    <a class="navbar-brand navbar-brand" href="index-2.html">phoenix <span class="text-1000 d-none d-sm-inline">slim</span></a>
                </div>
                <ul class="navbar-nav navbar-nav-icons flex-row">
                    <li class="nav-item">
                    <div class="theme-control-toggle fa-ion-wait pe-2 theme-control-toggle-slim"><input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="phoenixTheme" value="dark" /><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon me-1 d-none d-sm-block" data-feather="moon"></span><span class="fs--1 fw-bold">Dark</span></label><label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon me-1 d-none d-sm-block" data-feather="sun"></span><span class="fs--1 fw-bold">Light</span></label></div>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchBoxModal"><span data-feather="search" style="height:12px;width:12px;"></span></a></li>
                    <!--<li class="nav-item dropdown">
                        <a class="nav-link" id="navbarDropdownNotification" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span data-feather="bell" style="height:12px;width:12px;"></span></a>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border border-300 navbar-dropdown-caret" id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
                            <div class="card position-relative border-0">
                            <div class="card-header p-2">
                                <div class="d-flex justify-content-between">
                                <h5 class="text-black mb-0">Notificatons</h5><button class="btn btn-link p-0 fs--1 fw-normal" type="button">Mark all as read</button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="scrollbar-overlay" style="height: 27rem;">
                                <div class="border-300">
                                    <div class="px-2 px-sm-3 py-3 border-300 notification-card position-relative read border-bottom">
                                    <div class="d-flex align-items-center justify-content-between position-relative">
                                        <div class="d-flex">
                                        <div class="flex-1 me-sm-3">
                                            <h4 class="fs--1 text-black">Jessie Samson</h4>
                                            <p class="fs--1 text-1000 mb-2 mb-sm-3 fw-normal"><span class='me-1 fs--2'>💬</span>Mentioned you in a comment.<span class="ms-2 text-400 fw-bold fs--2">10m</span></p>
                                            <p class="text-800 fs--1 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">10:41 AM </span>August 7,2021</p>
                                        </div>
                                        </div>
                                        <div class="font-sans-serif d-none d-sm-block"><button class="btn fs--2 btn-sm dropdown-toggle dropdown-caret-none transition-none notification-dropdown-toggle" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs--2 text-900"></span></button>
                                        <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="px-2 px-sm-3 py-3 border-300 notification-card position-relative unread border-bottom">
                                    <div class="d-flex align-items-center justify-content-between position-relative">
                                        <div class="d-flex">
                                        <div class="avatar avatar-m status-online me-3">
                                            <div class="avatar-name rounded-circle"><span>J</span></div>
                                        </div>
                                        <div class="flex-1 me-sm-3">
                                            <h4 class="fs--1 text-black">Jane Foster</h4>
                                            <p class="fs--1 text-1000 mb-2 mb-sm-3 fw-normal"><span class='me-1 fs--2'>📅</span>Created an event.<span class="ms-2 text-400 fw-bold fs--2">20m</span></p>
                                            <p class="text-800 fs--1 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">10:20 AM </span>August 7,2021</p>
                                        </div>
                                        </div>
                                        <div class="font-sans-serif d-none d-sm-block"><button class="btn fs--2 btn-sm dropdown-toggle dropdown-caret-none transition-none notification-dropdown-toggle" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs--2 text-900"></span></button>
                                        <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="px-2 px-sm-3 py-3 border-300 notification-card position-relative unread border-bottom">
                                    <div class="d-flex align-items-center justify-content-between position-relative">
                                        <div class="d-flex">
                                        <div class="flex-1 me-sm-3">
                                            <h4 class="fs--1 text-black">Jessie Samson</h4>
                                            <p class="fs--1 text-1000 mb-2 mb-sm-3 fw-normal"><span class='me-1 fs--2'>👍</span>Liked your comment.<span class="ms-2 text-400 fw-bold fs--2">1h</span></p>
                                            <p class="text-800 fs--1 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">9:30 AM </span>August 7,2021</p>
                                        </div>
                                        </div>
                                        <div class="font-sans-serif d-none d-sm-block"><button class="btn fs--2 btn-sm dropdown-toggle dropdown-caret-none transition-none notification-dropdown-toggle" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs--2 text-900"></span></button>
                                        <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="border-300">
                                    <div class="px-2 px-sm-3 py-3 border-300 notification-card position-relative unread border-bottom">
                                    <div class="d-flex align-items-center justify-content-between position-relative">
                                        <div class="d-flex">
                                        <div class="avatar avatar-m status-online me-3"><img class="rounded-circle" src="{{asset('imagenes/user.png') }}" alt="" /></div>
                                        <div class="flex-1 me-sm-3">
                                            <h4 class="fs--1 text-black">Kiera Anderson</h4>
                                            <p class="fs--1 text-1000 mb-2 mb-sm-3 fw-normal"><span class='me-1 fs--2'>💬</span>Mentioned you in a comment.<span class="ms-2 text-400 fw-bold fs--2"></span></p>
                                            <p class="text-800 fs--1 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">9:11 AM </span>August 7,2021</p>
                                        </div>
                                        </div>
                                        <div class="font-sans-serif d-none d-sm-block"><button class="btn fs--2 btn-sm dropdown-toggle dropdown-caret-none transition-none notification-dropdown-toggle" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs--2 text-900"></span></button>
                                        <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="px-2 px-sm-3 py-3 border-300 notification-card position-relative unread border-bottom">
                                    <div class="d-flex align-items-center justify-content-between position-relative">
                                        <div class="d-flex">
                                        <div class="flex-1 me-sm-3">
                                            <h4 class="fs--1 text-black">Herman Carter</h4>
                                            <p class="fs--1 text-1000 mb-2 mb-sm-3 fw-normal"><span class='me-1 fs--2'>👤</span>Tagged you in a comment.<span class="ms-2 text-400 fw-bold fs--2"></span></p>
                                            <p class="text-800 fs--1 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">10:58 PM </span>August 7,2021</p>
                                        </div>
                                        </div>
                                        <div class="font-sans-serif d-none d-sm-block"><button class="btn fs--2 btn-sm dropdown-toggle dropdown-caret-none transition-none notification-dropdown-toggle" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs--2 text-900"></span></button>
                                        <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="px-2 px-sm-3 py-3 border-300 notification-card position-relative read ">
                                    <div class="d-flex align-items-center justify-content-between position-relative">
                                        <div class="d-flex">
                                        <div class="flex-1 me-sm-3">
                                            <h4 class="fs--1 text-black">Benjamin Button</h4>
                                            <p class="fs--1 text-1000 mb-2 mb-sm-3 fw-normal"><span class='me-1 fs--2'>👍</span>Liked your comment.<span class="ms-2 text-400 fw-bold fs--2"></span></p>
                                            <p class="text-800 fs--1 mb-0"><span class="me-1 fas fa-clock"></span><span class="fw-bold">10:18 AM </span>August 7,2021</p>
                                        </div>
                                        </div>
                                        <div class="font-sans-serif d-none d-sm-block"><button class="btn fs--2 btn-sm dropdown-toggle dropdown-caret-none transition-none notification-dropdown-toggle" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs--2 text-900"></span></button>
                                        <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">Mark as unread</a></div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="card-footer p-0 border-top border-0">
                                <div class="my-2 text-center fw-bold fs--2 text-600"><a class="fw-bolder" href="pages/notifications.html">Notification history</a></div>
                            </div>
                            </div>
                        </div>
                    </li>-->
                    <li class="nav-item dropdown">
                    <a class="nav-link" id="navbarDropdownNindeDots" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false"><svg width="10" height="10" viewbox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="2" cy="2" r="2" fill="currentColor"></circle>
                        <circle cx="2" cy="8" r="2" fill="currentColor"></circle>
                        <circle cx="2" cy="14" r="2" fill="currentColor"></circle>
                        <circle cx="8" cy="8" r="2" fill="currentColor"></circle>
                        <circle cx="8" cy="14" r="2" fill="currentColor"></circle>
                        <circle cx="14" cy="8" r="2" fill="currentColor"></circle>
                        <circle cx="14" cy="14" r="2" fill="currentColor"></circle>
                        <circle cx="8" cy="2" r="2" fill="currentColor"></circle>
                        <circle cx="14" cy="2" r="2" fill="currentColor"></circle>
                        </svg></a>
                    <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-nide-dots shadow border border-300" aria-labelledby="navbarDropdownNindeDots">
                        <div class="card bg-white position-relative border-0">
                        <div class="card-body pt-3 px-3 pb-0 overflow-auto scrollbar" style="height: 20rem;">
                           
                        </div>
                        </div>
                    </div>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0 white-space-nowrap" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false">Olivia <span class="fa-solid fa-chevron-down fs--2"></span></a>
                    <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border border-300" aria-labelledby="navbarDropdownUser">
                        <div class="card position-relative border-0">
                        <div class="card-body bg-white bg-white p-0">
                            <div class="text-center pt-4 pb-3">
                            <div class="avatar avatar-xl ">
                                <img class="rounded-circle " src="{{asset('imagenes/user.png') }}" alt="" />
                            </div>
                            @if (Auth::check())
                            <h6 class="mt-2 text-black">{{ Auth::user()->name }}</h6>
                            @endif
                            </div>
                            
                        <div class="overflow-auto scrollbar" style="height: 5rem;">
                            <ul class="nav d-flex flex-column mb-2 pb-1">
                            <li class="nav-item"><a class="nav-link px-3" href="#!"> <span class="me-2 text-900" data-feather="user"></span><span>Perfil</span></a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="{{route('Home')}}"><span class="me-2 text-900" data-feather="pie-chart"></span>Dashboard</a></li>
                            </ul>
                        </div>
                        <div class="card-footer p-0 border-top">
                            
                            <div class="px-3"> <a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="{{route('logout')}}"> <span class="me-2" data-feather="log-out"> </span>Cerrar sesi&oacute;n</a></div>
                            
                        </div>
                        </div>
                    </div>
                    </li>
                </ul>
                </div>
            </nav>
            <nav class="navbar navbar-top fixed-top navbar-expand-lg" id="navbarTop" style="display:none;">
            </nav>
            <nav class="navbar navbar-top navbar-slim justify-content-between fixed-top navbar-expand-lg" id="navbarTopSlim" style="display:none;">
            </nav>
            <nav class="navbar navbar-top fixed-top navbar-expand-lg" id="navbarCombo" data-navbar-top="combo" data-move-target="#navbarVerticalNav" style="display:none;">
            </nav>
            <nav class="navbar navbar-top fixed-top navbar-slim justify-content-between navbar-expand-lg" id="navbarComboSlim" data-navbar-top="combo" data-move-target="#navbarVerticalNav" style="display:none;">
            </nav>
            <script>
                var navbarTopShape = window.config.config.phoenixNavbarTopShape;
                var navbarPosition = window.config.config.phoenixNavbarPosition;
                var body = document.querySelector('body');
                var navbarDefault = document.querySelector('#navbarDefault');
                var navbarTop = document.querySelector('#navbarTop');
                var topNavSlim = document.querySelector('#topNavSlim');
                var navbarTopSlim = document.querySelector('#navbarTopSlim');
                var navbarCombo = document.querySelector('#navbarCombo');
                var navbarComboSlim = document.querySelector('#navbarComboSlim');
                var dualNav = document.querySelector('#dualNav');

                var documentElement = document.documentElement;
                var navbarVertical = document.querySelector('.navbar-vertical');

                if (navbarPosition === 'dual-nav') {
                topNavSlim.remove();
                navbarTop.remove();
                navbarVertical.remove();
                navbarTopSlim.remove();
                navbarCombo.remove();
                navbarComboSlim.remove();
                navbarDefault.remove();
                dualNav.removeAttribute('style');
                documentElement.classList.add('dual-nav');
                } else if (navbarTopShape === 'slim' && navbarPosition === 'vertical') {
                navbarDefault.remove();
                navbarTop.remove();
                navbarTopSlim.remove();
                navbarCombo.remove();
                navbarComboSlim.remove();
                topNavSlim.style.display = 'block';
                navbarVertical.style.display = 'inline-block';
                body.classList.add('nav-slim');
                } else if (navbarTopShape === 'slim' && navbarPosition === 'horizontal') {
                navbarDefault.remove();
                navbarVertical.remove();
                navbarTop.remove();
                topNavSlim.remove();
                navbarCombo.remove();
                navbarComboSlim.remove();
                navbarTopSlim.removeAttribute('style');
                body.classList.add('nav-slim');
                } else if (navbarTopShape === 'slim' && navbarPosition === 'combo') {
                navbarDefault.remove();
                //- navbarVertical.remove();
                navbarTop.remove();
                topNavSlim.remove();
                navbarCombo.remove();
                navbarTopSlim.remove();
                navbarComboSlim.removeAttribute('style');
                navbarVertical.removeAttribute('style');
                body.classList.add('nav-slim');
                } else if (navbarTopShape === 'default' && navbarPosition === 'horizontal') {
                navbarDefault.remove();
                topNavSlim.remove();
                navbarVertical.remove();
                navbarTopSlim.remove();
                navbarCombo.remove();
                navbarComboSlim.remove();
                navbarTop.removeAttribute('style');
                documentElement.classList.add('navbar-horizontal');
                } else if (navbarTopShape === 'default' && navbarPosition === 'combo') {
                topNavSlim.remove();
                navbarTop.remove();
                navbarTopSlim.remove();
                navbarDefault.remove();
                navbarComboSlim.remove();
                navbarCombo.removeAttribute('style');
                navbarVertical.removeAttribute('style');
                documentElement.classList.add('navbar-combo')

                } else {
                topNavSlim.remove();
                navbarTop.remove();
                navbarTopSlim.remove();
                navbarCombo.remove();
                navbarComboSlim.remove();
                navbarDefault.removeAttribute('style');
                navbarVertical.removeAttribute('style');
                }

                var navbarTopStyle = window.config.config.phoenixNavbarTopStyle;
                var navbarTop = document.querySelector('.navbar-top');
                if (navbarTopStyle === 'darker') {
                navbarTop.classList.add('navbar-darker');
                }

                var navbarVerticalStyle = window.config.config.phoenixNavbarVerticalStyle;
                var navbarVertical = document.querySelector('.navbar-vertical');
                if (navbarVerticalStyle === 'darker') {
                navbarVertical.classList.add('navbar-darker');
                }
            </script>
            <div class="content">
                @yield('content')
            </div>
        </main>
        <!-- ===============================================-->
        <!--MODAL SESION-->
        <div class="modal fade" id="ModalSesion" tabindex="-1" role="dialog" aria-labelledby="ModalSesionLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="ModalSesionexampleModalLabel">Tiempo de Sesi&oacute;n</h5>
                        <a class="btn" type="button" href="{{route('logout')}}" aria-label="Close" style="transform: scale(1.3)">
                        <span aria-hidden="true">&times;</span>
                        </a>
                    </div>
                    <div class="modal-body text-center">
                        <img class="p-0 m-0"  src="{{asset('imagenes/LoginSesion.png') }}" alt="Splittel" width="200" height="200">
                        <br>
                        Tú sesi&oacute;n esta por terminar <br> ¿Necesitas más tiempo para seguir trabajando?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="ActualizarSesion();">Confirmar</button>
                        <a type="button" class="btn btn-danger" href="{{route('logout')}}" >Salir</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- ===============================================-->
        <!--    JavaScripts-->
        <!-- ===============================================-->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{asset('menu2/vendors/popper/popper.min.js')}}"></script>
        <script src="{{asset('menu2/vendors/bootstrap/bootstrap.min.js')}}"></script>
        <script src="{{asset('menu2/vendors/anchorjs/anchor.min.js')}}"></script>
        <script src="{{asset('menu2/vendors/is/is.min.js')}}"></script>
        <script src="{{asset('menu2/vendors/fontawesome/all.min.js')}}"></script>
        <script src="{{asset('menu2/vendors/lodash/lodash.min.js')}}"></script>
        <!--<script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>-->
        {{--! Datatable --}}
        <script src="{{asset('js/DataTable/dataTables.min.js')}}"></script>
        <script src="{{asset('js/DataTable/dataTables.bootstrap5.min.js')}}"></script>
        <script src="{{asset('js/DataTable/dataTables.buttons.min.js')}}"></script>
        <script src="{{asset('js/DataTable/buttons.bootstrap5.min.js')}}"></script>
        <script src="{{asset('js/DataTable/buttons.html5.min.js')}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="{{asset('menu2/vendors/list.js/list.min.js')}}"></script>
        <script src="{{asset('menu2/vendors/feather-icons/feather.min.js')}}"></script>
        <script src="{{asset('menu2/vendors/dayjs/dayjs.min.js')}}"></script>
        <script src="{{asset('menu2/assets/js/phoenix.js')}}"></script>
        <script src="{{asset('menu2/vendors/echarts/echarts.min.js')}}"></script>
        <script src="{{asset('menu2/vendors/leaflet/leaflet.js')}}"></script>
        <script src="{{asset('menu2/vendors/leaflet.markercluster/leaflet.markercluster.js')}}"></script>
        <script src="{{asset('menu2/vendors/leaflet.tilelayer.colorfilter/leaflet-tilelayer-colorfilter.min.js')}}"></script>
        <script src="{{asset('menu2/assets/js/ecommerce-dashboard.js')}}"></script>
        <script src="{{ asset('js/funciones/Funciones.js') }}"></script>
        <script>
            // Configuración global del token CSRF para todas las solicitudes AJAX
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            //localStorage.setItem('Sessioniniciada', 'true');
            });
            //Cada que se ingresa a una pagina se eejecuta cada 2 horas para comprobar la sesion del usuarios
            setInterval(function() {
                $('#ModalSesion').modal('show');
            }, 7200000);
            //Actualiza la sesion del usuario
            function ActualizarSesion(){
                $.get('{{route("UpdateSession")}}', function(data) {
                    if (!data) {
                        window.location.href = "{{route('login')}}";
                    }else{
                        $('#ModalSesion').modal('hide');
                    }
                });
            }
            window.onpageshow = function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            };
            window.addEventListener('storage', (event) => {
                if (event.key === 'userLoggedIn' && event.newValue === 'true') {
                    // Recarga la página
                    location.reload();
                }
            });
            function login() {
                localStorage.setItem('userLoggedIn', 'true');
            }
        </script>

        <!--LINKS A BORRAR-->
            <!--<script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.min.js"></script>-->
            <!-- Botones Datatable-->
            {{--<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">--}}
            <!--<script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.bootstrap5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>-->
        <!--END-->

        @yield('scripts')
    </body>

</html>