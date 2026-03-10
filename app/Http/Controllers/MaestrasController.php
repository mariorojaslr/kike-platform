<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\Formacion;

class MaestrasController extends Controller
{
    public function index(Request $request)
    {
        $filtroEsp = $request->input('f_esp');
        $perPage = $request->input('per_page', 10);
        $formaciones = Formacion::orderBy('nombre', 'asc')->get();

        // Consulta base
        $query = Docente::query();

        if ($filtroEsp) {
            $query->where('formacion_id', $filtroEsp);
        }

        $data = $query->orderBy('nombre', 'asc')->paginate($perPage)->appends($request->all());

        $html = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>SISTEMA AUDITORÍA | Mario Rojas</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
                :root { --primario: #1e3a8a; --acento: #3b82f6; --fondo: #f8fafc; }
                body { background-color: var(--fondo); font-family: 'Poppins', sans-serif; font-size: 0.85rem; color: #334155; }

                .header-main { background: linear-gradient(90deg, #1e3a8a 0%, #1e40af 100%); color: white; padding: 15px 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
                .nav-top { background: white; padding: 0 30px; border-bottom: 1px solid #e2e8f0; display: flex; gap: 10px; }
                .nav-link { color: #64748b; padding: 15px 20px; text-decoration: none; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; border-bottom: 3px solid transparent; transition: 0.3s; }
                .nav-link.active { color: var(--primario); border-bottom: 3px solid var(--acento); }

                .main-box { background: white; border-radius: 20px; margin: 25px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
                .table thead th { background: #f1f5f9; color: var(--primario); font-weight: 700; border: none; padding: 12px; font-size: 0.7rem; }

                .badge-audit { padding: 5px 12px; border-radius: 50px; font-weight: 700; font-size: 0.65rem; display: inline-block; }
                .val { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
                .pen { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

                mark.resaltado { background: #fef08a; color: #854d0e; padding: 0; border-radius: 2px; }
                .row-hidden { display: none !important; }
                .btn-nuevo { background: var(--primario); color: white; border-radius: 10px; padding: 10px 20px; border: none; font-weight: 600; transition: 0.3s; }
                .btn-nuevo:hover { background: #1e40af; transform: translateY(-1px); }
            </style>
        </head>
        <body>
            <div class='header-main d-flex justify-content-between align-items-center'>
                <div class='d-flex align-items-center gap-3'>
                    <i class='fas fa-shield-halved fs-4'></i>
                    <div class='fw-bold fs-5'>SISTEMA DE GESTIÓN | <span class='fw-light'>Auditoría Maestras</span></div>
                </div>
                <div class='fw-bold'>ADMINISTRADOR: MARIO ROJAS <i class='fas fa-user-check ms-2'></i></div>
            </div>

            <nav class='nav-top shadow-sm'>
                <a href='/dashboard' class='nav-link'><i class='fas fa-chart-line me-2'></i>Dashboard</a>
                <a href='/docentes' class='nav-link active'><i class='fas fa-chalkboard-teacher me-2'></i>Maestras</a>
            </nav>

            <div class='main-box'>
                <div class='row g-3 mb-4 align-items-end'>
                    <div class='col-md-4'>
                        <label class='fw-bold mb-2 text-secondary'><i class='fas fa-search me-1'></i> Buscador en Tiempo Real</label>
                        <input type='text' id='inputBusqueda' class='form-control border-2' style='border-radius:10px;' placeholder='DNI, Nombre o Email...'>
                    </div>
                    <div class='col-md-8'>
                        <form method='GET' action='/docentes' class='row g-2 align-items-end' id='formFiltros text-end'>
                            <div class='col-md-5'>
                                <label class='fw-bold mb-2 text-secondary'>Filtrar por Especialidad</label>
                                <select name='f_esp' class='form-select' style='border-radius:10px;' onchange='this.form.submit()'>
                                    <option value=''>TODAS LAS ESPECIALIDADES</option>";
                                    foreach($formaciones as $f) {
                                        $s = ($filtroEsp == $f->id) ? 'selected' : '';
                                        $html .= "<option value='{$f->id}' $s>{$f->nombre}</option>";
                                    }
                        $html .= "</select>
                            </div>
                            <div class='col-md-2'>
                                <label class='fw-bold mb-2 text-secondary'>Ver</label>
                                <select name='per_page' class='form-select' style='border-radius:10px;' onchange='this.form.submit()'>
                                    <option value='10' ".($perPage==10?'selected':'').">10 filas</option>
                                    <option value='25' ".($perPage==25?'selected':'').">25 filas</option>
                                    <option value='50' ".($perPage==50?'selected':'').">50 filas</option>
                                </select>
                            </div>
                            <div class='col-md-5 d-flex gap-2 justify-content-end'>
                                <button type='submit' class='btn btn-outline-dark px-4' style='border-radius:10px;'>FILTRAR</button>
                                <button type='button' class='btn btn-nuevo shadow-sm' onclick='abrirNuevo()'><i class='fas fa-plus me-2'></i>NUEVA MAESTRA</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class='table-responsive'>
                    <table class='table table-hover align-middle'>
                        <thead>
                            <tr>
                                <th>Auditoría</th>
                                <th>Apellido y Nombre</th>
                                <th>DNI</th>
                                <th>Especialidad</th>
                                <th>Contacto</th>
                                <th>Estado</th>
                                <th class='text-center'>Acción</th>
                            </tr>
                        </thead>
                        <tbody>";

                        foreach($data as $doc) {
                            $f_doc = \App\Models\Formacion::find($doc->formacion_id);
                            $nombreEsp = $f_doc ? $f_doc->nombre : '---';
                            $audit = $doc->validado_auditoria ? "<span class='badge-audit val'>VALIDADO</span>" : "<span class='badge-audit pen'>PENDIENTE</span>";
                            $searchData = strtolower("{$doc->nombre} {$doc->dni} {$doc->email} {$doc->telefono}");

                            $html .= "
                            <tr class='fila-maestra' data-search='{$searchData}'>
                                <td>$audit</td>
                                <td class='col-resaltar fw-bold text-dark'>{$doc->nombre}</td>
                                <td class='col-resaltar text-secondary'>{$doc->dni}</td>
                                <td class='col-resaltar text-primary fw-bold'>$nombreEsp</td>
                                <td class='col-resaltar small'>
                                    <i class='fas fa-phone me-1 opacity-50'></i>{$doc->telefono}<br>
                                    <i class='fas fa-envelope me-1 opacity-50'></i>{$doc->email}
                                </td>
                                <td>".($doc->activo ? '<span class="text-success fw-bold">Activo</span>' : '<span class="text-danger">Inactivo</span>')."</td>
                                <td class='text-center'>
                                    <button class='btn btn-sm btn-light border shadow-sm' onclick='abrirEditar(".json_encode($doc).")'>
                                        <i class='fas fa-edit text-primary'></i>
                                    </button>
                                </td>
                            </tr>";
                        }

                $html .= "</tbody></table></div>
                <div class='mt-4 d-flex justify-content-center'>".$data->links('pagination::bootstrap-5')."</div>
            </div>

            <div class='modal fade' id='modalMaestra' tabindex='-1'>
                <div class='modal-dialog modal-lg modal-dialog-centered'>
                    <form action='/maestras/guardar' method='POST' class='modal-content border-0 shadow-lg' style='border-radius:20px;'>
                        ".csrf_field()."
                        <input type='hidden' name='id' id='m_id'>
                        <div class='modal-header bg-primary text-white' style='border-radius: 20px 20px 0 0;'>
                            <h5 class='modal-title fw-bold'><i class='fas fa-user-edit me-2'></i>Ficha Docente</h5>
                        </div>
                        <div class='modal-body p-4 row g-3'>
                            <div class='col-md-8'><label class='small fw-bold'>Nombre Completo</label><input type='text' name='nombre' id='m_nom' class='form-control shadow-sm' required></div>
                            <div class='col-md-4'><label class='small fw-bold'>DNI</label><input type='text' name='dni' id='m_dni' class='form-control shadow-sm'></div>
                            <div class='col-md-6'><label class='small fw-bold'>Especialidad / Formación</label><select name='formacion_id' id='m_esp' class='form-select shadow-sm'>";
                                foreach($formaciones as $f) { $html .= "<option value='{$f->id}'>{$f->nombre}</option>"; }
                $html .= "      </select></div>
                            <div class='col-md-3'><label class='small fw-bold'>Auditoría</label><select name='validado_auditoria' id='m_audit' class='form-select shadow-sm fw-bold text-primary'><option value='1'>VALIDADO</option><option value='0'>PENDIENTE</option></select></div>
                            <div class='col-md-3'><label class='small fw-bold'>Estado</label><select name='activo' id='m_act' class='form-select shadow-sm'><option value='1'>ACTIVO</option><option value='0'>INACTIVO</option></select></div>
                            <div class='col-md-6'><label class='small fw-bold'>Teléfono</label><input type='text' name='telefono' id='m_tel' class='form-control shadow-sm'></div>
                            <div class='col-md-6'><label class='small fw-bold'>Email Personal</label><input type='email' name='email' id='m_mail' class='form-control shadow-sm'></div>
                            <div class='col-md-12'><label class='small fw-bold'>Dirección de Residencia</label><input type='text' name='direccion' id='m_dir' class='form-control shadow-sm'></div>
                        </div>
                        <div class='modal-footer bg-light p-3' style='border-radius: 0 0 20px 20px;'>
                            <button type='button' class='btn btn-link text-secondary text-decoration-none' data-bs-dismiss='modal'>Cerrar</button>
                            <button type='submit' class='btn btn-primary px-5 fw-bold shadow'>GUARDAR REGISTRO</button>
                        </div>
                    </form>
                </div>
            </div>

            <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
            <script>
                // BUSCADOR REALTIME CON RESALTADO
                document.getElementById('inputBusqueda').addEventListener('input', function(e) {
                    const busqueda = e.target.value.toLowerCase().trim();
                    const filas = document.querySelectorAll('.fila-maestra');

                    filas.forEach(f => {
                        const textoSearch = f.getAttribute('data-search');
                        const celdas = f.querySelectorAll('.col-resaltar');

                        if (busqueda === '') {
                            f.classList.remove('row-hidden');
                            celdas.forEach(td => {
                                if (td.hasAttribute('data-original')) {
                                    td.innerHTML = td.getAttribute('data-original');
                                }
                            });
                            return;
                        }

                        if (textoSearch.includes(busqueda)) {
                            f.classList.remove('row-hidden');
                            celdas.forEach(td => {
                                if (!td.hasAttribute('data-original')) {
                                    td.setAttribute('data-original', td.innerHTML);
                                }
                                let original = td.getAttribute('data-original');
                                const regex = new RegExp('(' + busqueda + ')', 'gi');
                                td.innerHTML = original.replace(regex, '<mark class=\"resaltado\">$1</mark>');
                            });
                        } else {
                            f.classList.add('row-hidden');
                        }
                    });
                });

                const modal = new bootstrap.Modal(document.getElementById('modalMaestra'));
                function abrirNuevo() {
                    document.getElementById('m_id').value = '';
                    document.querySelector('#modalMaestra form').reset();
                    modal.show();
                }
                function abrirEditar(d) {
                    document.getElementById('m_id').value = d.id;
                    document.getElementById('m_nom').value = d.nombre;
                    document.getElementById('m_dni').value = d.dni;
                    document.getElementById('m_esp').value = d.formacion_id;
                    document.getElementById('m_audit').value = d.validado_auditoria;
                    document.getElementById('m_act').value = d.activo;
                    document.getElementById('m_tel').value = d.telefono;
                    document.getElementById('m_mail').value = d.email;
                    document.getElementById('m_dir').value = d.direccion;
                    modal.show();
                }
            </script>
        </body></html>";

        return response($html);
    }

    public function store(Request $request) {
        $id = $request->input('id');
        $obj = $id ? Docente::findOrFail($id) : new Docente;
        $obj->fill($request->all());
        $obj->save();
        return redirect('/docentes');
    }
}
