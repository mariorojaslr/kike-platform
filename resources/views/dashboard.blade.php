<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\Escuela;
use App\Models\Formacion;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. OBTENEMOS ESTADÍSTICAS REALES PARA LAS TARJETAS
        $totalMaestras = Docente::count();
        $maestrasValidadas = Docente::where('validado_auditoria', 1)->count();
        $totalEscuelas = Escuela::count();
        $totalFormaciones = Formacion::count();

        // Calcular porcentaje de avance de auditoría
        $porcentajeAudit = $totalMaestras > 0 ? round(($maestrasValidadas / $totalMaestras) * 100) : 0;

        $html = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>Panel de Control | Sistema Auditoría</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
                :root { --primario: #1e3a8a; --secundario: #3b82f6; --fondo: #f1f5f9; }
                body { background-color: var(--fondo); font-family: 'Poppins', sans-serif; color: #334155; }

                .header-dash { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 40px 30px; border-radius: 0 0 30px 30px; margin-bottom: -50px; }

                .card-stat { background: white; border: none; border-radius: 20px; transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
                .card-stat:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }

                .icon-box { width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 15px; }
                .bg-light-blue { background: #e0f2fe; color: #0369a1; }
                .bg-light-green { background: #dcfce7; color: #166534; }
                .bg-light-purple { background: #f3e8ff; color: #6b21a8; }
                .bg-light-orange { background: #ffedd5; color: #9a3412; }

                .nav-card { background: white; border-radius: 15px; padding: 20px; text-decoration: none; color: inherit; display: flex; align-items: center; gap: 15px; border: 1px solid transparent; transition: 0.3s; }
                .nav-card:hover { border-color: var(--secundario); background: #f8fafc; }

                .progress { height: 10px; border-radius: 10px; background-color: #e2e8f0; }
                .progress-bar { background: linear-gradient(90deg, #22c55e, #10b981); }
            </style>
        </head>
        <body>

            <div class='header-dash shadow-lg'>
                <div class='container'>
                    <div class='d-flex justify-content-between align-items-center'>
                        <div>
                            <h1 class='fw-bold mb-1'>¡Hola, Mario Rojas!</h1>
                            <p class='opacity-75 mb-0'>Bienvenido al Panel de Gestión de Auditoría</p>
                        </div>
                        <div class='text-end'>
                            <div class='badge bg-white text-primary px-3 py-2 rounded-pill fw-bold'>
                                <i class='far fa-calendar-alt me-2'></i> " . date('d/m/Y') . "
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class='container' style='margin-top: 80px;'>
                <div class='row g-4 mb-5'>
                    <div class='col-md-3'>
                        <div class='card-stat p-4'>
                            <div class='icon-box bg-light-blue'><i class='fas fa-chalkboard-teacher'></i></div>
                            <h6 class='text-secondary fw-bold'>Total Maestras</h6>
                            <h2 class='fw-bold'>$totalMaestras</h2>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class='card-stat p-4'>
                            <div class='icon-box bg-light-green'><i class='fas fa-check-double'></i></div>
                            <h6 class='text-secondary fw-bold'>Validadas</h6>
                            <h2 class='fw-bold'>$maestrasValidadas</h2>
                            <div class='progress mt-2'>
                                <div class='progress-bar' style='width: $porcentajeAudit%'></div>
                            </div>
                            <small class='text-muted'>$porcentajeAudit% del total</small>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class='card-stat p-4'>
                            <div class='icon-box bg-light-purple'><i class='fas fa-school'></i></div>
                            <h6 class='text-secondary fw-bold'>Escuelas</h6>
                            <h2 class='fw-bold'>$totalEscuelas</h2>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class='card-stat p-4'>
                            <div class='icon-box bg-light-orange'><i class='fas fa-graduation-cap'></i></div>
                            <h6 class='text-secondary fw-bold'>Especialidades</h6>
                            <h2 class='fw-bold'>$totalFormaciones</h2>
                        </div>
                    </div>
                </div>

                <h5 class='fw-bold mb-4'><i class='fas fa-rocket me-2 text-primary'></i>Accesos Directos</h5>
                <div class='row g-3'>
                    <div class='col-md-4'>
                        <a href='/docentes' class='nav-card shadow-sm'>
                            <div class='icon-box bg-primary text-white mb-0'><i class='fas fa-users'></i></div>
                            <div>
                                <div class='fw-bold'>Gestionar Maestras</div>
                                <small class='text-muted'>Altas, bajas y auditoría</small>
                            </div>
                        </a>
                    </div>
                    <div class='col-md-4'>
                        <a href='/escuelas' class='nav-card shadow-sm'>
                            <div class='icon-box bg-dark text-white mb-0'><i class='fas fa-university'></i></div>
                            <div>
                                <div class='fw-bold'>Listado de Escuelas</div>
                                <small class='text-muted'>Sedes y ubicaciones</small>
                            </div>
                        </a>
                    </div>
                    <div class='col-md-4'>
                        <a href='/configuracion' class='nav-card shadow-sm'>
                            <div class='icon-box bg-secondary text-white mb-0'><i class='fas fa-cogs'></i></div>
                            <div>
                                <div class='fw-bold'>Configuración</div>
                                <small class='text-muted'>Localidades y formación</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
        </body>
        </html>";

        return response($html);
    }
}
