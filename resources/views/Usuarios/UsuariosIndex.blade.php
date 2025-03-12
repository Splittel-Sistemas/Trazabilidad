@extends('layouts.menu2') 
@section('title', 'Perfil de Usuario') 
@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .profile-img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #007bff;
        transition: transform 0.3s;
    }
    .profile-img:hover {
        transform: scale(1.05);
    }
    .card {
        box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    .btn-custom {
        font-size: 0.8rem;
        padding: 6px 10px;
        border-radius: 90px;
    }
    .container-custom {
        max-width: 900px;
        margin: 20px auto;
    }
    .profile-header {
        margin-bottom: 15px;
    }
    .profile-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }
    .hr2{
            border-top: 5px solid rgb(22, 95, 163);
            width: 100%;
            margin: 2px auto;
        }
</style>
@endsection
@section('content')
<!-- Encabezado -->
<div class="row gy-3 mb-4 justify-content-between">
    <div class="col-md-9 col-auto">
        <h4 class="mb-2 text-1100 font-weight-bold">Mi Perfil</h4>
    </div>
</div>
<div class="container my-1">
    <div class="card p-4 mt-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 font-weight-bold">Perfil de Usuario</h5>
            <button class="btn btn-outline-primary btn-sm" id="editProfileBtn">
                <i class="fas fa-user-edit"></i> Editar
            </button>
        </div>
        <hr class="my-3">
        <form id="profileForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-1">
                <div class="row justify-content-center">
                    <div class="col-md-5 mb-2">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control form-control-sm" id="name" name="name" value="{{ $user->name }}" readonly
                            style="background-color: #f8f9fa; cursor: not-allowed;">
                    </div>
                    <div class="col-md-5 mb-2">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control form-control-sm" id="apellido" name="apellido" value="{{ $user->apellido }}" readonly
                            style="background-color: #f8f9fa; cursor: not-allowed;">
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-5 mb-2">
                        <label for="Role" class="form-label">Rol</label>
                        <input type="text" class="form-control form-control-sm" id="Role" name="Role" value="{{ $roles->pluck('name')->join(', ') }}" readonly
                            style="background-color: #e9ecef; cursor: not-allowed;">
                    </div>
                    @if ($user->role === 'A')
                        <div class="col-md-5 mb-2">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="text" class="form-control form-control-sm" id="email" name="email" value="{{ $user->email }}" readonly
                                style="background-color: #f8f9fa; cursor: not-allowed;">
                        </div>
                    @endif
                </div>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success btn-sm d-none" id="saveProfileBtn" style="font-size: 0.875rem; padding: 6px 24px;">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('editProfileBtn').addEventListener('click', function() {
            const inputs = document.querySelectorAll('#profileForm input');
            let isEditable = false;
            inputs.forEach(input => {
                if (!input.hasAttribute('readonly') && input.id !== 'Role') {
                    isEditable = true;
                }
            });
            inputs.forEach(input => {
                if (input.id !== 'Role') {
                    if (isEditable) {
                        input.setAttribute('readonly', 'true');
                        input.style.backgroundColor = "#e9ecef"; 
                        input.style.cursor = "not-allowed";
                    } else {
                        input.removeAttribute('readonly');
                        input.style.backgroundColor = "#ffffff"; 
                        input.style.cursor = "text";
                    }
                }
            });
            const saveButton = document.getElementById('saveProfileBtn');
            if (isEditable) {
                saveButton.classList.add('d-none');
            } else {
                saveButton.classList.remove('d-none');
            }
        });
    });
    $('#profileForm').on('submit', function(e) {
        e.preventDefault(); 
        var formData = new FormData(this);
        formData.append('_method', 'PUT');
        $.ajax({
            url: "{{ route('update.perfil') }}", 
            type: 'POST',  
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'Éxito',
                    text: response.message, 
                    icon: 'success',
                    confirmButtonText: 'Cerrar'
                });
                setTimeout(function() {
                    location.reload(); 
                }, 2000); 
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un problema al actualizar el perfil. Inténtelo nuevamente.',
                    icon: 'error',
                    confirmButtonText: 'Cerrar'
                });
            }
        });
    });
</script>
@endsection