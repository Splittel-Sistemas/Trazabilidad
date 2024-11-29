@extends('layouts.menu')

@section('content')
<div class="container">
    <h2 class="text-center my-4">Proceso de Trazabilidad</h2>
    <div class="progress-container">
        <!-- Barra de progreso -->
        <div class="progress">
            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>

        <!-- Número de Orden -->
        <div class="form-group text-center my-3">
            <label for="orderNumber" class="font-weight-bold">Número de Orden</label>
            <input type="number" id="orderNumber" class="form-control w-50 mx-auto" placeholder="Ingrese el número de orden">
        </div>

        <!-- Etapas -->
        <ul class="progress-bar-stages">
            <li class="stage" id="stage1">
                <div class="stage-circle">
                    <i class="fas fa-tools"></i>
                </div>
                <span>Área de preparado</span>
            </li>
            <li class="stage" id="stage2">
                <div class="stage-circle">
                    <i class="fas fa-cogs"></i>
                </div>
                <span>Ensamble</span>
            </li>
            <li class="stage" id="stage3">
                <div class="stage-circle">
                    <i class="fas fa-broom"></i>
                </div>
                <span>Pulido</span>
            </li>
            <li class="stage" id="stage4">
                <div class="stage-circle">
                    <i class="fas fa-ruler"></i>
                </div>
                <span>Medición</span>
            </li>
            <li class="stage" id="stage5">
                <div class="stage-circle">
                    <i class="fas fa-box-open"></i>
                </div>
                <span>Empaque</span>
            </li>
        </ul>
    </div>

    <!-- Botones -->
    <div class="text-center mt-4">
        <button class="btn btn-success" id="advanceBtn">Avanzar Etapa</button>
        <button class="btn btn-danger ml-2" id="backBtn">Retroceder Etapa</button>
        <button class="btn btn-primary ml-2" id="storeBtn">Almacenar en Base de Datos</button>
    </div>
</div>
@endsection

@section('styles')
<!-- (El estilo se mantiene igual que el anterior) -->
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentStage = 0;

    $('#advanceBtn').click(function() {
        if (currentStage < 5) {
            currentStage++;
            updateProgress(currentStage);
        }
    });

    $('#backBtn').click(function() {
        if (currentStage > 0) {
            currentStage--;
            updateProgress(currentStage);
        }
    });

    $('#storeBtn').click(function() {
        const orderNumber = $('#orderNumber').val();
        if (!orderNumber) {
            alert('Por favor, ingrese el número de orden.');
            return;
        }

        const data = {
            orderNumber: orderNumber,
            currentStage: currentStage,
        };

        
    });

    function updateProgress(stage) {
        let progressWidth = (stage / 5) * 100;
        $('#progressBar').css('width', progressWidth + '%').text(Math.round(progressWidth) + '%');

        let color;
        if (progressWidth <= 20) {
            color = '#dc3545';
        } else if (progressWidth <= 40) {
            color = '#ff851b';
        } else if (progressWidth <= 60) {
            color = '#ffc107';
        } else if (progressWidth <= 80) {
            color = '#28a745';
        } else {
            color = '#007bff';
        }
        $('#progressBar').css('background', `linear-gradient(90deg, ${color}, ${shadeColor(color, -20)})`);

        $('.stage').each(function(index) {
            if (index < stage) {
                $(this).addClass('completed');
                $(this).find('.stage-circle').css('background', `linear-gradient(90deg, ${color}, ${shadeColor(color, -20)})`);
            } else {
                $(this).removeClass('completed');
                $(this).find('.stage-circle').css('background', '#ccc');
            }
        });
    }

    function shadeColor(color, percent) {
        const num = parseInt(color.replace("#", ""), 16),
            amt = Math.round(2.55 * percent),
            R = (num >> 16) + amt,
            G = ((num >> 8) & 0x00ff) + amt,
            B = (num & 0x0000ff) + amt;
        return (
            "#" +
            (0x1000000 +
                (R < 255 ? (R < 1 ? 0 : R) : 255) * 0x10000 +
                (G < 255 ? (G < 1 ? 0 : G) : 255) * 0x100 +
                (B < 255 ? (B < 1 ? 0 : B) : 255))
                .toString(16)
                .slice(1)
        );
    }
});
</script>
@endsection
