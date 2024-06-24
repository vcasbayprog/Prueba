@extends('adminlte::page')

@section('title', 'Días Festivos')

@section('content_header')
    <h1>Listado de Días Festivos</h1>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-primary text-white" style="margin-top: 15px; background-color:darkblue !important;"
            data-bs-toggle="modal" data-bs-target="#createHolidayModal">
            <i class="fas fa-plus"></i> Agregar Día Festivo
        </button>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="holidays-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Color</th>
                                    <th>Fecha</th>
                                    <th>Recurrente</th>
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
                                @foreach ($holidays as $holiday)
                                    <tr>
                                        <td>{{ $holiday->id }}</td>
                                        <td>{{ $holiday->name }}</td>
                                        <td><span
                                                style="background-color: {{ $holiday->color }}; display: inline-block; width: 20px; height: 20px;"></span>
                                        </td>
                                        <td>{{\Carbon\Carbon::parse($holiday->date)->isoFormat('D [del] MM [del] YYYY') }}</td>

                                        <td>{{ $holiday->recurrent ? 'Sí' : 'No' }}</td>
                                        <td>
                                            <button class="btn btn-primary text-white mx-2"
                                                style="background-color: darkblue !important;"
                                                onclick="editHoliday({{ $holiday->id }}, '{{ $holiday->name }}', '{{ $holiday->color }}', '{{ $holiday->date->format('Y-m-d') }}', {{ $holiday->recurrent ? 'true' : 'false' }})">
                                                <i class="far fa-edit"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-danger text-white mx-2"
                                                onclick="confirmDelete({{ $holiday->id }})">
                                                <i class="far fa-trash-alt"></i> Eliminar
                                            </button>
                                            <form action="{{ route('holidays.destroy', $holiday) }}" method="POST"
                                                id="delete-form-{{ $holiday->id }}" style="display: none;">
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
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .sidebar-dark-orange {
            background-color: darkblue !important;
        }

        th {
            background-color: darkblue !important;
            color: white !important;
        }

        a{
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
        $(document).ready(function() {
            $('#holidays-table').DataTable({
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
        });

        function editHoliday(id, name, color, date, recurrent) {
            $('#holiday_id').val(id);
            $('#name').val(name);
            $('#color').val(color);
            $('#date').val(date);
            $('#recurrent').prop('checked', recurrent);
            $('#editHolidayForm').attr('action', '/holidays/' + id);
            $('#editHolidayModal').modal('show');
        }

        function confirmDelete(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esto.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#00008B',
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

<!-- Modal para editar días festivos -->

<div class="modal fade" id="editHolidayModal" tabindex="-1" aria-labelledby="editHolidayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editHolidayModalLabel">Editar Día Festivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editHolidayForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="holiday_id" id="holiday_id">

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="color" class="form-control" id="color" name="color">
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="recurrent" name="recurrent"
                                value="1">
                            <label class="form-check-label" for="recurrent">Recurrente (Todos los años)</label>
                            
                            </label>
                        </div>
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



<!-- Modal para crear días festivos -->
<div class="modal fade" id="createHolidayModal" tabindex="-1" aria-labelledby="createHolidayModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createHolidayModalLabel">Agregar Día Festivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createHolidayForm" method="POST" action="{{ route('holidays.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="create-name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="create-name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="create-color" class="form-label">Color</label>
                        <input type="color" class="form-control" id="create-color" name="color">
                    </div>

                    <div class="mb-3">
                        <label for="create-date" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="create-date" name="date" required>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="create-recurrent" name="recurrent" value="1">
                            <label class="form-check-label" for="create-recurrent">
                                Recurrente
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"
                            style="background-color: darkblue !important;">Agregar Día Festivo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
