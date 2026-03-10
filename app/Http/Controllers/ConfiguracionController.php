<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provincia;
use App\Models\Localidad;
use App\Models\Titulo;
use App\Models\Diagnostico;

class ConfiguracionController extends Controller
{
    /**
     * Estética unificada para todas las tablas maestras
     */
    private function vistaBase($titulo, $tablaHtml)
    {
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <title>Configuración | $titulo</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
                body { background-color: #f1f5f9; font-family: 'Poppins', sans-serif; font-size: 0.85rem; }
                .header-main { background: #0f172a; color: white; padding: 15px 30px; }
                .nav-top { background: white; padding: 10px 30px; border-bottom: 1px solid #e2e8f0; display: flex; gap: 20px; align-items: center; }
                .nav-link { color: #64748b; text-decoration: none; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; }
                .active-menu { color: #0f172a !important; border-bottom: 2px solid #0f172a; }
                .main-box { background: white; border-radius: 15px; margin: 20px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
            </style>
        </head>
        <body>
            <div class='header-main d-flex justify-content-between align-items-center'>
                <div class='fw-bold fs-5'><i class='fas fa-cogs me-2'></i> DATOS COMPLEMENTARIOS</div>
                <div class='small'>ADMINISTRACIÓN CENTRAL</div>
            </div>

            <div class='nav-top shadow-sm'>
                <a href='/dashboard' class='nav-link'>Dashboard</a>
                <a href='/docentes' class='nav-link'>Docentes</a>
                <a href='/escuelas' class='nav-link'>Escuelas</a>
                <div class='dropdown'>
                    <a href='#' class='nav-link dropdown-toggle active-menu' data-bs-toggle='dropdown'>Configuración</a>
                    <ul class='dropdown-menu shadow border-0'>
                        <li><a class='dropdown-item' href='/config/localidades'>Localidades</a></li>
                        <li><a class='dropdown-item' href='/config/titulos'>Títulos</a></li>
                        <li><a class='dropdown-item' href='/config/diagnosticos'>Diagnósticos</a></li>
                    </ul>
                </div>
            </div>

            <div class='main-box'>
                <h4 class='fw-bold mb-4 text-dark'>$titulo</h4>
                <div class='table-responsive'>
                    $tablaHtml
                </div>
            </div>

            <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
        </body>
        </html>";
    }

    public function localidades()
    {
        $rows = Localidad::with('provincia')->orderBy('nombre', 'asc')->get();
        $html = "<table class='table table-hover'>
            <thead class='table-light'><tr><th>ID</th><th>Provincia</th><th>Departamento</th></tr></thead>
            <tbody>";
        foreach($rows as $r) {
            $html .= "<tr><td>{$r->id}</td><td>{$r->provincia->nombre}</td><td class='fw-bold'>{$r->nombre}</td></tr>";
        }
        $html .= "</tbody></table>";
        return response($this->vistaBase("Departamentos de La Rioja", $html));
    }

    public function titulos()
    {
        $rows = Titulo::all();
        $html = "<table class='table table-hover'>
            <thead class='table-light'><tr><th>ID</th><th>Nombre del Título</th><th>Nivel</th></tr></thead>
            <tbody>";
        foreach($rows as $r) {
            $html .= "<tr><td>{$r->id}</td><td class='fw-bold'>{$r->nombre}</td><td>{$r->nivel}</td></tr>";
        }
        $html .= "</tbody></table>";
        return response($this->vistaBase("Catálogo de Títulos", $html));
    }

    public function diagnosticos()
    {
        $rows = Diagnostico::all();
        $html = "<table class='table table-hover'>
            <thead class='table-light'><tr><th>ID</th><th>Descripción</th></tr></thead>
            <tbody>";
        foreach($rows as $r) {
            $html .= "<tr><td>{$r->id}</td><td class='fw-bold'>{$r->descripcion}</td></tr>";
        }
        $html .= "</tbody></table>";
        return response($this->vistaBase("Catálogo de Diagnósticos", $html));
    }
}
