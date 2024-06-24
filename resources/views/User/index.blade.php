@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1>Listado de Usuarios</h1>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#createUserModal"
            style="margin-top: 15px; background-color:darkblue !important;">
            <i class="fas fa-plus"></i> Añadir Usuario
        </button>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="user-table" class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <button class="btn btn-primary text-white mx-2 edit-user-btn"
                                                style="background-color: darkblue !important;"
                                                data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                                <i class="far fa-edit"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-danger text-white mx-2"
                                                onclick="confirmDelete({{ $user->id }})">
                                                <i class="far fa-trash-alt"></i> Eliminar
                                            </button>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                id="delete-form-{{ $user->id }}" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Crear Usuario -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Agregar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createUserForm" action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="create-name">Nombre:</label>
                            <input type="text" class="form-control" id="create-name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div id="create-name-error" class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="create-email">Correo Electrónico:</label>
                            <input type="email" class="form-control" id="create-email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div id="create-email-error" class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="create-password">Contraseña:</label>
                            <input type="password" class="form-control" id="create-password" name="password" required>
                            @error('password')
                                <div id="create-password-error" class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary"
                                style="background-color: darkblue !important;">Agregar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Usuario -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="edit-name">Nombre:</label>
                            <input type="text" class="form-control" id="edit-name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div id="edit-name-error" class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="edit-email">Correo Electrónico:</label>
                            <input type="email" class="form-control" id="edit-email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div id="edit-email-error" class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="edit-current-password">Contraseña Actual:</label>
                            <input type="password" class="form-control" id="edit-current-password" name="current_password">
                            @error('current_password')
                                <div id="edit-current-password-error" class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="edit-password">Nueva Contraseña:</label>
                            <input type="password" class="form-control" id="edit-password" name="password">
                            @error('password')
                                <div id="edit-password-error" class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="edit-password_confirmation">Confirmar Nueva Contraseña:</label>
                            <input type="password" class="form-control" id="edit-password_confirmation" name="password_confirmation">
                            @error('password_confirmation')
                                <div id="edit-password_confirmation-error" class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary"
                                style="background-color: darkblue !important;">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .sidebar-dark-orange {
            background-color: darkblue !important;
        }

        li.paginate_button.page-item.active {
            background-color: darkblue !important;
            color: white !important;
        }

        a {
            text-decoration: none;
        }
    </style>
@stop

@section('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        $(document).ready(function () {
            $('#user-table').DataTable({
                language: {
                    info: 'Mostrando página _PAGE_ de _PAGES_',
                    infoEmpty: 'No hay registros disponibles',
                    infoFiltered: '(filtrado de _MAX_ registros totales)',
                    lengthMenu: 'Mostrar _MENU_ registros por página',
                    zeroRecords: 'Nada encontrado - lo siento',
                    paginate: {
                        first: 'Primero',
                        last: 'Último',
                        next: 'Siguiente',
                        previous: 'Anterior'
                    },
                    search: 'Buscar:',
                    loadingRecords: 'Cargando...',
                    processing: 'Procesando...',
                    emptyTable: 'No hay datos disponibles en la tabla',
                    infoThousands: ',',
                    decimal: ',',
                    thousands: '.'
                }
            });

            @if(session('modal') === 'create')
                $('#createUserModal').modal('show');
            @elseif(session('modal') === 'edit')
                $('#editUserModal').modal('show');
            @endif

            $('.edit-user-btn').click(function () {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var email = $(this).data('email');

                $('#editUserForm').attr('action', '/users/' + id);
                $('#edit-name').val(name);
                $('#edit-email').val(email);
            });
        });

        function confirmDelete(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esto.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@stop
