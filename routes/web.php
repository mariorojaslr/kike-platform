<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GestionController;
use App\Http\Controllers\MaestrasController;
use App\Http\Controllers\EscuelaController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\LimpiezaController;

/* |-------------------------------------------------------------------------- | Web Routes - Plataforma de Gestión de Servicios PRO (Responsive) |-------------------------------------------------------------------------- | Desarrollado para: Mario Rojas - Gestión de Prestadores |-------------------------------------------------------------------------- */

/**
 * 1. ACCESO PÚBLICO
 */
Route::get('/', function () {
    return redirect()->route('login');
});

/**
 * 2. RUTAS PROTEGIDAS (Middleware: Auth)
 */
Route::middleware(['auth', 'verified'])->group(function () {

    // --- ESCRITORIO PRINCIPAL (DASHBOARD) ---
    Route::get('/dashboard', [\App\Http\Controllers\OwnerDashboardController::class , 'index'])->name('dashboard');

    // --- MÓDULO: GEOGRAFÍA (ABM PROVINCIAS Y LOCALIDADES) ---
    Route::get('/owner/geografia', [\App\Http\Controllers\AbmGeograficoController::class , 'index'])->name('owner.geografia');
    Route::post('/owner/geografia/localidad', [\App\Http\Controllers\AbmGeograficoController::class , 'storeLocalidad'])->name('geografia.localidad.store');
    Route::delete('/owner/geografia/localidad/{id}', [\App\Http\Controllers\AbmGeograficoController::class , 'destroyLocalidad'])->name('geografia.localidad.destroy');

    // --- MÓDULO: EMPRESAS (SaaS) ---
    Route::post('/empresas/store', [\App\Http\Controllers\EmpresaController::class , 'store'])->name('empresas.store');
    Route::post('/empresas/{empresa}/toggle-status', [\App\Http\Controllers\EmpresaController::class , 'toggleStatus'])->name('empresas.toggle_status');
    Route::post('/empresas/{empresa}/reset-password', [\App\Http\Controllers\EmpresaController::class , 'resetPassword'])->name('owner.empresas.reset_password');
    Route::post('/empresas/{empresa}/crear-admin', [\App\Http\Controllers\EmpresaController::class , 'crearAdminPorDefecto'])->name('owner.empresas.crear_admin');

    // --- MÓDULO: FACTURACIÓN Y CICLOS (SaaS) ---
    Route::get('/owner/billing', [\App\Http\Controllers\SystemBillingController::class , 'index'])->name('owner.billing');
    Route::post('/owner/billing/tarifas', [\App\Http\Controllers\SystemBillingController::class , 'updateTarifas'])->name('owner.billing.update_tarifas');


    // --- MÓDULO: TENANT (CLIENTE/EMPRESA) ---
    Route::get('/tenant', [\App\Http\Controllers\TenantDashboardController::class , 'index'])->name('tenant.dashboard');
    Route::post('/tenant/setup', [\App\Http\Controllers\TenantDashboardController::class , 'updateSetup'])->name('tenant.setup.update');

    // CRUD Titulares (Referentes) del Tenant
    Route::group(['prefix' => 'tenant/titulares', 'as' => 'tenant.titulares.'], function () {
            Route::get('/', [\App\Http\Controllers\Tenant\TitularController::class , 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Tenant\TitularController::class , 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Tenant\TitularController::class , 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Tenant\TitularController::class , 'destroy'])->name('destroy');
            Route::get('/export/excel', [\App\Http\Controllers\Tenant\TitularController::class , 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [\App\Http\Controllers\Tenant\TitularController::class , 'exportPdf'])->name('export.pdf');
            Route::get('/import/template', [\App\Http\Controllers\Tenant\TitularController::class , 'importTemplate'])->name('import.template');
            Route::post('/import/excel', [\App\Http\Controllers\Tenant\TitularController::class , 'importExcel'])->name('import.excel');
        }
        );

        // CRUD Familiares (Alumnos/Pacientes) del Tenant
        Route::group(['prefix' => 'tenant/familiares', 'as' => 'tenant.familiares.'], function () {
            Route::get('/', [\App\Http\Controllers\Tenant\FamiliarController::class , 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Tenant\FamiliarController::class , 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Tenant\FamiliarController::class , 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Tenant\FamiliarController::class , 'destroy'])->name('destroy');
            Route::get('/export/excel', [\App\Http\Controllers\Tenant\FamiliarController::class , 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [\App\Http\Controllers\Tenant\FamiliarController::class , 'exportPdf'])->name('export.pdf');
            Route::get('/import/template', [\App\Http\Controllers\Tenant\FamiliarController::class , 'importTemplate'])->name('import.template');
            Route::post('/import/excel', [\App\Http\Controllers\Tenant\FamiliarController::class , 'importExcel'])->name('import.excel');
        }
        );

        // CRUD Docentes / Terapeutas del Tenant
        Route::group(['prefix' => 'tenant/docentes', 'as' => 'tenant.docentes.'], function () {
            Route::get('/', [\App\Http\Controllers\Tenant\DocenteController::class , 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Tenant\DocenteController::class , 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Tenant\DocenteController::class , 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Tenant\DocenteController::class , 'destroy'])->name('destroy');
            Route::get('/export/excel', [\App\Http\Controllers\Tenant\DocenteController::class , 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [\App\Http\Controllers\Tenant\DocenteController::class , 'exportPdf'])->name('export.pdf');
            Route::get('/import/template', [\App\Http\Controllers\Tenant\DocenteController::class , 'importTemplate'])->name('import.template');
            Route::post('/import/excel', [\App\Http\Controllers\Tenant\DocenteController::class , 'importExcel'])->name('import.excel');

            // Gestión de Certificados y PDFs Atados al Docente
            Route::post('/{docente_id}/docs', [\App\Http\Controllers\Tenant\DocumentacionController::class , 'store'])->name('docs.store');
            Route::get('/docs/{id}/download', [\App\Http\Controllers\Tenant\DocumentacionController::class , 'download'])->name('docs.download');
            Route::delete('/docs/{id}', [\App\Http\Controllers\Tenant\DocumentacionController::class , 'destroy'])->name('docs.destroy');
        }
        );

        // CRUD Escuelas Vinculadas (Instituciones) del Tenant
        Route::group(['prefix' => 'tenant/escuelas', 'as' => 'tenant.escuelas.'], function () {
            Route::get('/', [\App\Http\Controllers\Tenant\EscuelaController::class , 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Tenant\EscuelaController::class , 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Tenant\EscuelaController::class , 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Tenant\EscuelaController::class , 'destroy'])->name('destroy');
            Route::get('/export/excel', [\App\Http\Controllers\Tenant\EscuelaController::class , 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [\App\Http\Controllers\Tenant\EscuelaController::class , 'exportPdf'])->name('export.pdf');
            Route::get('/import/template', [\App\Http\Controllers\Tenant\EscuelaController::class , 'importTemplate'])->name('import.template');
            Route::post('/import/excel', [\App\Http\Controllers\Tenant\EscuelaController::class , 'importExcel'])->name('import.excel');
        }
        );

        // Importador Masivo Integral
        Route::group(['prefix' => 'tenant/importador', 'as' => 'tenant.importador.'], function () {
            Route::get('/', [\App\Http\Controllers\Tenant\ImportadorController::class, 'index'])->name('index');
            Route::post('/procesar', [\App\Http\Controllers\Tenant\ImportadorController::class, 'procesar'])->name('procesar');
            Route::get('/template/resumen', [\App\Http\Controllers\Tenant\ImportadorController::class, 'templateResumen'])->name('template.resumen');
            Route::get('/template/alumnos', [\App\Http\Controllers\Tenant\ImportadorController::class, 'templateAlumnos'])->name('template.alumnos');
        });


        // Catálogos Maestros de solo Consulta (Read-Only en Tenant)
        Route::group(['prefix' => 'tenant/diagnosticos', 'as' => 'tenant.diagnosticos.'], function () {
            Route::get('/', [\App\Http\Controllers\Tenant\DiagnosticoController::class , 'index'])->name('index');
        }
        );

        Route::group(['prefix' => 'tenant/formaciones', 'as' => 'tenant.formaciones.'], function () {
            Route::get('/', [\App\Http\Controllers\Tenant\FormacionController::class , 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Tenant\FormacionController::class , 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Tenant\FormacionController::class , 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Tenant\FormacionController::class , 'destroy'])->name('destroy');
        }
        );

        Route::group(['prefix' => 'tenant/config/tipo-documentos', 'as' => 'tenant.tipo_documentos.'], function () {
            Route::get('/', [\App\Http\Controllers\Tenant\TipoDocumentoController::class , 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Tenant\TipoDocumentoController::class , 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Tenant\TipoDocumentoController::class , 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Tenant\TipoDocumentoController::class , 'destroy'])->name('destroy');
        }
        );

        // --- MÓDULO: GOD MODE (IMPERSONATION) ---
        Route::get('/impersonate/{user}', [\App\Http\Controllers\ImpersonateController::class , 'enter'])->name('impersonate.enter');
        Route::get('/impersonate-leave', [\App\Http\Controllers\ImpersonateController::class , 'leave'])->name('impersonate.leave');

        // --- MÓDULO: APP MÓVIL TERAPEUTAS ---
        Route::get('/mobile/terapias', [\App\Http\Controllers\Mobile\TerapeutaController::class , 'index'])->name('mobile.terapeuta.dashboard');
        Route::post('/mobile/facturas', [\App\Http\Controllers\Mobile\TerapeutaController::class , 'storeFactura'])->name('mobile.facturas.store');

        // --- MÓDULO: AUDITORÍA DE FACTURAS Y DOCUMENTOS ---
        Route::get('/auditoria/facturas', [\App\Http\Controllers\AuditorController::class , 'index'])->name('auditor.facturas');
        Route::post('/auditoria/facturas/{factura}/status', [\App\Http\Controllers\AuditorController::class , 'updateStatus'])->name('auditor.facturas.status');
        Route::get('/auditoria/documentos', [\App\Http\Controllers\AuditorController::class , 'documentos'])->name('auditor.documentos');
        Route::post('/auditoria/documentos/{id}/status', [\App\Http\Controllers\AuditorController::class , 'updateDocumentoStatus'])->name('auditor.documentos.status');

        // --- GESTIÓN DE PERFIL ---
        Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');

        // --- MÓDULO: TITULARES ---
        Route::get('/titulares', [GestionController::class , 'index'])->name('titulares.index');
        Route::post('/titulares/guardar', [GestionController::class , 'store'])->name('titulares.store');

        // --- MÓDULO: BENEFICIARIOS ---
        Route::get('/beneficiarios', [GestionController::class , 'index'])->name('beneficiarios.index');

        // --- MÓDULO: ESCUELAS (Instituciones) ---
        Route::get('/escuelas', [EscuelaController::class , 'index'])->name('escuelas.index');
        Route::post('/escuelas/guardar', [EscuelaController::class , 'store'])->name('escuelas.store');

        // API para Combos Dinámicos (Provincias y Localidades)
        Route::get('/api/localidades/{provincia_id}', [EscuelaController::class , 'getLocalidades']);
        Route::get('/api/provincia-de-localidad/{localidad_id}', [EscuelaController::class , 'getInfoLocalidad']);

        // --- MÓDULO: MAESTRAS (Docentes) ---
        Route::get('/docentes', [MaestrasController::class , 'index'])->name('docentes.index');
        Route::post('/docentes/guardar', [MaestrasController::class , 'store'])->name('docentes.store');

        // --- MÓDULO: CONFIGURACIÓN ---
        Route::prefix('config')->group(function () {
            Route::get('/localidades', [ConfiguracionController::class , 'localidades'])->name('config.localidades');
            Route::get('/titulos', [ConfiguracionController::class , 'titulos'])->name('config.titulos');
            Route::get('/diagnosticos', [ConfiguracionController::class , 'diagnosticos'])->name('config.diagnosticos');
        }
        );

        // --- UTILIDADES ---
        Route::get('/limpiar-base-datos', [LimpiezaController::class , 'ejecutar']);
        Route::post('/gestion/guardar', [GestionController::class , 'store'])->name('gestion.guardar');
    });

// ==========================================
// MÓDULO PWA DOcentes / Terapeutas (Demo)
// ==========================================
Route::get('/app-docente/demo', [\App\Http\Controllers\PwaDocenteController::class , 'demo'])->name('pwa.docente.demo');
Route::post('/app-docente/upload', [\App\Http\Controllers\PwaDocenteController::class , 'uploadDocument'])->name('pwa.docente.upload');
Route::get('/app-docente/search', [\App\Http\Controllers\PwaDocenteController::class, 'search'])->name('pwa.docente.search');

/**
 * 3. SISTEMA DE AUTENTICACIÓN (Breeze/Jetstream)
 */
require __DIR__ . '/auth.php';
