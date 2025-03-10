<!DOCTYPE html>
<html lang="en-US" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- ===============================================-->
        <!--    Document Title-->
        <!-- ===============================================-->
        <title>@yield('title', 'splittel')</title>
        <!-- ===============================================-->
        <!--    Favicons-->
        <!-- ===============================================-->
        <!--<link rel="shortcut icon" type="image/x-icon" href="{{asset('imagenes/splittel.png') }}">-->
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
        <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.min.css">
        <link href="{{asset('menu2/assets/css/theme-rtl.min.css')}}" type="text/css" rel="stylesheet" id="style-rtl">
        <link href="{{asset('menu2/assets/css/theme.min.css')}}" type="text/css" rel="stylesheet" id="style-default">
        <link href="{{asset('menu2/assets/css/user-rtl.min.css')}}" type="text/css" rel="stylesheet" id="user-style-rtl">
        <link href="{{asset('menu2/assets/css/user.min.css')}}" type="text/css" rel="stylesheet" id="user-style-default">
        @yield('styles')
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
        <!--    Main Content-->
        <!-- ===============================================-->
        <main class="main" id="top">
            <!--Menu Izquerda--->
            <nav class="navbar navbar-vertical navbar-expand-lg" style="display:none;">
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
                                <a class="nav-link label-1 {{ Route::is('Home') ? 'active' : '' }}" href="{{route('Home')}}" role="button" data-bs-toggle="" aria-expanded="false">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="pie-chart"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Dashboard</span></span></div>
                                </a>
                            </div>
                        </li>
                        <li class="nav-item"><!-- Progreso-->
                            <p class="navbar-vertical-label">Progreso</p>
                            <hr class="navbar-vertical-line" /><!-- parent pages-->
                            <div class="nav-item-wrapper">
                                <a class="nav-link label-1 {{ Route::is('Busquedas.OV') ? 'active' : '' }}" href="{{ route('Busquedas.OV') }}" role="button" data-bs-toggle="" aria-expanded="false">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="bar-chart-2"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Progreso</span></span></div>
                                </a>
                            </div>
                        </li>
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
                                        <li class="nav-item"><a class="nav-link {{ Route::is('Planeacion') ? 'nav-tabs active' : '' }}" href="{{route('Planeacion')}}" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-text">Planeaci&oacute;n</span></div>
                                            </a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link {{ Route::is('corte.index') ? 'nav-tabs active' : '' }}" href="{{route('corte.index')}}" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-text">Corte</span></div>
                                            </a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link {{ Route::is('Suministro') ? 'nav-tabs active' : '' }}" href="{{route('Suministro')}}" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-text">Suministro</span></div>
                                            </a>
                                            <hr class="p-0 m-1">
                                        </li>
                                        <li class="nav-item"><a class="nav-link {{ Route::is('Preparado') ? 'nav-tabs active' : '' }}" href="{{route('Preparado')}}" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-text">Preparado</span></div>
                                            </a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link {{ Route::is('Ensamble') ? 'nav-tabs active' : '' }}" href="{{route('Ensamble')}}" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-text">Ensamble</span></div>
                                            </a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link {{ Route::is('Pulido') ? 'nav-tabs active' : '' }}" href="{{route('Pulido')}}" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-text">Pulido</span></div>
                                            </a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link {{ Route::is('Medicion') ? 'nav-tabs active' : '' }}" href="{{route('Medicion')}}" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-text">Medici&oacute;n</span></div>
                                            </a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link {{ Route::is('Visualizacion') ? 'nav-tabs active' : '' }}" href="{{route('Visualizacion')}}" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-text">Visualizaci&oacute;n</span></div>
                                            </a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link {{ Route::is('Empacado') ? 'nav-tabs active' : '' }}" href="{{route('Empacado')}}" data-bs-toggle="" aria-expanded="false">
                                            <div class="d-flex align-items-center"><span class="nav-link-text">Empaquetado</span></div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item"><!-- usuarios-->
                            <p class="navbar-vertical-label">Usuarios</p>
                            <hr class="navbar-vertical-line" /><!-- parent pages-->
                            <div class="nav-item-wrapper">
                                <a class="nav-link label-1 {{ Route::is('registro.index') ? 'active' : '' }}" href="{{route('registro.index')}}" role="button" data-bs-toggle="" aria-expanded="false">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="user"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Usuarios</span></span></div>
                                </a>
                            </div>
                        </li>
                        <li class="nav-item"><!-- Roles y permisos-->
                            <div class="nav-item-wrapper">
                                <a class="nav-link label-1 {{ Route::is('RolesPermisos.index') ? 'active' : '' }}" href="{{route('RolesPermisos.index')}}" role="button" data-bs-toggle="" aria-expanded="false">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="key"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Roles y permisos</span></span></div>
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
                    <a class="navbar-brand m-0 ">
                        <div class="d-flex align-items-start">
                            <div class="d-flex align-items-center">
                                <a class="navbar-brand p-0" href="./">
                                    <img src="{{asset('imagenes/splittel.png') }}" alt="Splittel" width="100" height="40">
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
                    <div class="theme-control-toggle fa-icon-wait px-2"><input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" /><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="moon"></span></label><label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="sun"></span></label></div>
                    </li>
                    <li class="nav-item dropdown">
                    <a class="nav-link" href="#" style="min-width: 2.5rem" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span data-feather="bell" style="height:20px;width:20px;"></span></a>
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
                    <li class="nav-item dropdown">
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
                    </li>
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
                <div class="navbar-logo">
                <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTopCollapse" aria-controls="navbarTopCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
                <a class="navbar-brand me-1 me-sm-3" href="index-2.html">
                    <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center"><a class="navbar-brand ps-3" href="./"><img src="{{asset('imagenes/splittel.png') }}" alt="Logo" width="100" height="40"></a>
                        
                    </div>
                    </div>
                </a>
                </div>
                <div class="collapse navbar-collapse navbar-top-collapse order-1 order-lg-0 justify-content-center" id="navbarTopCollapse">
                    <ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
                        <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-chart-pie"></span>Home1</a>
                        <ul class="dropdown-menu navbar-dropdown-caret">
                            <li><a class="dropdown-item active" href="index-2.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="shopping-cart"></span>E commerce</div>
                            </a></li>
                            <li><a class="dropdown-item" href="dashboard/project-management.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="clipboard"></span>Project management</div>
                            </a></li>
                            <li><a class="dropdown-item" href="dashboard/crm.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="phone"></span>CRM</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/social/feed.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="share-2"></span>Social feed</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-cube"></span>Apps</a>
                        <ul class="dropdown-menu navbar-dropdown-caret">
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="e-commerce" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="shopping-cart"></span>E commerce</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="admin" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Admin</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="apps/e-commerce/admin/add-product.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Add product</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/admin/products.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Products</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/admin/customers.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Customers</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/admin/customer-details.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Customer details</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/admin/orders.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Orders</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/admin/order-details.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Order details</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/admin/refund.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Refund</div>
                                    </a></li>
                                </ul>
                                </li>
                                <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="customer" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Customer</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="apps/e-commerce/landing/homepage.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Homepage</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Product details</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/landing/products-filter.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Products filter</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/landing/cart.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Cart</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/landing/checkout.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Checkout</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/landing/shipping-info.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Shipping info</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/landing/profile.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Profile</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/landing/favourite-stores.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Favourite stores</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/landing/wishlist.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Wishlist</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/landing/order-tracking.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Order tracking</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="apps/e-commerce/landing/invoice.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Invoice</div>
                                    </a></li>
                                </ul>
                                </li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="CRM" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="phone"></span>CRM</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/crm/analytics.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Analytics</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/crm/deals.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Deals</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/crm/deal-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Deal details</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/crm/leads.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Leads</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/crm/lead-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lead details</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/crm/reports.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reports</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/crm/reports-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reports details</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/crm/add-contact.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Add contact</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="project-management" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="clipboard"></span>Project management</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/project-management/create-new.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create new</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/project-management/project-list-view.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project list view</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/project-management/project-card-view.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project card view</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/project-management/project-board-view.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project board view</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/project-management/todo-list.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Todo list</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/project-management/project-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project details</div>
                                </a></li>
                            </ul>
                            </li>
                            <li><a class="dropdown-item" href="apps/chat.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="message-square"></span>Chat</div>
                            </a></li>
                            <!--<li class="dropdown"><a class="dropdown-item dropdown-toggle" id="email" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="mail"></span>Email</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/email/inbox.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Inbox</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/email/email-detail.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Email detail</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/email/compose.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Compose</div>
                                </a></li>
                            </ul>-->
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="events" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="bookmark"></span>Events</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/events/create-an-event.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create an event</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/events/event-detail.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Event detail</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="kanban" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="trello"></span>Kanban</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/kanban/kanban.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Kanban</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/kanban/boards.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Boards</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/kanban/create-kanban-board.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create board</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="social" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="share-2"></span>Social</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/social/profile.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Profile</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/social/settings.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Settings</div>
                                </a></li>
                            </ul>
                            </li>
                            <li><a class="dropdown-item" href="apps/calendar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="calendar"></span>Calendar</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-files-landscapes-alt"></span>Pages</a>
                        <ul class="dropdown-menu navbar-dropdown-caret">
                            <li><a class="dropdown-item" href="pages/starter.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="compass"></span>Starter</div>
                            </a></li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="faq" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="help-circle"></span>Faq</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/faq/faq-accordion.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Faq accordion</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/faq/faq-tab.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Faq tab</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="landing" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="globe"></span>Landing</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/landing/default.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Default</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/landing/alternate.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Alternate</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="pricing" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="tag"></span>Pricing</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/pricing/pricing-column.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Pricing column</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/pricing/pricing-grid.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Pricing grid</div>
                                </a></li>
                            </ul>
                            </li>
                            <li><a class="dropdown-item" href="pages/notifications.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="bell"></span>Notifications</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/members.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="users"></span>Members</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/timeline.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="clock"></span>Timeline</div>
                            </a></li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="errors" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="alert-triangle"></span>Errors</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/errors/404.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>404</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/errors/403.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>403</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/errors/500.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>500</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="authentication" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="lock"></span>Authentication</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="simple" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Simple</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="pages/authentication/simple/sign-in.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/simple/sign-up.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/simple/sign-out.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/simple/forgot-password.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/simple/reset-password.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/simple/lock-screen.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/simple/2FA.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                    </a></li>
                                </ul>
                                </li>
                                <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="split" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Split</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="pages/authentication/split/sign-in.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/split/sign-up.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/split/sign-out.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/split/forgot-password.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/split/reset-password.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/split/lock-screen.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/split/2FA.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                    </a></li>
                                </ul>
                                </li>
                                <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="Card" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Card</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="pages/authentication/card/sign-in.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/card/sign-up.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/card/sign-out.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/card/forgot-password.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/card/reset-password.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/card/lock-screen.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/card/2FA.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                    </a></li>
                                </ul>
                                </li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="layouts" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="layout"></span>Layouts</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="demo/vertical-sidenav.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Vertical sidenav</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/dark-mode.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dark mode</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/sidenav-collapse.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sidenav collapse</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/darknav.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Darknav</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/topnav-slim.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Topnav slim</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/navbar-top-slim.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Navbar top slim</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/navbar-top.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Navbar top</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/horizontal-slim.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Horizontal slim</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/combo-nav.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo nav</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/combo-nav-slim.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo nav slim</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/dual-nav.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dual nav</div>
                                </a></li>
                            </ul>
                            </li>
                        </ul>
                        </li>
                        <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-puzzle-piece"></span>Modules</a>
                        <ul class="dropdown-menu navbar-dropdown-caret dropdown-menu-card py-0">
                            <div class="border-0 scrollbar" style="max-height: 60vh;">
                            <div class="px-3 pt-4 pb-3 img-dropdown">
                                <div class="row gx-4 gy-5">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="dropdown-item-group"><span class="me-2" data-feather="file-text" style="stroke-width:2;"></span>
                                    <h6 class="dropdown-item-title">Forms</h6>
                                    </div><a class="dropdown-link" href="modules/forms/basic/form-control.html">Form control</a><a class="dropdown-link" href="modules/forms/basic/input-group.html">Input group</a><a class="dropdown-link" href="modules/forms/basic/select.html">Select</a><a class="dropdown-link" href="modules/forms/basic/checks.html">Checks</a><a class="dropdown-link" href="modules/forms/basic/range.html">Range</a><a class="dropdown-link" href="modules/forms/basic/floating-labels.html">Floating labels</a><a class="dropdown-link" href="modules/forms/basic/layout.html">Layout</a><a class="dropdown-link" href="modules/forms/advance/advance-select.html">Advance select</a><a class="dropdown-link" href="modules/forms/advance/date-picker.html">Date picker</a><a class="dropdown-link" href="modules/forms/advance/editor.html">Editor</a><a class="dropdown-link" href="modules/forms/advance/file-uploader.html">File uploader</a><a class="dropdown-link" href="modules/forms/advance/rating.html">Rating</a><a class="dropdown-link" href="modules/forms/advance/emoji-button.html">Emoji button</a><a class="dropdown-link" href="modules/forms/validation.html">Validation</a><a class="dropdown-link" href="modules/forms/wizard.html">Wizard</a>
                                    <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="grid" style="stroke-width:2;"></span>
                                    <h6 class="dropdown-item-title">Icons</h6>
                                    </div><a class="dropdown-link" href="modules/icons/feather.html">Feather</a><a class="dropdown-link" href="modules/icons/font-awesome.html">Font awesome</a><a class="dropdown-link" href="modules/icons/unicons.html">Unicons</a>
                                    <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="bar-chart-2" style="stroke-width:2;"></span>
                                    <h6 class="dropdown-item-title">ECharts</h6>
                                    </div><a class="dropdown-link" href="modules/echarts/line-charts.html">Line charts</a><a class="dropdown-link" href="modules/echarts/bar-charts.html">Bar charts</a><a class="dropdown-link" href="modules/echarts/candlestick-charts.html">Candlestick charts</a><a class="dropdown-link" href="modules/echarts/geo-map.html">Geo map</a><a class="dropdown-link" href="modules/echarts/scatter-charts.html">Scatter charts</a><a class="dropdown-link" href="modules/echarts/pie-charts.html">Pie charts</a><a class="dropdown-link" href="modules/echarts/gauge-chart.html">Gauge chart</a><a class="dropdown-link" href="modules/echarts/radar-charts.html">Radar charts</a><a class="dropdown-link" href="modules/echarts/heatmap-charts.html">Heatmap charts</a><a class="dropdown-link" href="modules/echarts/how-to-use.html">How to use</a>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="dropdown-item-group"><span class="me-2" data-feather="package" style="stroke-width:2;"></span>
                                    <h6 class="dropdown-item-title">Components</h6>
                                    </div><a class="dropdown-link" href="modules/components/accordion.html">Accordion</a><a class="dropdown-link" href="modules/components/avatar.html">Avatar</a><a class="dropdown-link" href="modules/components/alerts.html">Alerts</a><a class="dropdown-link" href="modules/components/badge.html">Badge</a><a class="dropdown-link" href="modules/components/breadcrumb.html">Breadcrumb</a><a class="dropdown-link" href="modules/components/button.html">Buttons</a><a class="dropdown-link" href="modules/components/calendar.html">Calendar</a><a class="dropdown-link" href="modules/components/card.html">Card</a><a class="dropdown-link" href="modules/components/carousel/bootstrap.html">Bootstrap</a><a class="dropdown-link" href="modules/components/carousel/swiper.html">Swiper</a><a class="dropdown-link" href="modules/components/collapse.html">Collapse</a><a class="dropdown-link" href="modules/components/dropdown.html">Dropdown</a><a class="dropdown-link" href="modules/components/list-group.html">List group</a><a class="dropdown-link" href="modules/components/modal.html">Modals</a><a class="dropdown-link" href="modules/components/navs-and-tabs/navs.html">Navs</a><a class="dropdown-link" href="modules/components/navs-and-tabs/navbar.html">Navbar</a><a class="dropdown-link" href="modules/components/navs-and-tabs/tabs.html">Tabs</a><a class="dropdown-link" href="modules/components/offcanvas.html">Offcanvas</a><a class="dropdown-link" href="modules/components/progress-bar.html">Progress bar</a><a class="dropdown-link" href="modules/components/placeholder.html">Placeholder</a><a class="dropdown-link" href="modules/components/pagination.html">Pagination</a><a class="dropdown-link" href="modules/components/popovers.html">Popovers</a><a class="dropdown-link" href="modules/components/scrollspy.html">Scrollspy</a><a class="dropdown-link" href="modules/components/sortable.html">Sortable</a><a class="dropdown-link" href="modules/components/spinners.html">Spinners</a><a class="dropdown-link" href="modules/components/toast.html">Toast</a><a class="dropdown-link" href="modules/components/tooltips.html">Tooltips</a><a class="dropdown-link" href="modules/components/chat-widget.html">Chat widget</a>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="dropdown-item-group"><span class="me-2" data-feather="columns" style="stroke-width:2;"></span>
                                    <h6 class="dropdown-item-title">Tables</h6>
                                    </div><a class="dropdown-link" href="modules/tables/basic-tables.html">Basic tables</a><a class="dropdown-link" href="modules/tables/advance-tables.html">Advance tables</a><a class="dropdown-link" href="modules/tables/bulk-select.html">Bulk Select</a>
                                    <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="tool" style="stroke-width:2;"></span>
                                    <h6 class="dropdown-item-title">Utilities</h6>
                                    </div><a class="dropdown-link" href="modules/utilities/background.html">Background</a><a class="dropdown-link" href="modules/utilities/borders.html">Borders</a><a class="dropdown-link" href="modules/utilities/colors.html">Colors</a><a class="dropdown-link" href="modules/utilities/display.html">Display</a><a class="dropdown-link" href="modules/utilities/flex.html">Flex</a><a class="dropdown-link" href="modules/utilities/stacks.html">Stacks</a><a class="dropdown-link" href="modules/utilities/float.html">Float</a><a class="dropdown-link" href="modules/utilities/grid.html">Grid</a><a class="dropdown-link" href="modules/utilities/interactions.html">Interactions</a><a class="dropdown-link" href="modules/utilities/opacity.html">Opacity</a><a class="dropdown-link" href="modules/utilities/overflow.html">Overflow</a><a class="dropdown-link" href="modules/utilities/position.html">Position</a><a class="dropdown-link" href="modules/utilities/shadows.html">Shadows</a><a class="dropdown-link" href="modules/utilities/sizing.html">Sizing</a><a class="dropdown-link" href="modules/utilities/spacing.html">Spacing</a><a class="dropdown-link" href="modules/utilities/typography.html">Typography</a><a class="dropdown-link" href="modules/utilities/vertical-align.html">Vertical align</a><a class="dropdown-link" href="modules/utilities/visibility.html">Visibility</a>
                                </div>
                                </div>
                            </div>
                            </div>
                        </ul>
                        </li>
                        <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-document-layout-right"></span>Documentation</a>
                        <ul class="dropdown-menu navbar-dropdown-caret">
                            <li><a class="dropdown-item" href="documentation/getting-started.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="life-buoy"></span>Getting started</div>
                            </a></li>
                            <li class="dropdown dropdown-inside"><a class="dropdown-item dropdown-toggle" id="customization" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="settings"></span>Customization</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="documentation/customization/configuration.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Configuration</div>
                                </a></li>
                                <li><a class="dropdown-item" href="documentation/customization/styling.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Styling</div>
                                </a></li>
                                <li><a class="dropdown-item" href="documentation/customization/dark-mode.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dark mode</div>
                                </a></li>
                                <li><a class="dropdown-item" href="documentation/customization/plugin.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Plugin</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown dropdown-inside"><a class="dropdown-item dropdown-toggle" id="layouts-doc" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="table"></span>Layouts doc</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="documentation/layouts/vertical-navbar.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Vertical navbar</div>
                                </a></li>
                                <li><a class="dropdown-item" href="documentation/layouts/horizontal-navbar.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Horizontal navbar</div>
                                </a></li>
                                <li><a class="dropdown-item" href="documentation/layouts/combo-navbar.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo navbar</div>
                                </a></li>
                                <li><a class="dropdown-item" href="documentation/layouts/dual-nav.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dual nav</div>
                                </a></li>
                            </ul>
                            </li>
                            <li><a class="dropdown-item" href="documentation/gulp.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 fa-brands fa-gulp ms-1 me-1 fa-lg"></span>Gulp</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/design-file.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="figma"></span>Design file</div>
                            </a></li>
                            <li><a class="dropdown-item" href="changelog.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="git-merge"></span>Changelog</div>
                            </a></li>
                            <li><a class="dropdown-item" href="showcase.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="monitor"></span>Showcase</div>
                            </a></li>
                        </ul>
                        </li>
                    </ul>
                </div>
                <ul class="navbar-nav navbar-nav-icons flex-row">
                <li class="nav-item">
                    <div class="theme-control-toggle fa-icon-wait px-2"><input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" /><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="moon"></span></label><label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="sun"></span></label></div>
                </li>
                <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchBoxModal"><span data-feather="search" style="height:19px;width:19px;margin-bottom: 2px;"></span></a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" style="min-width: 2.5rem" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span data-feather="bell" style="height:20px;width:20px;"></span></a>
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
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" id="navbarDropdownNindeDots" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false"><svg width="16" height="16" viewbox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                        <div class="row text-center align-items-center gx-0 gy-0">
                            
                        </div>
                        </div>
                    </div>
                    </div>
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
            </nav>
            <nav class="navbar navbar-top navbar-slim justify-content-between fixed-top navbar-expand-lg" id="navbarTopSlim" style="display:none;">
                <div class="navbar-logo">
                <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTopCollapse" aria-controls="navbarTopCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
                <a class="navbar-brand navbar-brand" href="index-2.html">phoenix <span class="text-1000 d-none d-sm-inline">slim</span></a>
                </div>
                <div class="collapse navbar-collapse navbar-top-collapse order-1 order-lg-0 justify-content-center" id="navbarTopCollapse">
                <ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-chart-pie"></span>Home1</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item active" href="index-2.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="shopping-cart"></span>E commerce</div>
                        </a></li>
                        <li><a class="dropdown-item" href="dashboard/project-management.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="clipboard"></span>Project management</div>
                        </a></li>
                        <li><a class="dropdown-item" href="dashboard/crm.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="phone"></span>CRM</div>
                        </a></li>
                        <li><a class="dropdown-item" href="apps/social/feed.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="share-2"></span>Social feed</div>
                        </a></li>
                    </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-cube"></span>Apps</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="e-commerce" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="shopping-cart"></span>E commerce</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="admin" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Admin</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/add-product.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Add product</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/products.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Products</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/customers.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Customers</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/customer-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Customer details</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/orders.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Orders</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/order-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Order details</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/refund.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Refund</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="customer" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Customer</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/homepage.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Homepage</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Product details</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/products-filter.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Products filter</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/cart.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Cart</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/checkout.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Checkout</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/shipping-info.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Shipping info</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/profile.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Profile</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/favourite-stores.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Favourite stores</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/wishlist.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Wishlist</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/order-tracking.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Order tracking</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/invoice.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Invoice</div>
                                </a></li>
                            </ul>
                            </li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="CRM" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="phone"></span>CRM</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/crm/analytics.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Analytics</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/deals.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Deals</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/deal-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Deal details</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/leads.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Leads</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/lead-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lead details</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/reports.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reports</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/reports-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reports details</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/add-contact.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Add contact</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="project-management" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="clipboard"></span>Project management</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/project-management/create-new.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create new</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-list-view.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project list view</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-card-view.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project card view</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-board-view.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project board view</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/todo-list.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Todo list</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project details</div>
                            </a></li>
                        </ul>
                        </li>
                        <li><a class="dropdown-item" href="apps/chat.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="message-square"></span>Chat</div>
                        </a></li>
                        <!--
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="email" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="mail"></span>Email</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/email/inbox.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Inbox</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/email/email-detail.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Email detail</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/email/compose.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Compose</div>
                            </a></li>
                        </ul>-->
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="events" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="bookmark"></span>Events</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/events/create-an-event.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create an event</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/events/event-detail.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Event detail</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="kanban" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="trello"></span>Kanban</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/kanban/kanban.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Kanban</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/kanban/boards.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Boards</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/kanban/create-kanban-board.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create board</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="social" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="share-2"></span>Social</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/social/profile.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Profile</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/social/settings.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Settings</div>
                            </a></li>
                        </ul>
                        </li>
                        <li><a class="dropdown-item" href="apps/calendar.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="calendar"></span>Calendar</div>
                        </a></li>
                    </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-files-landscapes-alt"></span>Pages</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item" href="pages/starter.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="compass"></span>Starter</div>
                        </a></li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="faq" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="help-circle"></span>Faq</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/faq/faq-accordion.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Faq accordion</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/faq/faq-tab.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Faq tab</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="landing" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="globe"></span>Landing</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/landing/default.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Default</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/landing/alternate.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Alternate</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="pricing" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="tag"></span>Pricing</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/pricing/pricing-column.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Pricing column</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/pricing/pricing-grid.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Pricing grid</div>
                            </a></li>
                        </ul>
                        </li>
                        <li><a class="dropdown-item" href="pages/notifications.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="bell"></span>Notifications</div>
                        </a></li>
                        <li><a class="dropdown-item" href="pages/members.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="users"></span>Members</div>
                        </a></li>
                        <li><a class="dropdown-item" href="pages/timeline.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="clock"></span>Timeline</div>
                        </a></li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="errors" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="alert-triangle"></span>Errors</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/errors/404.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>404</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/errors/403.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>403</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/errors/500.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>500</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="authentication" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="lock"></span>Authentication</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="simple" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Simple</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/authentication/simple/sign-in.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/sign-up.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/sign-out.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/forgot-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/reset-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/lock-screen.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/2FA.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="split" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Split</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/authentication/split/sign-in.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/sign-up.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/sign-out.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/forgot-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/reset-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/lock-screen.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/2FA.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="Card" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Card</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/authentication/card/sign-in.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/sign-up.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/sign-out.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/forgot-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/reset-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/lock-screen.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/2FA.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                </a></li>
                            </ul>
                            </li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="layouts" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="layout"></span>Layouts</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="demo/vertical-sidenav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Vertical sidenav</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/dark-mode.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dark mode</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/sidenav-collapse.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sidenav collapse</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/darknav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Darknav</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/topnav-slim.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Topnav slim</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/navbar-top-slim.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Navbar top slim</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/navbar-top.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Navbar top</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/horizontal-slim.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Horizontal slim</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/combo-nav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo nav</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/combo-nav-slim.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo nav slim</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/dual-nav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dual nav</div>
                            </a></li>
                        </ul>
                        </li>
                    </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-puzzle-piece"></span>Modules</a>
                    <ul class="dropdown-menu navbar-dropdown-caret dropdown-menu-card py-0">
                        <div class="border-0 scrollbar" style="max-height: 60vh;">
                        <div class="px-3 pt-4 pb-3 img-dropdown">
                            <div class="row gx-4 gy-5">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="dropdown-item-group"><span class="me-2" data-feather="file-text" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Forms</h6>
                                </div><a class="dropdown-link" href="modules/forms/basic/form-control.html">Form control</a><a class="dropdown-link" href="modules/forms/basic/input-group.html">Input group</a><a class="dropdown-link" href="modules/forms/basic/select.html">Select</a><a class="dropdown-link" href="modules/forms/basic/checks.html">Checks</a><a class="dropdown-link" href="modules/forms/basic/range.html">Range</a><a class="dropdown-link" href="modules/forms/basic/floating-labels.html">Floating labels</a><a class="dropdown-link" href="modules/forms/basic/layout.html">Layout</a><a class="dropdown-link" href="modules/forms/advance/advance-select.html">Advance select</a><a class="dropdown-link" href="modules/forms/advance/date-picker.html">Date picker</a><a class="dropdown-link" href="modules/forms/advance/editor.html">Editor</a><a class="dropdown-link" href="modules/forms/advance/file-uploader.html">File uploader</a><a class="dropdown-link" href="modules/forms/advance/rating.html">Rating</a><a class="dropdown-link" href="modules/forms/advance/emoji-button.html">Emoji button</a><a class="dropdown-link" href="modules/forms/validation.html">Validation</a><a class="dropdown-link" href="modules/forms/wizard.html">Wizard</a>
                                <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="grid" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Icons</h6>
                                </div><a class="dropdown-link" href="modules/icons/feather.html">Feather</a><a class="dropdown-link" href="modules/icons/font-awesome.html">Font awesome</a><a class="dropdown-link" href="modules/icons/unicons.html">Unicons</a>
                                <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="bar-chart-2" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">ECharts</h6>
                                </div><a class="dropdown-link" href="modules/echarts/line-charts.html">Line charts</a><a class="dropdown-link" href="modules/echarts/bar-charts.html">Bar charts</a><a class="dropdown-link" href="modules/echarts/candlestick-charts.html">Candlestick charts</a><a class="dropdown-link" href="modules/echarts/geo-map.html">Geo map</a><a class="dropdown-link" href="modules/echarts/scatter-charts.html">Scatter charts</a><a class="dropdown-link" href="modules/echarts/pie-charts.html">Pie charts</a><a class="dropdown-link" href="modules/echarts/gauge-chart.html">Gauge chart</a><a class="dropdown-link" href="modules/echarts/radar-charts.html">Radar charts</a><a class="dropdown-link" href="modules/echarts/heatmap-charts.html">Heatmap charts</a><a class="dropdown-link" href="modules/echarts/how-to-use.html">How to use</a>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="dropdown-item-group"><span class="me-2" data-feather="package" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Components</h6>
                                </div><a class="dropdown-link" href="modules/components/accordion.html">Accordion</a><a class="dropdown-link" href="modules/components/avatar.html">Avatar</a><a class="dropdown-link" href="modules/components/alerts.html">Alerts</a><a class="dropdown-link" href="modules/components/badge.html">Badge</a><a class="dropdown-link" href="modules/components/breadcrumb.html">Breadcrumb</a><a class="dropdown-link" href="modules/components/button.html">Buttons</a><a class="dropdown-link" href="modules/components/calendar.html">Calendar</a><a class="dropdown-link" href="modules/components/card.html">Card</a><a class="dropdown-link" href="modules/components/carousel/bootstrap.html">Bootstrap</a><a class="dropdown-link" href="modules/components/carousel/swiper.html">Swiper</a><a class="dropdown-link" href="modules/components/collapse.html">Collapse</a><a class="dropdown-link" href="modules/components/dropdown.html">Dropdown</a><a class="dropdown-link" href="modules/components/list-group.html">List group</a><a class="dropdown-link" href="modules/components/modal.html">Modals</a><a class="dropdown-link" href="modules/components/navs-and-tabs/navs.html">Navs</a><a class="dropdown-link" href="modules/components/navs-and-tabs/navbar.html">Navbar</a><a class="dropdown-link" href="modules/components/navs-and-tabs/tabs.html">Tabs</a><a class="dropdown-link" href="modules/components/offcanvas.html">Offcanvas</a><a class="dropdown-link" href="modules/components/progress-bar.html">Progress bar</a><a class="dropdown-link" href="modules/components/placeholder.html">Placeholder</a><a class="dropdown-link" href="modules/components/pagination.html">Pagination</a><a class="dropdown-link" href="modules/components/popovers.html">Popovers</a><a class="dropdown-link" href="modules/components/scrollspy.html">Scrollspy</a><a class="dropdown-link" href="modules/components/sortable.html">Sortable</a><a class="dropdown-link" href="modules/components/spinners.html">Spinners</a><a class="dropdown-link" href="modules/components/toast.html">Toast</a><a class="dropdown-link" href="modules/components/tooltips.html">Tooltips</a><a class="dropdown-link" href="modules/components/chat-widget.html">Chat widget</a>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="dropdown-item-group"><span class="me-2" data-feather="columns" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Tables</h6>
                                </div><a class="dropdown-link" href="modules/tables/basic-tables.html">Basic tables</a><a class="dropdown-link" href="modules/tables/advance-tables.html">Advance tables</a><a class="dropdown-link" href="modules/tables/bulk-select.html">Bulk Select</a>
                                <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="tool" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Utilities</h6>
                                </div><a class="dropdown-link" href="modules/utilities/background.html">Background</a><a class="dropdown-link" href="modules/utilities/borders.html">Borders</a><a class="dropdown-link" href="modules/utilities/colors.html">Colors</a><a class="dropdown-link" href="modules/utilities/display.html">Display</a><a class="dropdown-link" href="modules/utilities/flex.html">Flex</a><a class="dropdown-link" href="modules/utilities/stacks.html">Stacks</a><a class="dropdown-link" href="modules/utilities/float.html">Float</a><a class="dropdown-link" href="modules/utilities/grid.html">Grid</a><a class="dropdown-link" href="modules/utilities/interactions.html">Interactions</a><a class="dropdown-link" href="modules/utilities/opacity.html">Opacity</a><a class="dropdown-link" href="modules/utilities/overflow.html">Overflow</a><a class="dropdown-link" href="modules/utilities/position.html">Position</a><a class="dropdown-link" href="modules/utilities/shadows.html">Shadows</a><a class="dropdown-link" href="modules/utilities/sizing.html">Sizing</a><a class="dropdown-link" href="modules/utilities/spacing.html">Spacing</a><a class="dropdown-link" href="modules/utilities/typography.html">Typography</a><a class="dropdown-link" href="modules/utilities/vertical-align.html">Vertical align</a><a class="dropdown-link" href="modules/utilities/visibility.html">Visibility</a>
                            </div>
                            </div>
                        </div>
                        </div>
                    </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-document-layout-right"></span>Documentation</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item" href="documentation/getting-started.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="life-buoy"></span>Getting started</div>
                        </a></li>
                        <li class="dropdown dropdown-inside"><a class="dropdown-item dropdown-toggle" id="customization" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="settings"></span>Customization</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="documentation/customization/configuration.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Configuration</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/customization/styling.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Styling</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/customization/dark-mode.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dark mode</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/customization/plugin.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Plugin</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown dropdown-inside"><a class="dropdown-item dropdown-toggle" id="layouts-doc" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="table"></span>Layouts doc</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="documentation/layouts/vertical-navbar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Vertical navbar</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/layouts/horizontal-navbar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Horizontal navbar</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/layouts/combo-navbar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo navbar</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/layouts/dual-nav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dual nav</div>
                            </a></li>
                        </ul>
                        </li>
                        <li><a class="dropdown-item" href="documentation/gulp.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 fa-brands fa-gulp ms-1 me-1 fa-lg"></span>Gulp</div>
                        </a></li>
                        <li><a class="dropdown-item" href="documentation/design-file.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="figma"></span>Design file</div>
                        </a></li>
                        <li><a class="dropdown-item" href="changelog.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="git-merge"></span>Changelog</div>
                        </a></li>
                        <li><a class="dropdown-item" href="showcase.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="monitor"></span>Showcase</div>
                        </a></li>
                    </ul>
                    </li>
                </ul>
                </div>
                <ul class="navbar-nav navbar-nav-icons flex-row">
                <li class="nav-item">
                    <div class="theme-control-toggle fa-ion-wait pe-2 theme-control-toggle-slim"><input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="phoenixTheme" value="dark" /><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon me-1 d-none d-sm-block" data-feather="moon"></span><span class="fs--1 fw-bold">Dark</span></label><label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon me-1 d-none d-sm-block" data-feather="sun"></span><span class="fs--1 fw-bold">Light</span></label></div>
                </li>
                <li class="nav-item"> <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchBoxModal"><span data-feather="search" style="height:12px;width:12px;"></span></a></li>
                <li class="nav-item dropdown">
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
                </li>
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
                        <div class="row text-center align-items-center gx-0 gy-0">
                            
                        </div>
                        </div>
                    </div>
                    </div>
                </li>
                <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0 white-space-nowrap" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false">Olivia <span class="fa-solid fa-chevron-down fs--2"></span></a>
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
            </nav>
            <nav class="navbar navbar-top fixed-top navbar-expand-lg" id="navbarCombo" data-navbar-top="combo" data-move-target="#navbarVerticalNav" style="display:none;">
                <div class="navbar-logo">
                <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
                <a class="navbar-brand me-1 me-sm-3" href="index-2.html">
                    <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center"><a class="navbar-brand ps-3" href="./"><img src="{{asset('imagenes/splittel.png') }}" alt="Logo" width="100" height="40"></a>
                    </div>
                    </div>
                </a>
                </div>
                <div class="collapse navbar-collapse navbar-top-collapse order-1 order-lg-0 justify-content-center" id="navbarTopCollapse">
                <ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-chart-pie"></span>Home1</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item active" href="index-2.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="shopping-cart"></span>E commerce</div>
                        </a></li>
                        <li><a class="dropdown-item" href="dashboard/project-management.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="clipboard"></span>Project management</div>
                        </a></li>
                        <li><a class="dropdown-item" href="dashboard/crm.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="phone"></span>CRM</div>
                        </a></li>
                        <li><a class="dropdown-item" href="apps/social/feed.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="share-2"></span>Social feed</div>
                        </a></li>
                    </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-cube"></span>Apps</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="e-commerce" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="shopping-cart"></span>E commerce</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="admin" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Admin</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/add-product.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Add product</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/products.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Products</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/customers.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Customers</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/customer-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Customer details</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/orders.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Orders</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/order-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Order details</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/refund.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Refund</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="customer" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Customer</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/homepage.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Homepage</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Product details</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/products-filter.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Products filter</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/cart.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Cart</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/checkout.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Checkout</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/shipping-info.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Shipping info</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/profile.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Profile</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/favourite-stores.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Favourite stores</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/wishlist.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Wishlist</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/order-tracking.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Order tracking</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/invoice.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Invoice</div>
                                </a></li>
                            </ul>
                            </li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="CRM" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="phone"></span>CRM</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/crm/analytics.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Analytics</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/deals.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Deals</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/deal-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Deal details</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/leads.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Leads</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/lead-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lead details</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/reports.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reports</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/reports-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reports details</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/add-contact.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Add contact</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="project-management" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="clipboard"></span>Project management</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/project-management/create-new.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create new</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-list-view.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project list view</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-card-view.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project card view</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-board-view.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project board view</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/todo-list.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Todo list</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project details</div>
                            </a></li>
                        </ul>
                        </li>
                        <li><a class="dropdown-item" href="apps/chat.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="message-square"></span>Chat</div>
                        </a></li>
                        <!--
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="email" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="mail"></span>Email</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/email/inbox.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Inbox</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/email/email-detail.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Email detail</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/email/compose.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Compose</div>
                            </a></li>
                        </ul>-->
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="events" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="bookmark"></span>Events</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/events/create-an-event.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create an event</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/events/event-detail.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Event detail</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="kanban" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="trello"></span>Kanban</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/kanban/kanban.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Kanban</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/kanban/boards.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Boards</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/kanban/create-kanban-board.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create board</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="social" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="share-2"></span>Social</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/social/profile.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Profile</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/social/settings.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Settings</div>
                            </a></li>
                        </ul>
                        </li>
                        <li><a class="dropdown-item" href="apps/calendar.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="calendar"></span>Calendar</div>
                        </a></li>
                    </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-files-landscapes-alt"></span>Pages</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item" href="pages/starter.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="compass"></span>Starter</div>
                        </a></li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="faq" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="help-circle"></span>Faq</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/faq/faq-accordion.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Faq accordion</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/faq/faq-tab.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Faq tab</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="landing" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="globe"></span>Landing</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/landing/default.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Default</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/landing/alternate.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Alternate</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="pricing" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="tag"></span>Pricing</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/pricing/pricing-column.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Pricing column</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/pricing/pricing-grid.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Pricing grid</div>
                            </a></li>
                        </ul>
                        </li>
                        <li><a class="dropdown-item" href="pages/notifications.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="bell"></span>Notifications</div>
                        </a></li>
                        <li><a class="dropdown-item" href="pages/members.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="users"></span>Members</div>
                        </a></li>
                        <li><a class="dropdown-item" href="pages/timeline.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="clock"></span>Timeline</div>
                        </a></li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="errors" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="alert-triangle"></span>Errors</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/errors/404.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>404</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/errors/403.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>403</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/errors/500.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>500</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="authentication" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="lock"></span>Authentication</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="simple" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Simple</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/authentication/simple/sign-in.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/sign-up.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/sign-out.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/forgot-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/reset-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/lock-screen.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/2FA.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="split" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Split</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/authentication/split/sign-in.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/sign-up.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/sign-out.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/forgot-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/reset-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/lock-screen.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/2FA.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="Card" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Card</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/authentication/card/sign-in.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/sign-up.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/sign-out.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/forgot-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/reset-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/lock-screen.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/2FA.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                </a></li>
                            </ul>
                            </li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="layouts" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="layout"></span>Layouts</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="demo/vertical-sidenav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Vertical sidenav</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/dark-mode.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dark mode</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/sidenav-collapse.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sidenav collapse</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/darknav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Darknav</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/topnav-slim.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Topnav slim</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/navbar-top-slim.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Navbar top slim</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/navbar-top.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Navbar top</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/horizontal-slim.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Horizontal slim</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/combo-nav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo nav</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/combo-nav-slim.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo nav slim</div>
                            </a></li>
                            <li><a class="dropdown-item" href="demo/dual-nav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dual nav</div>
                            </a></li>
                        </ul>
                        </li>
                    </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-puzzle-piece"></span>Modules</a>
                    <ul class="dropdown-menu navbar-dropdown-caret dropdown-menu-card py-0">
                        <div class="border-0 scrollbar" style="max-height: 60vh;">
                        <div class="px-3 pt-4 pb-3 img-dropdown">
                            <div class="row gx-4 gy-5">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="dropdown-item-group"><span class="me-2" data-feather="file-text" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Forms</h6>
                                </div><a class="dropdown-link" href="modules/forms/basic/form-control.html">Form control</a><a class="dropdown-link" href="modules/forms/basic/input-group.html">Input group</a><a class="dropdown-link" href="modules/forms/basic/select.html">Select</a><a class="dropdown-link" href="modules/forms/basic/checks.html">Checks</a><a class="dropdown-link" href="modules/forms/basic/range.html">Range</a><a class="dropdown-link" href="modules/forms/basic/floating-labels.html">Floating labels</a><a class="dropdown-link" href="modules/forms/basic/layout.html">Layout</a><a class="dropdown-link" href="modules/forms/advance/advance-select.html">Advance select</a><a class="dropdown-link" href="modules/forms/advance/date-picker.html">Date picker</a><a class="dropdown-link" href="modules/forms/advance/editor.html">Editor</a><a class="dropdown-link" href="modules/forms/advance/file-uploader.html">File uploader</a><a class="dropdown-link" href="modules/forms/advance/rating.html">Rating</a><a class="dropdown-link" href="modules/forms/advance/emoji-button.html">Emoji button</a><a class="dropdown-link" href="modules/forms/validation.html">Validation</a><a class="dropdown-link" href="modules/forms/wizard.html">Wizard</a>
                                <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="grid" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Icons</h6>
                                </div><a class="dropdown-link" href="modules/icons/feather.html">Feather</a><a class="dropdown-link" href="modules/icons/font-awesome.html">Font awesome</a><a class="dropdown-link" href="modules/icons/unicons.html">Unicons</a>
                                <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="bar-chart-2" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">ECharts</h6>
                                </div><a class="dropdown-link" href="modules/echarts/line-charts.html">Line charts</a><a class="dropdown-link" href="modules/echarts/bar-charts.html">Bar charts</a><a class="dropdown-link" href="modules/echarts/candlestick-charts.html">Candlestick charts</a><a class="dropdown-link" href="modules/echarts/geo-map.html">Geo map</a><a class="dropdown-link" href="modules/echarts/scatter-charts.html">Scatter charts</a><a class="dropdown-link" href="modules/echarts/pie-charts.html">Pie charts</a><a class="dropdown-link" href="modules/echarts/gauge-chart.html">Gauge chart</a><a class="dropdown-link" href="modules/echarts/radar-charts.html">Radar charts</a><a class="dropdown-link" href="modules/echarts/heatmap-charts.html">Heatmap charts</a><a class="dropdown-link" href="modules/echarts/how-to-use.html">How to use</a>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="dropdown-item-group"><span class="me-2" data-feather="package" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Components</h6>
                                </div><a class="dropdown-link" href="modules/components/accordion.html">Accordion</a><a class="dropdown-link" href="modules/components/avatar.html">Avatar</a><a class="dropdown-link" href="modules/components/alerts.html">Alerts</a><a class="dropdown-link" href="modules/components/badge.html">Badge</a><a class="dropdown-link" href="modules/components/breadcrumb.html">Breadcrumb</a><a class="dropdown-link" href="modules/components/button.html">Buttons</a><a class="dropdown-link" href="modules/components/calendar.html">Calendar</a><a class="dropdown-link" href="modules/components/card.html">Card</a><a class="dropdown-link" href="modules/components/carousel/bootstrap.html">Bootstrap</a><a class="dropdown-link" href="modules/components/carousel/swiper.html">Swiper</a><a class="dropdown-link" href="modules/components/collapse.html">Collapse</a><a class="dropdown-link" href="modules/components/dropdown.html">Dropdown</a><a class="dropdown-link" href="modules/components/list-group.html">List group</a><a class="dropdown-link" href="modules/components/modal.html">Modals</a><a class="dropdown-link" href="modules/components/navs-and-tabs/navs.html">Navs</a><a class="dropdown-link" href="modules/components/navs-and-tabs/navbar.html">Navbar</a><a class="dropdown-link" href="modules/components/navs-and-tabs/tabs.html">Tabs</a><a class="dropdown-link" href="modules/components/offcanvas.html">Offcanvas</a><a class="dropdown-link" href="modules/components/progress-bar.html">Progress bar</a><a class="dropdown-link" href="modules/components/placeholder.html">Placeholder</a><a class="dropdown-link" href="modules/components/pagination.html">Pagination</a><a class="dropdown-link" href="modules/components/popovers.html">Popovers</a><a class="dropdown-link" href="modules/components/scrollspy.html">Scrollspy</a><a class="dropdown-link" href="modules/components/sortable.html">Sortable</a><a class="dropdown-link" href="modules/components/spinners.html">Spinners</a><a class="dropdown-link" href="modules/components/toast.html">Toast</a><a class="dropdown-link" href="modules/components/tooltips.html">Tooltips</a><a class="dropdown-link" href="modules/components/chat-widget.html">Chat widget</a>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="dropdown-item-group"><span class="me-2" data-feather="columns" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Tables</h6>
                                </div><a class="dropdown-link" href="modules/tables/basic-tables.html">Basic tables</a><a class="dropdown-link" href="modules/tables/advance-tables.html">Advance tables</a><a class="dropdown-link" href="modules/tables/bulk-select.html">Bulk Select</a>
                                <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="tool" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Utilities</h6>
                                </div><a class="dropdown-link" href="modules/utilities/background.html">Background</a><a class="dropdown-link" href="modules/utilities/borders.html">Borders</a><a class="dropdown-link" href="modules/utilities/colors.html">Colors</a><a class="dropdown-link" href="modules/utilities/display.html">Display</a><a class="dropdown-link" href="modules/utilities/flex.html">Flex</a><a class="dropdown-link" href="modules/utilities/stacks.html">Stacks</a><a class="dropdown-link" href="modules/utilities/float.html">Float</a><a class="dropdown-link" href="modules/utilities/grid.html">Grid</a><a class="dropdown-link" href="modules/utilities/interactions.html">Interactions</a><a class="dropdown-link" href="modules/utilities/opacity.html">Opacity</a><a class="dropdown-link" href="modules/utilities/overflow.html">Overflow</a><a class="dropdown-link" href="modules/utilities/position.html">Position</a><a class="dropdown-link" href="modules/utilities/shadows.html">Shadows</a><a class="dropdown-link" href="modules/utilities/sizing.html">Sizing</a><a class="dropdown-link" href="modules/utilities/spacing.html">Spacing</a><a class="dropdown-link" href="modules/utilities/typography.html">Typography</a><a class="dropdown-link" href="modules/utilities/vertical-align.html">Vertical align</a><a class="dropdown-link" href="modules/utilities/visibility.html">Visibility</a>
                            </div>
                            </div>
                        </div>
                        </div>
                    </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-document-layout-right"></span>Documentation</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item" href="documentation/getting-started.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="life-buoy"></span>Getting started</div>
                        </a></li>
                        <li class="dropdown dropdown-inside"><a class="dropdown-item dropdown-toggle" id="customization" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="settings"></span>Customization</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="documentation/customization/configuration.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Configuration</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/customization/styling.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Styling</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/customization/dark-mode.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dark mode</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/customization/plugin.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Plugin</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown dropdown-inside"><a class="dropdown-item dropdown-toggle" id="layouts-doc" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="table"></span>Layouts doc</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="documentation/layouts/vertical-navbar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Vertical navbar</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/layouts/horizontal-navbar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Horizontal navbar</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/layouts/combo-navbar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo navbar</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/layouts/dual-nav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dual nav</div>
                            </a></li>
                        </ul>
                        </li>
                        <li><a class="dropdown-item" href="documentation/gulp.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 fa-brands fa-gulp ms-1 me-1 fa-lg"></span>Gulp</div>
                        </a></li>
                        <li><a class="dropdown-item" href="documentation/design-file.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="figma"></span>Design file</div>
                        </a></li>
                        <li><a class="dropdown-item" href="changelog.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="git-merge"></span>Changelog</div>
                        </a></li>
                        <li><a class="dropdown-item" href="showcase.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="monitor"></span>Showcase</div>
                        </a></li>
                    </ul>
                    </li>
                </ul>
                </div>
                <ul class="navbar-nav navbar-nav-icons flex-row">
                <li class="nav-item">
                    <div class="theme-control-toggle fa-icon-wait px-2"><input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" /><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="moon"></span></label><label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="sun"></span></label></div>
                </li>
                <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchBoxModal"><span data-feather="search" style="height:19px;width:19px;margin-bottom: 2px;"></span></a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" style="min-width: 2.5rem" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span data-feather="bell" style="height:20px;width:20px;"></span></a>
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
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" id="navbarDropdownNindeDots" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false"><svg width="16" height="16" viewbox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                        <div class="row text-center align-items-center gx-0 gy-0">
                            
                        </div>
                        </div>
                    </div>
                    </div>
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
                            <li class="nav-item"><a class="nav-link px-3" href="#!"> <span class="me-2 text-900" data-feather="user"></span><span>Perfil</span></a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="{{route('Home')}}"><span class="me-2 text-900" data-feather="pie-chart"></span>Dashboard</a></li>
                        </ul>
                        </div>
                        <div class="card-footer p-0 border-top">
                        <!---------------------------------------------------------------------------------------->
                        <div class="px-3"> 
                            <form id="logout-form" action="{{ route('logout') }}" method="get" style="display: none;">
                                 @csrf
                            </form>
                            <a class="btn btn-phoenix-secondary d-flex flex-center w-100" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <span class="nav-link-icon"> <span data-feather="log-out"></span></span>
                                <span class="nav-link-text-wrapper"> <span class="nav-link-text">Cerrar sesi&oacute;n</span> </span>
                            </a>
                        </div>
                        
                        </div>
                    </div>
                    </div>
                </li>
                </ul>
            </nav>
            <nav class="navbar navbar-top fixed-top navbar-slim justify-content-between navbar-expand-lg" id="navbarComboSlim" data-navbar-top="combo" data-move-target="#navbarVerticalNav" style="display:none;">
                <div class="navbar-logo">
                <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
                <a class="navbar-brand navbar-brand" href="index-2.html">phoenix <span class="text-1000 d-none d-sm-inline">slim</span></a>
                </div>
                <div class="collapse navbar-collapse navbar-top-collapse order-1 order-lg-0 justify-content-center" id="navbarTopCollapse">
                <ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-chart-pie"></span>Home1</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item active" href="index-2.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="shopping-cart"></span>E commerce</div>
                        </a></li>
                        <li><a class="dropdown-item" href="dashboard/project-management.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="clipboard"></span>Project management</div>
                        </a></li>
                        <li><a class="dropdown-item" href="dashboard/crm.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="phone"></span>CRM</div>
                        </a></li>
                        <li><a class="dropdown-item" href="apps/social/feed.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="share-2"></span>Social feed</div>
                        </a></li>
                    </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-cube"></span>Apps</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="e-commerce" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="shopping-cart"></span>E commerce</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="admin" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Admin</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/add-product.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Add product</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/products.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Products</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/customers.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Customers</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/customer-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Customer details</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/orders.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Orders</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/order-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Order details</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/refund.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Refund</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="customer" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Customer</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/homepage.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Homepage</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Product details</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/products-filter.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Products filter</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/cart.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Cart</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/checkout.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Checkout</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/shipping-info.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Shipping info</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/profile.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Profile</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/favourite-stores.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Favourite stores</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/wishlist.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Wishlist</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/order-tracking.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Order tracking</div>
                                </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/invoice.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Invoice</div>
                                </a></li>
                            </ul>
                            </li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="CRM" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="phone"></span>CRM</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/crm/analytics.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Analytics</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/deals.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Deals</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/deal-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Deal details</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/leads.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Leads</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/lead-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lead details</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/reports.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reports</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/reports-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reports details</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/crm/add-contact.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Add contact</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="project-management" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="clipboard"></span>Project management</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/project-management/create-new.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create new</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-list-view.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project list view</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-card-view.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project card view</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-board-view.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project board view</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/todo-list.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Todo list</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project details</div>
                            </a></li>
                        </ul>
                        </li>
                        <!--
                        <li><a class="dropdown-item" href="apps/chat.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="message-square"></span>Chat</div>
                        </a></li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="email" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="mail"></span>Email</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/email/inbox.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Inbox</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/email/email-detail.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Email detail</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/email/compose.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Compose</div>
                            </a></li>
                        </ul>-->
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="events" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="bookmark"></span>Events</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/events/create-an-event.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create an event</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/events/event-detail.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Event detail</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="kanban" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="trello"></span>Kanban</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/kanban/kanban.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Kanban</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/kanban/boards.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Boards</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/kanban/create-kanban-board.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create board</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="social" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="share-2"></span>Social</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/social/profile.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Profile</div>
                            </a></li>
                            <li><a class="dropdown-item" href="apps/social/settings.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Settings</div>
                            </a></li>
                        </ul>
                        </li>
                        <li><a class="dropdown-item" href="apps/calendar.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="calendar"></span>Calendar</div>
                        </a></li>
                    </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-files-landscapes-alt"></span>Pages</a>
                        <ul class="dropdown-menu navbar-dropdown-caret">
                            <li><a class="dropdown-item" href="pages/starter.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="compass"></span>Starter</div>
                            </a></li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="faq" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="help-circle"></span>Faq</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/faq/faq-accordion.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Faq accordion</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/faq/faq-tab.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Faq tab</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="landing" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="globe"></span>Landing</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/landing/default.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Default</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/landing/alternate.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Alternate</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="pricing" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="tag"></span>Pricing</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/pricing/pricing-column.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Pricing column</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/pricing/pricing-grid.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Pricing grid</div>
                                </a></li>
                            </ul>
                            </li>
                            <li><a class="dropdown-item" href="pages/notifications.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="bell"></span>Notifications</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/members.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="users"></span>Members</div>
                            </a></li>
                            <li><a class="dropdown-item" href="pages/timeline.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="clock"></span>Timeline</div>
                            </a></li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="errors" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="alert-triangle"></span>Errors</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/errors/404.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>404</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/errors/403.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>403</div>
                                </a></li>
                                <li><a class="dropdown-item" href="pages/errors/500.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>500</div>
                                </a></li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="authentication" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="lock"></span>Authentication</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="simple" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Simple</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="pages/authentication/simple/sign-in.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/simple/sign-up.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/simple/sign-out.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/simple/forgot-password.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/simple/reset-password.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/simple/lock-screen.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/simple/2FA.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                    </a></li>
                                </ul>
                                </li>
                                <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="split" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Split</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="pages/authentication/split/sign-in.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/split/sign-up.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/split/sign-out.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/split/forgot-password.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/split/reset-password.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/split/lock-screen.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/split/2FA.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                    </a></li>
                                </ul>
                                </li>
                                <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="Card" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Card</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="pages/authentication/card/sign-in.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/card/sign-up.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/card/sign-out.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/card/forgot-password.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/card/reset-password.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/card/lock-screen.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                    </a></li>
                                    <li><a class="dropdown-item" href="pages/authentication/card/2FA.html">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                    </a></li>
                                </ul>
                                </li>
                            </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="layouts" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="layout"></span>Layouts</span></div>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="demo/vertical-sidenav.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Vertical sidenav</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/dark-mode.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dark mode</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/sidenav-collapse.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sidenav collapse</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/darknav.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Darknav</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/topnav-slim.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Topnav slim</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/navbar-top-slim.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Navbar top slim</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/navbar-top.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Navbar top</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/horizontal-slim.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Horizontal slim</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/combo-nav.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo nav</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/combo-nav-slim.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo nav slim</div>
                                </a></li>
                                <li><a class="dropdown-item" href="demo/dual-nav.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dual nav</div>
                                </a></li>
                            </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-puzzle-piece"></span>Modules</a>
                    <ul class="dropdown-menu navbar-dropdown-caret dropdown-menu-card py-0">
                        <div class="border-0 scrollbar" style="max-height: 60vh;">
                        <div class="px-3 pt-4 pb-3 img-dropdown">
                            <div class="row gx-4 gy-5">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="dropdown-item-group"><span class="me-2" data-feather="file-text" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Forms</h6>
                                </div><a class="dropdown-link" href="modules/forms/basic/form-control.html">Form control</a><a class="dropdown-link" href="modules/forms/basic/input-group.html">Input group</a><a class="dropdown-link" href="modules/forms/basic/select.html">Select</a><a class="dropdown-link" href="modules/forms/basic/checks.html">Checks</a><a class="dropdown-link" href="modules/forms/basic/range.html">Range</a><a class="dropdown-link" href="modules/forms/basic/floating-labels.html">Floating labels</a><a class="dropdown-link" href="modules/forms/basic/layout.html">Layout</a><a class="dropdown-link" href="modules/forms/advance/advance-select.html">Advance select</a><a class="dropdown-link" href="modules/forms/advance/date-picker.html">Date picker</a><a class="dropdown-link" href="modules/forms/advance/editor.html">Editor</a><a class="dropdown-link" href="modules/forms/advance/file-uploader.html">File uploader</a><a class="dropdown-link" href="modules/forms/advance/rating.html">Rating</a><a class="dropdown-link" href="modules/forms/advance/emoji-button.html">Emoji button</a><a class="dropdown-link" href="modules/forms/validation.html">Validation</a><a class="dropdown-link" href="modules/forms/wizard.html">Wizard</a>
                                <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="grid" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Icons</h6>
                                </div><a class="dropdown-link" href="modules/icons/feather.html">Feather</a><a class="dropdown-link" href="modules/icons/font-awesome.html">Font awesome</a><a class="dropdown-link" href="modules/icons/unicons.html">Unicons</a>
                                <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="bar-chart-2" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">ECharts</h6>
                                </div><a class="dropdown-link" href="modules/echarts/line-charts.html">Line charts</a><a class="dropdown-link" href="modules/echarts/bar-charts.html">Bar charts</a><a class="dropdown-link" href="modules/echarts/candlestick-charts.html">Candlestick charts</a><a class="dropdown-link" href="modules/echarts/geo-map.html">Geo map</a><a class="dropdown-link" href="modules/echarts/scatter-charts.html">Scatter charts</a><a class="dropdown-link" href="modules/echarts/pie-charts.html">Pie charts</a><a class="dropdown-link" href="modules/echarts/gauge-chart.html">Gauge chart</a><a class="dropdown-link" href="modules/echarts/radar-charts.html">Radar charts</a><a class="dropdown-link" href="modules/echarts/heatmap-charts.html">Heatmap charts</a><a class="dropdown-link" href="modules/echarts/how-to-use.html">How to use</a>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="dropdown-item-group"><span class="me-2" data-feather="package" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Components</h6>
                                </div><a class="dropdown-link" href="modules/components/accordion.html">Accordion</a><a class="dropdown-link" href="modules/components/avatar.html">Avatar</a><a class="dropdown-link" href="modules/components/alerts.html">Alerts</a><a class="dropdown-link" href="modules/components/badge.html">Badge</a><a class="dropdown-link" href="modules/components/breadcrumb.html">Breadcrumb</a><a class="dropdown-link" href="modules/components/button.html">Buttons</a><a class="dropdown-link" href="modules/components/calendar.html">Calendar</a><a class="dropdown-link" href="modules/components/card.html">Card</a><a class="dropdown-link" href="modules/components/carousel/bootstrap.html">Bootstrap</a><a class="dropdown-link" href="modules/components/carousel/swiper.html">Swiper</a><a class="dropdown-link" href="modules/components/collapse.html">Collapse</a><a class="dropdown-link" href="modules/components/dropdown.html">Dropdown</a><a class="dropdown-link" href="modules/components/list-group.html">List group</a><a class="dropdown-link" href="modules/components/modal.html">Modals</a><a class="dropdown-link" href="modules/components/navs-and-tabs/navs.html">Navs</a><a class="dropdown-link" href="modules/components/navs-and-tabs/navbar.html">Navbar</a><a class="dropdown-link" href="modules/components/navs-and-tabs/tabs.html">Tabs</a><a class="dropdown-link" href="modules/components/offcanvas.html">Offcanvas</a><a class="dropdown-link" href="modules/components/progress-bar.html">Progress bar</a><a class="dropdown-link" href="modules/components/placeholder.html">Placeholder</a><a class="dropdown-link" href="modules/components/pagination.html">Pagination</a><a class="dropdown-link" href="modules/components/popovers.html">Popovers</a><a class="dropdown-link" href="modules/components/scrollspy.html">Scrollspy</a><a class="dropdown-link" href="modules/components/sortable.html">Sortable</a><a class="dropdown-link" href="modules/components/spinners.html">Spinners</a><a class="dropdown-link" href="modules/components/toast.html">Toast</a><a class="dropdown-link" href="modules/components/tooltips.html">Tooltips</a><a class="dropdown-link" href="modules/components/chat-widget.html">Chat widget</a>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="dropdown-item-group"><span class="me-2" data-feather="columns" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Tables</h6>
                                </div><a class="dropdown-link" href="modules/tables/basic-tables.html">Basic tables</a><a class="dropdown-link" href="modules/tables/advance-tables.html">Advance tables</a><a class="dropdown-link" href="modules/tables/bulk-select.html">Bulk Select</a>
                                <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="tool" style="stroke-width:2;"></span>
                                <h6 class="dropdown-item-title">Utilities</h6>
                                </div><a class="dropdown-link" href="modules/utilities/background.html">Background</a><a class="dropdown-link" href="modules/utilities/borders.html">Borders</a><a class="dropdown-link" href="modules/utilities/colors.html">Colors</a><a class="dropdown-link" href="modules/utilities/display.html">Display</a><a class="dropdown-link" href="modules/utilities/flex.html">Flex</a><a class="dropdown-link" href="modules/utilities/stacks.html">Stacks</a><a class="dropdown-link" href="modules/utilities/float.html">Float</a><a class="dropdown-link" href="modules/utilities/grid.html">Grid</a><a class="dropdown-link" href="modules/utilities/interactions.html">Interactions</a><a class="dropdown-link" href="modules/utilities/opacity.html">Opacity</a><a class="dropdown-link" href="modules/utilities/overflow.html">Overflow</a><a class="dropdown-link" href="modules/utilities/position.html">Position</a><a class="dropdown-link" href="modules/utilities/shadows.html">Shadows</a><a class="dropdown-link" href="modules/utilities/sizing.html">Sizing</a><a class="dropdown-link" href="modules/utilities/spacing.html">Spacing</a><a class="dropdown-link" href="modules/utilities/typography.html">Typography</a><a class="dropdown-link" href="modules/utilities/vertical-align.html">Vertical align</a><a class="dropdown-link" href="modules/utilities/visibility.html">Visibility</a>
                            </div>
                            </div>
                        </div>
                        </div>
                    </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-document-layout-right"></span>Documentation</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item" href="documentation/getting-started.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="life-buoy"></span>Getting started</div>
                        </a></li>
                        <li class="dropdown dropdown-inside"><a class="dropdown-item dropdown-toggle" id="customization" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="settings"></span>Customization</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="documentation/customization/configuration.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Configuration</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/customization/styling.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Styling</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/customization/dark-mode.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dark mode</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/customization/plugin.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Plugin</div>
                            </a></li>
                        </ul>
                        </li>
                        <li class="dropdown dropdown-inside"><a class="dropdown-item dropdown-toggle" id="layouts-doc" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="table"></span>Layouts doc</span></div>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="documentation/layouts/vertical-navbar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Vertical navbar</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/layouts/horizontal-navbar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Horizontal navbar</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/layouts/combo-navbar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo navbar</div>
                            </a></li>
                            <li><a class="dropdown-item" href="documentation/layouts/dual-nav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dual nav</div>
                            </a></li>
                        </ul>
                        </li>
                        <li><a class="dropdown-item" href="documentation/gulp.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 fa-brands fa-gulp ms-1 me-1 fa-lg"></span>Gulp</div>
                        </a></li>
                        <li><a class="dropdown-item" href="documentation/design-file.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="figma"></span>Design file</div>
                        </a></li>
                        <li><a class="dropdown-item" href="changelog.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="git-merge"></span>Changelog</div>
                        </a></li>
                        <li><a class="dropdown-item" href="showcase.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="monitor"></span>Showcase</div>
                        </a></li>
                    </ul>
                    </li>
                </ul>
                </div>
                <ul class="navbar-nav navbar-nav-icons flex-row">
                <li class="nav-item">
                    <div class="theme-control-toggle fa-ion-wait pe-2 theme-control-toggle-slim"><input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="phoenixTheme" value="dark" /><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon me-1 d-none d-sm-block" data-feather="moon"></span><span class="fs--1 fw-bold">Dark</span></label><label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon me-1 d-none d-sm-block" data-feather="sun"></span><span class="fs--1 fw-bold">Light</span></label></div>
                </li>
                <li class="nav-item"> <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchBoxModal"><span data-feather="search" style="height:12px;width:12px;"></span></a></li>
                <li class="nav-item dropdown">
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
                </li>
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
                        <div class="row text-center align-items-center gx-0 gy-0">
                            
                        </div>
                        </div>
                    </div>
                    </div>
                </li>
                <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0 white-space-nowrap" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false">Olivia <span class="fa-solid fa-chevron-down fs--2"></span></a>
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
            </nav>
            <nav class="navbar navbar-top fixed-top navbar-expand-lg" id="dualNav" style="display:none;">
                <div class="w-100">
                <div class="d-flex flex-between-center dual-nav-first-layer">
                    <div class="navbar-logo">
                    <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTopCollapse" aria-controls="navbarTopCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
                    <a class="navbar-brand me-1 me-sm-3" href="index-2.html">
                        <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center"><a class="navbar-brand ps-3" href="./"><img src="{{asset('imagenes/splittel.png') }}" alt="Logo" width="100" height="40"></a>
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
                        <div class="theme-control-toggle fa-icon-wait px-2"><input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" /><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="moon"></span></label><label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="sun"></span></label></div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" style="min-width: 2.5rem" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span data-feather="bell" style="height:20px;width:20px;"></span></a>
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
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" id="navbarDropdownNindeDots" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside" aria-expanded="false"><svg width="16" height="16" viewbox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                            <div class="row text-center align-items-center gx-0 gy-0">
                                
                            </div>
                            </div>
                        </div>
                        </div>
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
                            <div class="mb-3 mx-3"><input class="form-control form-control-sm" id="statusUpdateInput" type="text" placeholder="Update your status" /></div>
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
                <div class="collapse navbar-collapse navbar-top-collapse justify-content-center" id="navbarTopCollapse">
                    <ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-chart-pie"></span>Home</a>
                        <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item active" href="index-2.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="shopping-cart"></span>E commerce</div>
                            </a></li>
                        <li><a class="dropdown-item" href="dashboard/project-management.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="clipboard"></span>Project management</div>
                            </a></li>
                        <li><a class="dropdown-item" href="dashboard/crm.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="phone"></span>CRM</div>
                            </a></li>
                        <li><a class="dropdown-item" href="apps/social/feed.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="share-2"></span>Social feed</div>
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-cube"></span>Apps</a>
                        <ul class="dropdown-menu navbar-dropdown-caret">
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="e-commerce" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="shopping-cart"></span>E commerce</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="admin" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Admin</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/add-product.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Add product</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/products.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Products</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/customers.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Customers</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/customer-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Customer details</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/orders.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Orders</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/order-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Order details</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/admin/refund.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Refund</div>
                                    </a></li>
                                </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="customer" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Customer</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/homepage.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Homepage</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/product-details.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Product details</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/products-filter.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Products filter</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/cart.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Cart</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/checkout.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Checkout</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/shipping-info.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Shipping info</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/profile.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Profile</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/favourite-stores.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Favourite stores</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/wishlist.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Wishlist</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/order-tracking.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Order tracking</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="apps/e-commerce/landing/invoice.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Invoice</div>
                                    </a></li>
                                </ul>
                            </li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="CRM" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="phone"></span>CRM</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/crm/analytics.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Analytics</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/crm/deals.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Deals</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/crm/deal-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Deal details</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/crm/leads.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Leads</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/crm/lead-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lead details</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/crm/reports.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reports</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/crm/reports-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reports details</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/crm/add-contact.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Add contact</div>
                                </a></li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="project-management" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="clipboard"></span>Project management</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/project-management/create-new.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create new</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-list-view.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project list view</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-card-view.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project card view</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-board-view.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project board view</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/todo-list.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Todo list</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/project-management/project-details.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Project details</div>
                                </a></li>
                            </ul>
                        </li>
                        <li><a class="dropdown-item" href="apps/chat.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="message-square"></span>Chat</div>
                            </a></li>
                            <!--
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="email" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="mail"></span>Email</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/email/inbox.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Inbox</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/email/email-detail.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Email detail</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/email/compose.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Compose</div>
                                </a></li>
                            </ul>-->
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="events" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="bookmark"></span>Events</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/events/create-an-event.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create an event</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/events/event-detail.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Event detail</div>
                                </a></li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="kanban" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="trello"></span>Kanban</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/kanban/kanban.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Kanban</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/kanban/boards.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Boards</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/kanban/create-kanban-board.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Create board</div>
                                </a></li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="social" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="share-2"></span>Social</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="apps/social/profile.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Profile</div>
                                </a></li>
                            <li><a class="dropdown-item" href="apps/social/settings.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Settings</div>
                                </a></li>
                            </ul>
                        </li>
                        <li><a class="dropdown-item" href="apps/calendar.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="calendar"></span>Calendar</div>
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-files-landscapes-alt"></span>Pages</a>
                        <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item" href="pages/starter.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="compass"></span>Starter</div>
                            </a></li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="faq" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="help-circle"></span>Faq</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/faq/faq-accordion.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Faq accordion</div>
                                </a></li>
                            <li><a class="dropdown-item" href="pages/faq/faq-tab.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Faq tab</div>
                                </a></li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="landing" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="globe"></span>Landing</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/landing/default.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Default</div>
                                </a></li>
                            <li><a class="dropdown-item" href="pages/landing/alternate.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Alternate</div>
                                </a></li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="pricing" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="tag"></span>Pricing</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/pricing/pricing-column.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Pricing column</div>
                                </a></li>
                            <li><a class="dropdown-item" href="pages/pricing/pricing-grid.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Pricing grid</div>
                                </a></li>
                            </ul>
                        </li>
                        <li><a class="dropdown-item" href="pages/notifications.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="bell"></span>Notifications</div>
                            </a></li>
                        <li><a class="dropdown-item" href="pages/members.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="users"></span>Members</div>
                            </a></li>
                        <li><a class="dropdown-item" href="pages/timeline.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="clock"></span>Timeline</div>
                            </a></li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="errors" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="alert-triangle"></span>Errors</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/errors/404.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>404</div>
                                </a></li>
                            <li><a class="dropdown-item" href="pages/errors/403.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>403</div>
                                </a></li>
                            <li><a class="dropdown-item" href="pages/errors/500.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>500</div>
                                </a></li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="authentication" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="lock"></span>Authentication</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="simple" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Simple</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/authentication/simple/sign-in.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/sign-up.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/sign-out.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/forgot-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/reset-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/lock-screen.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/simple/2FA.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                    </a></li>
                                </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="split" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Split</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/authentication/split/sign-in.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/sign-up.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/sign-out.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/forgot-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/reset-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/lock-screen.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/split/2FA.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                    </a></li>
                                </ul>
                            </li>
                            <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="Card" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil"></span>Card</span></div>
                                </a>
                                <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/authentication/card/sign-in.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign in</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/sign-up.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign up</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/sign-out.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sign out</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/forgot-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Forgot password</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/reset-password.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Reset password</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/lock-screen.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Lock screen</div>
                                    </a></li>
                                <li><a class="dropdown-item" href="pages/authentication/card/2FA.html">
                                    <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>2FA</div>
                                    </a></li>
                                </ul>
                            </li>
                            </ul>
                        </li>
                        <li class="dropdown"><a class="dropdown-item dropdown-toggle" id="layouts" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="layout"></span>Layouts</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="demo/vertical-sidenav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Vertical sidenav</div>
                                </a></li>
                            <li><a class="dropdown-item" href="demo/dark-mode.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dark mode</div>
                                </a></li>
                            <li><a class="dropdown-item" href="demo/sidenav-collapse.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Sidenav collapse</div>
                                </a></li>
                            <li><a class="dropdown-item" href="demo/darknav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Darknav</div>
                                </a></li>
                            <li><a class="dropdown-item" href="demo/topnav-slim.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Topnav slim</div>
                                </a></li>
                            <li><a class="dropdown-item" href="demo/navbar-top-slim.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Navbar top slim</div>
                                </a></li>
                            <li><a class="dropdown-item" href="demo/navbar-top.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Navbar top</div>
                                </a></li>
                            <li><a class="dropdown-item" href="demo/horizontal-slim.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Horizontal slim</div>
                                </a></li>
                            <li><a class="dropdown-item" href="demo/combo-nav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo nav</div>
                                </a></li>
                            <li><a class="dropdown-item" href="demo/combo-nav-slim.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo nav slim</div>
                                </a></li>
                            <li><a class="dropdown-item" href="demo/dual-nav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dual nav</div>
                                </a></li>
                            </ul>
                        </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-puzzle-piece"></span>Modules</a>
                        <ul class="dropdown-menu navbar-dropdown-caret dropdown-menu-card py-0">
                        <div class="border-0 scrollbar" style="max-height: 60vh;">
                            <div class="px-3 pt-4 pb-3 img-dropdown">
                            <div class="row gx-4 gy-5">
                                <div class="col-12 col-sm-6 col-md-4">
                                <div class="dropdown-item-group"><span class="me-2" data-feather="file-text" style="stroke-width:2;"></span>
                                    <h6 class="dropdown-item-title">Forms</h6>
                                </div><a class="dropdown-link" href="modules/forms/basic/form-control.html">Form control</a><a class="dropdown-link" href="modules/forms/basic/input-group.html">Input group</a><a class="dropdown-link" href="modules/forms/basic/select.html">Select</a><a class="dropdown-link" href="modules/forms/basic/checks.html">Checks</a><a class="dropdown-link" href="modules/forms/basic/range.html">Range</a><a class="dropdown-link" href="modules/forms/basic/floating-labels.html">Floating labels</a><a class="dropdown-link" href="modules/forms/basic/layout.html">Layout</a><a class="dropdown-link" href="modules/forms/advance/advance-select.html">Advance select</a><a class="dropdown-link" href="modules/forms/advance/date-picker.html">Date picker</a><a class="dropdown-link" href="modules/forms/advance/editor.html">Editor</a><a class="dropdown-link" href="modules/forms/advance/file-uploader.html">File uploader</a><a class="dropdown-link" href="modules/forms/advance/rating.html">Rating</a><a class="dropdown-link" href="modules/forms/advance/emoji-button.html">Emoji button</a><a class="dropdown-link" href="modules/forms/validation.html">Validation</a><a class="dropdown-link" href="modules/forms/wizard.html">Wizard</a>
                                <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="grid" style="stroke-width:2;"></span>
                                    <h6 class="dropdown-item-title">Icons</h6>
                                </div><a class="dropdown-link" href="modules/icons/feather.html">Feather</a><a class="dropdown-link" href="modules/icons/font-awesome.html">Font awesome</a><a class="dropdown-link" href="modules/icons/unicons.html">Unicons</a>
                                <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="bar-chart-2" style="stroke-width:2;"></span>
                                    <h6 class="dropdown-item-title">ECharts</h6>
                                </div><a class="dropdown-link" href="modules/echarts/line-charts.html">Line charts</a><a class="dropdown-link" href="modules/echarts/bar-charts.html">Bar charts</a><a class="dropdown-link" href="modules/echarts/candlestick-charts.html">Candlestick charts</a><a class="dropdown-link" href="modules/echarts/geo-map.html">Geo map</a><a class="dropdown-link" href="modules/echarts/scatter-charts.html">Scatter charts</a><a class="dropdown-link" href="modules/echarts/pie-charts.html">Pie charts</a><a class="dropdown-link" href="modules/echarts/gauge-chart.html">Gauge chart</a><a class="dropdown-link" href="modules/echarts/radar-charts.html">Radar charts</a><a class="dropdown-link" href="modules/echarts/heatmap-charts.html">Heatmap charts</a><a class="dropdown-link" href="modules/echarts/how-to-use.html">How to use</a>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                <div class="dropdown-item-group"><span class="me-2" data-feather="package" style="stroke-width:2;"></span>
                                    <h6 class="dropdown-item-title">Components</h6>
                                </div><a class="dropdown-link" href="modules/components/accordion.html">Accordion</a><a class="dropdown-link" href="modules/components/avatar.html">Avatar</a><a class="dropdown-link" href="modules/components/alerts.html">Alerts</a><a class="dropdown-link" href="modules/components/badge.html">Badge</a><a class="dropdown-link" href="modules/components/breadcrumb.html">Breadcrumb</a><a class="dropdown-link" href="modules/components/button.html">Buttons</a><a class="dropdown-link" href="modules/components/calendar.html">Calendar</a><a class="dropdown-link" href="modules/components/card.html">Card</a><a class="dropdown-link" href="modules/components/carousel/bootstrap.html">Bootstrap</a><a class="dropdown-link" href="modules/components/carousel/swiper.html">Swiper</a><a class="dropdown-link" href="modules/components/collapse.html">Collapse</a><a class="dropdown-link" href="modules/components/dropdown.html">Dropdown</a><a class="dropdown-link" href="modules/components/list-group.html">List group</a><a class="dropdown-link" href="modules/components/modal.html">Modals</a><a class="dropdown-link" href="modules/components/navs-and-tabs/navs.html">Navs</a><a class="dropdown-link" href="modules/components/navs-and-tabs/navbar.html">Navbar</a><a class="dropdown-link" href="modules/components/navs-and-tabs/tabs.html">Tabs</a><a class="dropdown-link" href="modules/components/offcanvas.html">Offcanvas</a><a class="dropdown-link" href="modules/components/progress-bar.html">Progress bar</a><a class="dropdown-link" href="modules/components/placeholder.html">Placeholder</a><a class="dropdown-link" href="modules/components/pagination.html">Pagination</a><a class="dropdown-link" href="modules/components/popovers.html">Popovers</a><a class="dropdown-link" href="modules/components/scrollspy.html">Scrollspy</a><a class="dropdown-link" href="modules/components/sortable.html">Sortable</a><a class="dropdown-link" href="modules/components/spinners.html">Spinners</a><a class="dropdown-link" href="modules/components/toast.html">Toast</a><a class="dropdown-link" href="modules/components/tooltips.html">Tooltips</a><a class="dropdown-link" href="modules/components/chat-widget.html">Chat widget</a>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                <div class="dropdown-item-group"><span class="me-2" data-feather="columns" style="stroke-width:2;"></span>
                                    <h6 class="dropdown-item-title">Tables</h6>
                                </div><a class="dropdown-link" href="modules/tables/basic-tables.html">Basic tables</a><a class="dropdown-link" href="modules/tables/advance-tables.html">Advance tables</a><a class="dropdown-link" href="modules/tables/bulk-select.html">Bulk Select</a>
                                <div class="dropdown-item-group mt-5"><span class="me-2" data-feather="tool" style="stroke-width:2;"></span>
                                    <h6 class="dropdown-item-title">Utilities</h6>
                                </div><a class="dropdown-link" href="modules/utilities/background.html">Background</a><a class="dropdown-link" href="modules/utilities/borders.html">Borders</a><a class="dropdown-link" href="modules/utilities/colors.html">Colors</a><a class="dropdown-link" href="modules/utilities/display.html">Display</a><a class="dropdown-link" href="modules/utilities/flex.html">Flex</a><a class="dropdown-link" href="modules/utilities/stacks.html">Stacks</a><a class="dropdown-link" href="modules/utilities/float.html">Float</a><a class="dropdown-link" href="modules/utilities/grid.html">Grid</a><a class="dropdown-link" href="modules/utilities/interactions.html">Interactions</a><a class="dropdown-link" href="modules/utilities/opacity.html">Opacity</a><a class="dropdown-link" href="modules/utilities/overflow.html">Overflow</a><a class="dropdown-link" href="modules/utilities/position.html">Position</a><a class="dropdown-link" href="modules/utilities/shadows.html">Shadows</a><a class="dropdown-link" href="modules/utilities/sizing.html">Sizing</a><a class="dropdown-link" href="modules/utilities/spacing.html">Spacing</a><a class="dropdown-link" href="modules/utilities/typography.html">Typography</a><a class="dropdown-link" href="modules/utilities/vertical-align.html">Vertical align</a><a class="dropdown-link" href="modules/utilities/visibility.html">Visibility</a>
                                </div>
                            </div>
                            </div>
                        </div>
                        </ul>
                    </li>
                    <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-0 me-2 uil-document-layout-right"></span>Documentation</a>
                        <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item" href="documentation/getting-started.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="life-buoy"></span>Getting started</div>
                            </a></li>
                        <li class="dropdown dropdown-inside"><a class="dropdown-item dropdown-toggle" id="customization" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="settings"></span>Customization</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="documentation/customization/configuration.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Configuration</div>
                                </a></li>
                            <li><a class="dropdown-item" href="documentation/customization/styling.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Styling</div>
                                </a></li>
                            <li><a class="dropdown-item" href="documentation/customization/dark-mode.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dark mode</div>
                                </a></li>
                            <li><a class="dropdown-item" href="documentation/customization/plugin.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Plugin</div>
                                </a></li>
                            </ul>
                        </li>
                        <li class="dropdown dropdown-inside"><a class="dropdown-item dropdown-toggle" id="layouts-doc" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <div class="dropdown-item-wrapper"><span class="uil fs-0 uil-angle-right lh-1 dropdown-indicator-icon"></span><span><span class="me-2 uil" data-feather="table"></span>Layouts doc</span></div>
                            </a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="documentation/layouts/vertical-navbar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Vertical navbar</div>
                                </a></li>
                            <li><a class="dropdown-item" href="documentation/layouts/horizontal-navbar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Horizontal navbar</div>
                                </a></li>
                            <li><a class="dropdown-item" href="documentation/layouts/combo-navbar.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Combo navbar</div>
                                </a></li>
                            <li><a class="dropdown-item" href="documentation/layouts/dual-nav.html">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>Dual nav</div>
                                </a></li>
                            </ul>
                        </li>
                        <li><a class="dropdown-item" href="documentation/gulp.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 fa-brands fa-gulp ms-1 me-1 fa-lg"></span>Gulp</div>
                            </a></li>
                        <li><a class="dropdown-item" href="documentation/design-file.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="figma"></span>Design file</div>
                            </a></li>
                        <li><a class="dropdown-item" href="changelog.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="git-merge"></span>Changelog</div>
                            </a></li>
                        <li><a class="dropdown-item" href="showcase.html">
                            <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="monitor"></span>Showcase</div>
                            </a></li>
                        </ul>
                    </li>
                    </ul>
                </div>
                </div>
            </nav>
            <div class="modal fade" id="searchBoxModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-phoenix-modal="data-phoenix-modal" style="--phoenix-backdrop-opacity: 1;">
                <div class="modal-dialog">
                <div class="modal-content mt-15 rounded-pill">
                    <div class="modal-body p-0">
                    <div class="search-box navbar-top-search-box" data-list='{"valueNames":["title"]}' style="width: auto;">
                        <form class="position-relative" data-bs-toggle="search" data-bs-display="static"><input class="form-control search-input fuzzy-search rounded-pill form-control-lg" type="search" placeholder="Search..." aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>
                        </form>
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
                    </div>
                </div>
                </div>
            </div>
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
            <div class="support-chat-container">
                <div class="container-fluid support-chat">
                <div class="card bg-white">
                    <div class="card-header d-flex flex-between-center px-4 py-3 border-bottom">
                    <h5 class="mb-0 d-flex align-items-center gap-2">Demo widget<span class="fa-solid fa-circle text-success fs--3"></span></h5>
                    <div class="btn-reveal-trigger"><button class="btn btn-link p-0 dropdown-toggle dropdown-caret-none transition-none d-flex" type="button" id="support-chat-dropdown" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h text-900"></span></button>
                        <div class="dropdown-menu dropdown-menu-end py-2" aria-labelledby="support-chat-dropdown"><a class="dropdown-item" href="#!">Request a callback</a><a class="dropdown-item" href="#!">Search in chat</a><a class="dropdown-item" href="#!">Show history</a><a class="dropdown-item" href="#!">Report to Admin</a><a class="dropdown-item btn-support-chat" href="#!">Close Support</a></div>
                    </div>
                    </div>
                    <div class="card-body chat p-0">
                    <div class="d-flex flex-column-reverse scrollbar h-100 p-3">
                        <div class="text-end mt-6"><a class="mb-2 d-inline-flex align-items-center text-decoration-none text-1100 hover-bg-soft rounded-pill border border-primary py-2 ps-4 pe-3" href="#!">
                            <p class="mb-0 fw-semi-bold fs--1">I need help with something</p><span class="fa-solid fa-paper-plane text-primary fs--1 ms-3"></span>
                        </a><a class="mb-2 d-inline-flex align-items-center text-decoration-none text-1100 hover-bg-soft rounded-pill border border-primary py-2 ps-4 pe-3" href="#!">
                            <p class="mb-0 fw-semi-bold fs--1">I can’t reorder a product I previously ordered</p><span class="fa-solid fa-paper-plane text-primary fs--1 ms-3"></span>
                        </a><a class="mb-2 d-inline-flex align-items-center text-decoration-none text-1100 hover-bg-soft rounded-pill border border-primary py-2 ps-4 pe-3" href="#!">
                            <p class="mb-0 fw-semi-bold fs--1">How do I place an order?</p><span class="fa-solid fa-paper-plane text-primary fs--1 ms-3"></span>
                        </a><a class="false d-inline-flex align-items-center text-decoration-none text-1100 hover-bg-soft rounded-pill border border-primary py-2 ps-4 pe-3" href="#!">
                            <p class="mb-0 fw-semi-bold fs--1">My payment method not working</p><span class="fa-solid fa-paper-plane text-primary fs--1 ms-3"></span>
                        </a></div>
                        <div class="text-center mt-auto">
                        <h5 class="mt-2 mb-3">Eric</h5>
                        <!--<p class="text-center text-black mb-0">Ask us anything – we’ll get back to you here or by email within 24 hours.</p>-->
                        </div>
                    </div>
                    </div>
                    <div class="card-footer d-flex align-items-center gap-2 border-top ps-3 pe-4 py-3">
                    <div class="d-flex align-items-center flex-1 gap-3 border rounded-pill px-4"><input class="form-control outline-none border-0 flex-1 fs--1 px-0" type="text" placeholder="Write message" /><label class="btn btn-link d-flex p-0 text-500 fs--1 border-0" for="supportChatPhotos"><span class="fa-solid fa-image"></span></label><input class="d-none" type="file" accept="image/*" id="supportChatPhotos" /><label class="btn btn-link d-flex p-0 text-500 fs--1 border-0" for="supportChatAttachment"> <span class="fa-solid fa-paperclip"></span></label><input class="d-none" type="file" id="supportChatAttachment" /></div><button class="btn p-0 border-0 send-btn"><span class="fa-solid fa-paper-plane fs--1"></span></button>
                    </div>
                </div>
                </div><button style="display: none" class="btn p-0 border border-200 btn-support-chat"><span class="fs-0 btn-text text-primary text-nowrap">Chat demo</span><span class="fa-solid fa-circle text-success fs--1 ms-2"></span><span class="fa-solid fa-chevron-down text-primary fs-1"></span></button>
            </div>
        </main><!-- ===============================================-->
        <!--    End of Main Content-->
        <!-- ===============================================-->
        <div class="offcanvas offcanvas-end settings-panel border-0" id="settings-offcanvas" tabindex="-1" aria-labelledby="settings-offcanvas">
            <div class="offcanvas-header align-items-start border-bottom flex-column">
                <div class="pt-1 w-100 mb-6 d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="mb-2 me-2 lh-sm"><span class="fas fa-palette me-2 fs-0"></span>Theme Customizer</h5>
                    <p class="mb-0 fs--1">Explore different styles according to your preferences</p>
                </div><button class="btn p-1 fw-bolder" type="button" data-bs-dismiss="offcanvas" aria-label="Close"><span class="fas fa-times fs-0"> </span></button>
                </div><button class="btn btn-phoenix-secondary w-100" data-theme-control="reset"><span class="fas fa-arrows-rotate me-2 fs--2"></span>Reset to default</button>
            </div>
            <div class="offcanvas-body scrollbar px-card" id="themeController">
                <div class="setting-panel-item mt-0">
                <h5 class="setting-panel-item-title">Color Scheme</h5>
               
                </div>
                <div class="border rounded-3 p-4 setting-panel-item bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="setting-panel-item-title mb-1">RTL </h5>
                    <div class="form-check form-switch mb-0"><input class="form-check-input ms-auto" type="checkbox" data-theme-control="phoenixIsRTL" /></div>
                </div>
                <p class="mb-0 text-700">Change text direction</p>
                </div>
                <div class="border rounded-3 p-4 setting-panel-item bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="setting-panel-item-title mb-1">Support Chat </h5>
                    <div class="form-check form-switch mb-0"><input class="form-check-input ms-auto" type="checkbox" data-theme-control="phoenixSupportChat" /></div>
                </div>
                <p class="mb-0 text-700">Toggle support chat</p>
                </div>
                <div class="setting-panel-item">
                <h5 class="setting-panel-item-title">Navigation Type</h5>
                </div>
                <div class="setting-panel-item">
                <h5 class="setting-panel-item-title">Vertical Navbar Appearance</h5>
               
                </div>
                <div class="setting-panel-item">
                <h5 class="setting-panel-item-title">Horizontal Navbar Shape</h5>
                
                </div>
                <div class="setting-panel-item">
                <h5 class="setting-panel-item-title">Horizontal Navbar Appearance</h5>
               
                </div><a class="bun btn-primary d-grid mb-3 text-white dark__text-100 mt-5 btn btn-primary" href="https://themes.getbootstrap.com/product/phoenix-admin-dashboard-webapp-template/" target="_blank">Purchase template</a>
            </div>
        </div>
        <a class="card setting-toggle" href="#settings-offcanvas" data-bs-toggle="offcanvas" style="display: none">
            <div class="card-body d-flex align-items-center px-2 py-1">
                <div class="position-relative rounded-start" style="height:34px;width:28px">
                <div class="settings-popover"><span class="ripple"><span class="fa-spin position-absolute all-0 d-flex flex-center"><span class="icon-spin position-absolute all-0 d-flex flex-center"><svg width="20" height="20" viewBox="0 0 20 20" fill="#ffffff" xmlns="http://www.w3.org/2000/svg"><path d="M19.7369 12.3941L19.1989 12.1065C18.4459 11.7041 18.0843 10.8487 18.0843 9.99495C18.0843 9.14118 18.4459 8.28582 19.1989 7.88336L19.7369 7.59581C19.9474 7.47484 20.0316 7.23291 19.9474 7.03131C19.4842 5.57973 18.6843 4.28943 17.6738 3.20075C17.5053 3.03946 17.2527 2.99914 17.0422 3.12011L16.393 3.46714C15.6883 3.84379 14.8377 3.74529 14.1476 3.3427C14.0988 3.31422 14.0496 3.28621 14.0002 3.25868C13.2568 2.84453 12.7055 2.10629 12.7055 1.25525V0.70081C12.7055 0.499202 12.5371 0.297594 12.2845 0.257272C10.7266 -0.105622 9.16879 -0.0653007 7.69516 0.257272C7.44254 0.297594 7.31623 0.499202 7.31623 0.70081V1.23474C7.31623 2.09575 6.74999 2.8362 5.99824 3.25599C5.95774 3.27861 5.91747 3.30159 5.87744 3.32493C5.15643 3.74527 4.26453 3.85902 3.53534 3.45302L2.93743 3.12011C2.72691 2.99914 2.47429 3.03946 2.30587 3.20075C1.29538 4.28943 0.495411 5.57973 0.0322686 7.03131C-0.051939 7.23291 0.0322686 7.47484 0.242788 7.59581L0.784376 7.8853C1.54166 8.29007 1.92694 9.13627 1.92694 9.99495C1.92694 10.8536 1.54166 11.6998 0.784375 12.1046L0.242788 12.3941C0.0322686 12.515 -0.051939 12.757 0.0322686 12.9586C0.495411 14.4102 1.29538 15.7005 2.30587 16.7891C2.47429 16.9504 2.72691 16.9907 2.93743 16.8698L3.58669 16.5227C4.29133 16.1461 5.14131 16.2457 5.8331 16.6455C5.88713 16.6767 5.94159 16.7074 5.99648 16.7375C6.75162 17.1511 7.31623 17.8941 7.31623 18.7552V19.2891C7.31623 19.4425 7.41373 19.5959 7.55309 19.696C7.64066 19.7589 7.74815 19.7843 7.85406 19.8046C9.35884 20.0925 10.8609 20.0456 12.2845 19.7729C12.5371 19.6923 12.7055 19.4907 12.7055 19.2891V18.7346C12.7055 17.8836 13.2568 17.1454 14.0002 16.7312C14.0496 16.7037 14.0988 16.6757 14.1476 16.6472C14.8377 16.2446 15.6883 16.1461 16.393 16.5227L17.0422 16.8698C17.2527 16.9907 17.5053 16.9504 17.6738 16.7891C18.7264 15.7005 19.4842 14.4102 19.9895 12.9586C20.0316 12.757 19.9474 12.515 19.7369 12.3941ZM10.0109 13.2005C8.1162 13.2005 6.64257 11.7893 6.64257 9.97478C6.64257 8.20063 8.1162 6.74905 10.0109 6.74905C11.8634 6.74905 13.3792 8.20063 13.3792 9.97478C13.3792 11.7893 11.8634 13.2005 10.0109 13.2005Z" fill="#2A7BE4"></path></svg></span></span></span></div>
                </div><small class="text-uppercase text-700 fw-bold py-2 pe-2 ps-1 rounded-end">customize</small>
            </div>
        </a>
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
        <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
        {{--! Datatable --}}
        <script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.min.js"></script>
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
        @yield('scripts')
    </body>

</html>