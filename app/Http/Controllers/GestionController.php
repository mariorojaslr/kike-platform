<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GestionController extends Controller
{
    public function index()
    {
        // Llamamos a los modelos por su ruta completa para forzar a PHP a encontrarlos
        try {
            $cantMaestras = \App\Models\Docente::count();
        } catch (\Exception $e) {
            $cantMaestras = 0;
        }

        try {
            $cantEscuelas = \App\Models\Escuela::count();
        } catch (\Exception $e) {
            $cantEscuelas = 0;
        }

        $cantTitulares = 1;
        $cantFamilia = 0;

        $html = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Dashboard | Gestión</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
                body { background-color: #f1f5f9; font-family: 'Poppins', sans-serif; }
                .navbar-custom { background-color: #3b62d1; color: white; padding: 15px 30px; }
                .nav-menu { background: white; padding: 10px 30px; border-bottom: 2px solid #e2e8f0; display: flex; gap: 20px; overflow-x: auto; }
                .nav-menu a { color: #64748b; text-decoration: none; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; padding: 10px 5px; white-space: nowrap; }
                .nav-menu a.active { color: #3b62d1; border-bottom: 3px solid #3b62d1; }
                .card-dash { border: none; border-radius: 20px; color: white; padding: 25px; transition: 0.3s; position: relative; overflow: hidden; height: 160px; text-decoration: none; display: block; }
                .card-dash:hover { transform: translateY(-5px); filter: brightness(1.1); color: white; }
                .card-dash i { position: absolute; right: 20px; bottom: 20px; font-size: 4rem; opacity: 0.2; }
                .card-num { font-size: 2.5rem; font-weight: 700; line-height: 1; }
                .card-title { font-size: 0.9rem; font-weight: 600; text-transform: uppercase; margin-top: 10px; }
                .bg-titulares { background: linear-gradient(135deg, #00c6ff, #0072ff); }
                .bg-familiar { background: linear-gradient(135deg, #2af598, #009efd); }
                .bg-escuelas { background: linear-gradient(135deg, #f9d423, #ff4e50); }
                .bg-maestras { background: linear-gradient(135deg, #3b41c5, #a4508b); }
                .main-content { background: white; border-radius: 25px; margin-top: 30px; padding: 50px; text-align: center; border: 1px solid #e2e8f0; }
            </style>
        </head>
        <body>
            <div class='navbar-custom d-flex justify-content-between align-items-center shadow-sm'>
                <div class='d-flex align-items-center gap-2'>
                    <i class='fas fa-briefcase-medical fs-4'></i>
                    <span class='fw-bold fs-5 text-uppercase'>Gestión de Prestadores</span>
                </div>
                <div class='small fw-bold'>BIENVENIDO, MARIO ROJAS</div>
            </div>

            <div class='nav-menu shadow-sm'>
                <a href='/dashboard' class='active'><i class='fas fa-th-large me-2'></i>Dashboard</a>
                <a href='#'><i class='fas fa-address-card me-2'></i>Titulares</a>
                <a href='#'><i class='fas fa-users me-2'></i>Grupo Familiar</a>
                <a href='/docentes'><i class='fas fa-chalkboard-teacher me-2'></i>Maestras</a>
                <a href='/escuelas'><i class='fas fa-school me-2'></i>Escuelas</a>
            </div>

            <div class='container mt-4'>
                <div class='row g-4'>
                    <div class='col-md-3'>
                        <a href='#' class='card-dash bg-titulares shadow'>
                            <div class='card-num'>$cantTitulares</div>
                            <div class='card-title'>Titulares</div>
                            <i class='fas fa-users'></i>
                        </a>
                    </div>
                    <div class='col-md-3'>
                        <a href='#' class='card-dash bg-familiar shadow'>
                            <div class='card-num'>$cantFamilia</div>
                            <div class='card-title'>Grupo Familiar</div>
                            <i class='fas fa-child'></i>
                        </a>
                    </div>
                    <div class='col-md-3'>
                        <a href='/escuelas' class='card-dash bg-escuelas shadow'>
                            <div class='card-num'>$cantEscuelas</div>
                            <div class='card-title'>Escuelas</div>
                            <i class='fas fa-school'></i>
                        </a>
                    </div>
                    <div class='col-md-3'>
                        <a href='/docentes' class='card-dash bg-maestras shadow'>
                            <div class='card-num'>$cantMaestras</div>
                            <div class='card-title'>Maestras</div>
                            <i class='fas fa-chalkboard-teacher'></i>
                        </a>
                    </div>
                </div>

                <div class='main-content shadow-sm'>
                    <h3 class='fw-bold text-dark'>Panel Principal de Gestión</h3>
                    <p class='text-muted'>Mario, selecciona un módulo para trabajar.</p>
                </div>
            </div>
        </body>
        </html>";

        return response($html);
    }
}
