@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5 text-center">
                <h1 class="display-5 fw-bold text-primary">Registro de Prestaciones</h1>
                <p class="lead">Sistema de gestión para el seguimiento de prestaciones de maestras.</p>
                <hr class="my-4">
                <p>La estructura base está lista. Ahora podemos proceder a definir los campos según tu planilla de Excel.</p>

                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <button type="button" class="btn btn-primary btn-lg px-4 gap-3">Gestionar Prestaciones</button>
                    <button type="button" class="btn btn-outline-secondary btn-lg px-4">Ver Escuelas</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
