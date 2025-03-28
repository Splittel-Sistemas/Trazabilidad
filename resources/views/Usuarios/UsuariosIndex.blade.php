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
    .usuario-icono{
        background: #c00000;
        color: white;
        padding: 1.3rem;
       font-size: 5rem;
       border-radius: 100px;
    }
    .hr2{
            border-top: 1px solid rgb(255, 255, 255);
            width: 100%;
            margin: 2px auto;
    }
    
</style>
@endsection
@section('content')
<!-- Encabezado -->
<div class="row gy-3 mb-4 justify-content-between">
    <div class="col-md-9 col-auto">
        <h4 class="mb-2 font-weight-bold">Mi Perfil</h4>
    </div>
</div>
<div class="container my-0">
    <div class="card p-4 mt-2" style="background-color: rgba(0, 0, 0, 0.1);">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h4 class="mb-0 font-weight-bold text-danger text-uppercase">Hola {{ $user->name }}</h4>
            <button class="btn btn-danger btn-sm" id="editProfileBtn">
                <i class="fas fa-user-edit"></i> Editar
            </button>
        </div>
        <hr class="my-3 hr2">
        <form id="profileForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-1">
                <div class="row justify-content-center">
                    <div class="col-sm-12 text-center">
                        <i class="far fa-user usuario-icono"></i>
                    </div>
                    <div class="col-md-5 mb-2">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control form-control-sm" id="name" name="name" value="{{ $user->name }}" disabled>
                    </div>
                    <div class="col-md-5 mb-2">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control form-control-sm" id="apellido" name="apellido" value="{{ $user->apellido }}" disabled>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-5 mb-2">
                        <label for="Role" class="form-label">Rol</label>
                        <input type="text" class="form-control form-control-sm" id="Role" name="Role" value="{{ $roles->pluck('name')->join(', ') }}" disabled>
                    </div>
                    @if ($user->role === 'A')
                        <div class="col-md-5 mb-2">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="text" class="form-control form-control-sm" id="email" name="email" value="{{ $user->email }}" disabled
                                >
                        </div>
                    @endif
                </div>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-sm d-none" id="saveProfileBtn" style="font-size: 0.875rem; padding: 6px 24px;">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('editProfileBtn').addEventListener('click', function() {
            const inputs = document.querySelectorAll('#profileForm input');
            const saveButton = document.getElementById('saveProfileBtn');
            if (!saveButton.classList.contains('d-none')) {
                saveButton.classList.add('d-none');
                $('#name').prop('disabled', true);
                $('#apellido').prop('disabled', true);
                $('#email').prop('disabled', true);
            } else {
                saveButton.classList.remove('d-none');
                $('#name').prop('disabled', false);
                $('#apellido').prop('disabled', false);
                $('#email').prop('disabled', false);
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