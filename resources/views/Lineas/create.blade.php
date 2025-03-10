@extends('layouts.menu2') 
@section('title', 'Crear Linea')

<style>
    .roles-container {
    display: flex;
    flex-wrap: wrap; /* Permite que los elementos pasen a una nueva línea si no caben */
    gap: 10px;       /* Espaciado entre elementos */
}

.form-check {
    display: flex;
    align-items: center; /* Alinea el checkbox con el texto */
}

    
</style>
@section('content')
<!--
        <div style="height: 30px;"></div>
        <div class="breadcrumbs mb-4">
            <div class="row gy-3 mb-2 justify-content-between">
                <div class="col-md-9 col-auto">
                    <h4 class="mb-2 text-1100">Crear Nueva Línea</h4>
                </div>
            </div>
        
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        
            <div style="height: 30px;"></div>
        
            <div class="card">
                <form action="{{ route('linea.store') }}" method="POST" class="p-3 rounded bg-white">
                    @csrf
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Nombre">Nombre</label>
                                <input type="text" name="Nombre" id="Nombre" class="form-control form-control-sm" placeholder="Ingrese el nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="NumeroLinea">Número de Línea</label>
                                <input type="text" name="NumeroLinea" id="NumeroLinea" class="form-control form-control-sm" placeholder="Ingrese el número de línea" required>
                            </div>
                        </div>
                    </div>
        
                    <div class="row mb-1">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Descripcion">Descripción</label>
                                <input type="text" name="Descripcion" id="Descripcion" class="form-control form-control-sm" placeholder="Ingrese la descripción" required>
                            </div>
                        </div>
                    </div>
        
                    <div class="d-flex justify-content-center mt-3">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-lg transition-all hover:bg-success hover:text-white">
                            Registrar
                        </button>
                    </div>   
                </form>
            </div>
        </div>-->        
<script>
        
</script>


    
@endsection

