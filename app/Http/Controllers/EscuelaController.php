<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Escuela;
use App\Models\Provincia;
use App\Models\Localidad;
use Illuminate\Support\Facades\Schema;

class EscuelaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $data = Escuela::orderBy('nombre', 'asc')->paginate($perPage);
        $provincias = Provincia::orderBy('nombre', 'asc')->get();

        $html = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Escuelas | Gestión</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
                body { background: #f4f7f6; font-family: 'Poppins', sans-serif; font-size: 0.85rem; }
                .main-card { background: white; border-radius: 15px; margin: 20px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
                .table thead { background: #0f172a; color: white; }
                .text-truncate-custom { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
                @media (max-width: 768px) { .hide-mobile { display: none; } }
            </style>
        </head>
        <body>
            <div class='p-3 bg-dark text-white d-flex justify-content-between align-items-center'>
                <span class='fw-bold'><i class='fas fa-school text-warning me-2'></i>ESCUELAS</span>
                <button class='btn btn-warning btn-sm fw-bold' onclick='abrirNuevo()'>+ NUEVA</button>
            </div>

            <div class='main-card'>
                <div class='table-responsive'>
                    <table class='table table-hover align-middle'>
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Institución</th>
                                <th class='hide-mobile'>Contacto (Email / Tel)</th>
                                <th class='text-center'>Acción</th>
                            </tr>
                        </thead>
                        <tbody>";
                        foreach($data as $esc) {
                            $status = $esc->activo ? 'success' : 'danger';
                            // Mostramos Email y Teléfono en un solo renglón
                            $contacto = ($esc->email ?? '---') . " | " . ($esc->telefono ?? '---');

                            $html .= "
                            <tr>
                                <td><span class='badge bg-$status'>".($esc->activo ? 'ACT' : 'INA')."</span></td>
                                <td>
                                    <div class='fw-bold text-primary'>{$esc->nombre}</div>
                                    <small class='text-muted'>CUE: ".($esc->cue ?? 'N/A')."</small>
                                </td>
                                <td class='hide-mobile text-muted small'>$contacto</td>
                                <td class='text-center'>
                                    <button class='btn btn-sm btn-light border' onclick='abrirEditar(".json_encode($esc).")'><i class='fas fa-edit'></i></button>
                                </td>
                            </tr>";
                        }
                $html .= "</tbody></table></div>
                <div class='mt-3'>".$data->links('pagination::bootstrap-5')."</div>
            </div>

            <div class='modal fade' id='modalEscuela' tabindex='-1'>
                <div class='modal-dialog modal-lg modal-dialog-centered'>
                    <form action='/escuelas/guardar' method='POST' class='modal-content'>
                        ".csrf_field()."
                        <input type='hidden' name='id' id='e_id'>
                        <div class='modal-header bg-primary text-white'>
                            <h6 class='modal-title fw-bold'>DATOS DE LA ESCUELA</h6>
                            <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal'></button>
                        </div>
                        <div class='modal-body row g-2'>
                            <div class='col-md-8'><label class='small fw-bold'>Nombre</label><input type='text' name='nombre' id='e_nom' class='form-control form-control-sm' required></div>
                            <div class='col-md-4'><label class='small fw-bold'>CUE</label><input type='text' name='cue' id='e_cue' class='form-control form-control-sm'></div>
                            <div class='col-md-6'><label class='small fw-bold'>Provincia</label><select id='e_prov' class='form-select form-select-sm' onchange='getLocs(this.value)'><option value=''>- Seleccionar -</option>";
                            foreach($provincias as $p) { $html .= "<option value='{$p->id}'>{$p->nombre}</option>"; }
                $html .= "  </select></div>
                            <div class='col-md-6'><label class='small fw-bold'>Localidad</label><select name='localidad_id' id='e_loc' class='form-select form-select-sm' disabled><option value=''>- Elija Provincia -</option></select></div>
                            <div class='col-md-8'><label class='small fw-bold'>Email</label><input type='email' name='email' id='e_mail' class='form-control form-control-sm'></div>
                            <div class='col-md-4'><label class='small fw-bold'>Teléfono</label><input type='text' name='telefono' id='e_tel' class='form-control form-control-sm'></div>
                            <div class='col-md-8'><label class='small fw-bold'>Dirección</label><input type='text' name='direccion' id='e_dir' class='form-control form-control-sm'></div>
                            <div class='col-md-4'><label class='small fw-bold'>Estado</label><select name='activo' id='e_act' class='form-select form-select-sm'><option value='1'>ACTIVA</option><option value='0'>INACTIVA</option></select></div>
                        </div>
                        <div class='modal-footer bg-light'>
                            <button type='submit' class='btn btn-primary btn-sm w-100 fw-bold'>GUARDAR CAMBIOS</button>
                        </div>
                    </form>
                </div>
            </div>

            <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
            <script>
                const modalEsc = new bootstrap.Modal(document.getElementById('modalEscuela'));
                async function getLocs(provId, locId = null) {
                    const selLoc = document.getElementById('e_loc');
                    if(!provId) return;
                    const r = await fetch('/api/localidades/' + provId);
                    const data = await r.json();
                    selLoc.innerHTML = '<option value=\"\">- Localidad -</option>';
                    data.forEach(l => { selLoc.innerHTML += `<option value=\"\${l.id}\" \${locId == l.id ? 'selected' : ''}>\${l.nombre}</option>`; });
                    selLoc.disabled = false;
                }
                function abrirNuevo() { document.getElementById('e_id').value = ''; document.querySelector('#modalEscuela form').reset(); modalEsc.show(); }
                function abrirEditar(d) {
                    document.getElementById('e_id').value = d.id;
                    document.getElementById('e_nom').value = d.nombre || '';
                    document.getElementById('e_cue').value = d.cue || '';
                    document.getElementById('e_mail').value = d.email || '';
                    document.getElementById('e_tel').value = d.telefono || '';
                    document.getElementById('e_dir').value = d.direccion || '';
                    document.getElementById('e_act').value = d.activo;
                    let locId = d.localidad_id || d.id_localidad;
                    if(locId) {
                        fetch('/api/provincia-de-localidad/' + locId).then(r => r.json()).then(res => {
                            document.getElementById('e_prov').value = res.provincia_id;
                            getLocs(res.provincia_id, locId);
                        });
                    }
                    modalEsc.show();
                }
            </script>
        </body></html>";
        return response($html);
    }

    public function store(Request $request) {
        $id = $request->input('id');
        $escuela = $id ? Escuela::findOrFail($id) : new Escuela;

        // Detectamos columnas reales para no fallar
        $cols = Schema::getColumnListing('escuelas');
        $datos = $request->all();

        // Mapeo por si en DB es id_localidad
        if (!in_array('localidad_id', $cols) && in_array('id_localidad', $cols)) {
            $datos['id_localidad'] = $request->input('localidad_id');
        }

        $final = array_intersect_key($datos, array_flip($cols));

        // PRUEBA DE FUEGO: Si no graba, esto detendrá el proceso y te mostrará el error
        try {
            $escuela->fill($final);
            $escuela->save();
        } catch (\Exception $e) {
            dd("ERROR AL GRABAR: " . $e->getMessage(), "DATOS ENVIADOS:", $final);
        }

        return redirect('/escuelas');
    }

    public function getLocalidades($provincia_id) { return response()->json(Localidad::where('provincia_id', $provincia_id)->get()); }
    public function getInfoLocalidad($localidad_id) { return response()->json(Localidad::find($localidad_id)); }
}
