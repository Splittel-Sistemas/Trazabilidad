@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Registrar Corte</h2>
    
    <!-- Mensaje de éxito, si lo hubiera -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Formulario de registro de corte -->
    <form action="{{ url('/corte') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="orden_venta_id" class="form-label">Orden de Venta</label>
            <input type="number" class="form-control" id="orden_venta_id" name="orden_venta_id" required>
        </div>
        <div class="mb-3">
            <label for="orden_fabricacion_id" class="form-label">Orden de Fabricación</label>
            <input type="number" class="form-control" id="orden_fabricacion_id" name="orden_fabricacion_id" required>
        </div>
        <div class="mb-3">
            <label for="numero_corte" class="form-label">Número de Corte</label>
            <input type="number" class="form-control" id="numero_corte" name="numero_corte" required>
        </div>
        <div class="mb-3">
            <label for="cantidad_cortada" class="form-label">Cantidad Cortada</label>
            <input type="number" class="form-control" id="cantidad_cortada" name="cantidad_cortada" required>
        </div>

        <button type="submit" class="btn btn-primary">Registrar Corte</button>
    </form>

    <hr>
    <h3>Cortes Realizados</h3>
    <div id="cortesList">
        <p>Aún no hay cortes registrados.</p>
    </div>
</div>

<script>
    // Si quieres mostrar los cortes sin backend, puedes usar datos ficticios.
    function showCortes() {
        const cortes = [
            { numero_corte: 1, cantidad_cortada: 100 },
            { numero_corte: 2, cantidad_cortada: 150 },
            { numero_corte: 3, cantidad_cortada: 200 }
        ];

        const cortesList = document.getElementById('cortesList');
        if (cortes.length > 0) {
            let html = '<ul>';
            cortes.forEach(corte => {
                html += `<li>Corte #${corte.numero_corte} - Cantidad: ${corte.cantidad_cortada}</li>`;
            });
            html += '</ul>';
            cortesList.innerHTML = html;
        } else {
            cortesList.innerHTML = '<p>Aún no hay cortes registrados.</p>';
        }
    }

    // Llamar a la función al cargar la página
    showCortes();
</script>
@endsection
