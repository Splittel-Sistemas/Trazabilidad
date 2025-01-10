<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>@yield('title', 'splittel')</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="{{asset('menu1/css/styles.css')}}">
        <link rel="stylesheet" href="{{asset('css/menu.css')}}">
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        @yield('styles')    
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-light shadow-sm " style="background-color: #ffffff;">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="./"><img src="{{asset('imagenes/splittel.png') }}" alt="Logo" width="100" height="40"></a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar-->
            <div class="ms-auto d-flex align-items-center">
                <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4 float-end">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#!">Settings</a></li>
                            <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                            <li><hr class="dropdown-divider" /></li>
                            <li><a class="dropdown-item" href="#!">Cerrar sesi&oacute;n</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-light shadow" id="sidenavAccordion" style="background-color: #ffffff;">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Trazabilidad</div>
                            <a class="nav-link {{ Route::is('Home') ? 'active' : '' }}" href="{{route('Home')}}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <a class="nav-link {{ Route::is('registro.index') ? 'active' : '' }}" href="{{route('registro.index')}}">
                                <div class="sb-nav-link-icon"><i class="fas fa fa-user"></i></div>
                                Usuarios
                            </a>
                            <a class="nav-link {{ Route::is('RolesPermisos.index') ? 'active' : '' }}" href="{{route('RolesPermisos.index')}}">
                                <div class="sb-nav-link-icon"><i class="fas fa-users-rectangle"></i></div>
                                Permisos
                            </a>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa fa-cogs"></i></div>
                                Areas
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link {{ Route::is('Planeacion') ? 'nav-tabs active' : '' }}" href="{{route('Planeacion')}}"><i class="fa fa-calendar m-1"></i> Planeaci&oacute;n</a>
                                    <a class="nav-link {{ Route::is('corte.index') ? 'nav-tabs active' : '' }}" href="{{route('corte.index')}}"><i class="fa fa-cut m-1"></i> Corte</a>
                                    <a class="nav-link {{ Route::is('Suministro') ? 'nav-tabs active' : '' }}" href="{{route('Suministro')}}"><i class="fa fa-archive m-1"></i> Suministro</a>
                                    <a class="nav-link" href="ui-cards.html">Preparado</a>
                                    <a class="nav-link" href="ui-alerts.html">Ensamble</a>
                                    <a class="nav-link" href="ui-progressbar.html">Pulido</a>
                                    <a class="nav-link" href="ui-modals.html">Medici&oacute;n</a>
                                    <a class="nav-link" href="ui-switches.html">Visualizaci&oacute;n</a>
                                    <a class="nav-link" href="layout-static.html">Static Navigation</a>
                                    <a class="nav-link" href="layout-sidenav-light.html">Light Sidenav</a>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        Start Bootstrap
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4 mt-4">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="{{asset('menu1/js/scripts.js')}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="{{asset('menu1/assets/demo/chart-area-demo.js')}}"></script>
        <script src="{{asset('menu1/assets/demo/chart-bar-demo.js')}}"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="{{asset('menu1/js/datatables-simple-demo.js')}}"></script>
        @yield('scripts')
    </body>