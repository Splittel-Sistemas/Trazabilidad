@extends('layouts.menu')

@section('content')
<div class="container">
    <h2>Proceso de la trazabilidad</h2>
    <div class="progress-container">
        <!-- Barra de progreso -->
        <div class="progress">
            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
        
        <!-- Selección de número de orden -->
        <div class="form-group">
            <label for="orderNumber">Número de Orden:</label>
            <input type="number" id="orderNumber" class="form-control" placeholder="Ingrese el número de orden">
        </div>

        <!-- Lista de etapas -->
        <ul class="progress-bar-stages">
            <li class="stage pending" id="stage1">
                <div class="stage-circle">
                    <i class="fas fa-cogs"></i>
                </div>
                <span>1. Área de preparado</span>
            </li>
            <li class="stage pending" id="stage2">
                <div class="stage-circle">
                    <i class="fas fa-cogs"></i>
                </div>
                <span>2. Ensamble</span>
            </li>
            <li class="stage pending" id="stage3">
                <div class="stage-circle">
                    <i class="fas fa-cogs"></i>
                </div>
                <span>3. Pulido</span>
            </li>
            <li class="stage pending" id="stage4">
                <div class="stage-circle">
                    <i class="fas fa-cogs"></i>
                </div>
                <span>4. Medición</span>
            </li>
            <li class="stage pending" id="stage5">
                <div class="stage-circle">
                    <i class="fas fa-box"></i>
                </div>
                <span>5. Empaque</span>
            </li>
        </ul>
    </div>

    <!-- Botones para avanzar y retroceder -->
    <div class="text-center mt-4">
        <button class="btn btn-primary" id="advanceBtn">Avanzar etapa</button>
        <button class="btn btn-secondary ml-2" id="backBtn">Retroceder etapa</button>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Contenedor principal */
    .progress-container {
        width: 100%;
        padding: 20px 0;
        text-align: center;
        position: relative;
        margin-bottom: 40px;
    }

    /* Barra de progreso */
    .progress {
        width: 100%;
        height: 30px;
        background-color: #f0f0f0;
        border-radius: 15px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
        height: 100%;
        text-align: center;
        color: white;
        line-height: 30px;
        border-radius: 15px;
        transition: width 0.5s ease, background-color 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    /* Lista de etapas */
    .progress-bar-stages {
        list-style: none;
        display: flex;
        justify-content: space-between;
        padding: 0;
        margin: 0;
        position: relative;
        width: 100%;
        align-items: center;
    }

    .stage {
        position: relative;
        text-align: center;
        flex: 1;
        transition: all 0.4s ease;
        color: #999;
        cursor: pointer;
        opacity: 0.7;
    }

    .stage:hover {
        opacity: 1;
    }

    .stage-circle {
        width: 60px;
        height: 60px;
        background-color: #e0e0e0;
        border-radius: 50%;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: background-color 0.3s ease, transform 0.3s ease;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.15);
    }

    .stage-circle i {
        font-size: 24px;
        color: #fff;
    }

    .stage.pending .stage-circle {
        background-color: #e0e0e0;
        transform: scale(1);
    }

    .stage.pending span {
        color: #999;
    }

    .stage.completed .stage-circle {
        background-color: #28a745;
        transform: scale(1.2);
        box-shadow: 0 0 12px rgba(0, 255, 0, 0.5);
    }

    .stage.completed span {
        color: #28a745;
        font-weight: bold;
    }

    /* Estilos de los botones */
    .btn-primary, .btn-secondary {
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 25px;
        transition: background-color 0.3s ease, transform 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
        transform: scale(1.05);
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #4e555b;
        transform: scale(1.05);
    }

    .btn-primary:active, .btn-secondary:active {
        transform: scale(1);
    }

    .ml-2 {
        margin-left: 10px;
    }
</style>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        let currentStage = 0;

        $('#advanceBtn').click(function () {
            if (currentStage < 5) {
                currentStage++;
                updateProgress(currentStage);
            }
        });

        $('#backBtn').click(function () {
            if (currentStage > 0) {
                currentStage--;
                updateProgress(currentStage);
            }
        });

        function updateProgress(stage) {
            let progressWidth = (stage / 5) * 100;
            $('#progressBar')
                .css('width', progressWidth + '%')
                .text(Math.round(progressWidth) + '%');

            const progressColor = getProgressColor(progressWidth);
            $('#progressBar').css('background-color', progressColor);

            $('.stage').each(function (index) {
                if (index < stage) {
                    $(this).addClass('completed').removeClass('pending');
                } else {
                    $(this).addClass('pending').removeClass('completed');
                }
            });
        }

        function getProgressColor(progressWidth) {
            if (progressWidth <= 20) return '#dc3545';
            if (progressWidth <= 40) return '#ff851b';
            if (progressWidth <= 60) return '#ffc107';
            if (progressWidth <= 80) return '#17a2b8';
            return '#28a745';
        }

        updateProgress(currentStage);
    });
</script>
@endsection

