@extends('layouts.menu')

@section('title', 'Suministros')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/FormularioSuministros.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateForm() {
            // Validación de Orden de Fabricación
            var ordenFabricacion = document.getElementById('ordenFabricacion').value;
            if (ordenFabricacion.trim() === "") {
                alert("La Orden de Fabricación es obligatoria.");
                return false;
            }

            // Validación de Orden de Parte
            var ordenParte = document.getElementById('ordenParte').value;
            if (ordenParte.trim() === "") {
                alert("La Orden de Parte es obligatoria.");
                return false;
            }

            // Validación de Cantidad
            var cantidad = document.getElementById('cantidad').value;
            if (cantidad.trim() === "" || isNaN(cantidad) || cantidad < 1) {
                alert("La Cantidad debe ser un número mayor a 0.");
                return false;
            }

            return true;
        }
    </script>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="form-container">
            <h1 class="text-center mb-4">Formulario de Suministros de Fibra Óptica</h1>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="form-fibra-optica" action="{{ route('suministros.enviar') }}" method="POST" onsubmit="return validateForm()">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="ordenFabricacion">Orden de Fabricación:</label>
                        <input type="text" class="form-control form-control-sm" id="ordenFabricacion" name="ordenFabricacion" placeholder="Ingrese la orden de fabricación" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="ordenParte">Orden de Parte:</label>
                        <input type="text" class="form-control form-control-sm" id="ordenParte" name="ordenParte" placeholder="Ingrese la orden de parte" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" class="form-control form-control-sm" id="cantidad" name="cantidad" placeholder="Ingrese la cantidad" min="1" required>
                    </div>
                </div>

                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="remember"> Remember me
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-custom-size mb-2">Enviar</button>
            </form>
        </div>
    </div>
@endsection
