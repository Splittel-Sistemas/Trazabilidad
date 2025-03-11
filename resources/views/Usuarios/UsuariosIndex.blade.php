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
        font-size: 0.5rem;
        padding: 5px 10px;
        border-radius: 6px;
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
</style>
@endsection
@section('content')
    <!-- Encabezado -->
    <div class="row gy-3 mb-4 justify-content-between align-items-center">
        <div class="col-md-9">
            <h6 class="text-muted">Editar Perfil</h6>
            <h3 class="font-weight-bold">Mi Perfil</h3>
        </div>
        <div class="col-md-3 text-md-end">
            <!-- Aquí podrías agregar un botón u otros elementos si es necesario -->
        </div>
    </div>
    <!--
    <div class="card p-3 mt-3">
        <div class="profile-header d-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-1">Mi Perfil</h4>
                <p class="text-muted mb-2">Administra tu información personal</p>
            </div>
            <div class="text-center">
                <img src="https://via.placeholder.com/100" alt="Foto de perfil" class="profile-img mb-2">
                <br>
                <button class="btn btn-outline-primary btn-custom mt-1" id="changePhotoBtn">
                    <i class="fas fa-camera"></i> Cambiar Foto
                </button>
            </div>
        </div>
    </div>-->
    <!-- Información Personal -->
    <div class="card">
        <div class="card p-3 mt-3 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center p-3" style="border: 1px solid #17a2b8; background-color:">
                <h6 class="mb-0 text-info font-weight-bold">Información Personal</h6>
                <div class="profile-actions">
                    <button class="btn btn-outline-primary btn-sm btn-custom" id="editProfileBtn">
                        <i class="fas fa-user-edit"></i> Editar
                    </button>
                <!-- <button class="btn btn-outline-danger btn-sm btn-custom" id="changePasswordBtn">
                        <i class="fas fa-key"></i> Contraseña
                    </button>-->
                </div>
            </div>
        </div>
        <div class="card p-3 mt-3">
            <div class="card-body">
                <form id="profileForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        @foreach ([
                            ['name', 'Nombre', $user->name],
                            ['apellido', 'Apellido', $user->apellido],
                            ['email', 'Correo Electrónico', $user->email],
                            ['Role', 'Rol', $roles->pluck('name')->join(', ')]
                        ] as [$id, $label, $value])
                            <div class="col-md-6 mb-3">
                                <div class="mb-2">
                                    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
                                    @if ($id === 'Role')
                                        <input type="text" class="form-control form-control-sm" id="{{ $id }}" name="{{ $id }}" value="{{ $value }}" readonly>
                                    @else
                                        <input type="text" class="form-control form-control-sm" id="{{ $id }}" name="{{ $id }}" value="{{ $value }}" readonly>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>           
                    <button type="submit" class="btn btn-success w-100 d-none btn-custom" id="saveProfileBtn" style="font-size: 1rem; padding: 8px;">Guardar</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('editProfileBtn').addEventListener('click', function() {
        const inputs = document.querySelectorAll('#profileForm input');
        let isEditable = false;
        inputs.forEach(input => {
            if (!input.hasAttribute('readonly')) {
                isEditable = true;
            }
        });
        inputs.forEach(input => {
            if (input.id !== 'Role') {
                if (isEditable) {
                    input.setAttribute('readonly', 'true'); 
                } else {
                    input.removeAttribute('readonly'); 
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