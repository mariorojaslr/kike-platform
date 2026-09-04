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

    // --- MÓDULO: GESTIÓN CENTRALIZADA DE USUARIOS Y ACCESOS (OWNER) ---
    Route::get('/owner/usuarios', [\App\Http\Controllers\OwnerUserController::class, 'index'])->name('owner.usuarios.index');
    Route::post('/owner/usuarios', [\App\Http\Controllers\OwnerUserController::class, 'store'])->name('owner.usuarios.store');
    Route::post('/owner/usuarios/{user}/reset-password', [\App\Http\Controllers\OwnerUserController::class, 'resetPassword'])->name('owner.usuarios.reset_password');
    Route::delete('/owner/usuarios/{user}', [\App\Http\Controllers\OwnerUserController::class, 'destroy'])->name('owner.usuarios.destroy');

    // --- MÓDULO: EMPRESAS (SaaS) ---
    Route::post('/empresas/store', [\App\Http\Controllers\EmpresaController::class , 'store'])->name('empresas.store');
    Route::post('/empresas/{empresa}/toggle-status', [\App\Http\Controllers\EmpresaController::class , 'toggleStatus'])->name('empresas.toggle_status');
    Route::post('/empresas/{empresa}/reset-password', [\App\Http\Controllers\EmpresaController::class , 'resetPassword'])->name('owner.empresas.reset_password');
    Route::post('/empresas/{empresa}/crear-admin', [\App\Http\Controllers\EmpresaController::class , 'crearAdminPorDefecto'])->name('owner.empresas.crear_admin');
    Route::post('/empresas/{empresa}/billing-config', [\App\Http\Controllers\EmpresaController::class , 'updateBillingConfig'])->name('owner.empresas.update_billing_config');

    // --- MÓDULO: FACTURACIÓN Y CICLOS (SaaS) ---
    Route::get('/owner/billing', [\App\Http\Controllers\SystemBillingController::class , 'index'])->name('owner.billing');
    Route::post('/owner/billing/tarifas', [\App\Http\Controllers\SystemBillingController::class , 'updateTarifas'])->name('owner.billing.update_tarifas');
    
    // CRUD Cuentas de Cobro / Billeteras
    Route::post('/owner/cuentas-cobro', [\App\Http\Controllers\CuentaCobroController::class, 'store'])->name('owner.cuentas.store');
    Route::put('/owner/cuentas-cobro/{id}', [\App\Http\Controllers\CuentaCobroController::class, 'update'])->name('owner.cuentas.update');
    Route::post('/owner/cuentas-cobro/{id}/toggle', [\App\Http\Controllers\CuentaCobroController::class, 'toggleStatus'])->name('owner.cuentas.toggle');
    Route::delete('/owner/cuentas-cobro/{id}', [\App\Http\Controllers\CuentaCobroController::class, 'destroy'])->name('owner.cuentas.destroy');

    // --- MÓDULO: AUDITORÍA DE DOCENTES & NOVEDADES EN TIEMPO REAL ---
    Route::get('/auditor/docentes-legajos', [\App\Http\Controllers\AuditorDocenteController::class, 'indexLegajos'])->name('auditor.docentes.legajos');
    Route::get('/auditor/novedades', [\App\Http\Controllers\AuditorDocenteController::class, 'novedades'])->name('auditor.novedades');
    
    Route::post('/auditor/expedientes/{id}/aprobar', [\App\Http\Controllers\AuditorDocenteController::class, 'aprobarExpediente'])->name('auditor.expedientes.aprobar');
    Route::post('/auditor/expedientes/{id}/rechazar', [\App\Http\Controllers\AuditorDocenteController::class, 'rechazarExpediente'])->name('auditor.expedientes.rechazar');
    
    Route::post('/auditor/facturas-docente/{id}/aprobar', [\App\Http\Controllers\AuditorDocenteController::class, 'aprobarFactura'])->name('auditor.facturas_docente.aprobar');
    Route::post('/auditor/facturas-docente/{id}/rechazar', [\App\Http\Controllers\AuditorDocenteController::class, 'rechazarFactura'])->name('auditor.facturas_docente.rechazar');

    Route::post('/auditor/documento-legajo/{id}/aprobar', [\App\Http\Controllers\AuditorDocenteController::class, 'aprobarDocumentoLegajo'])->name('auditor.legajo_doc.aprobar');
    Route::post('/auditor/documento-legajo/{id}/rechazar', [\App\Http\Controllers\AuditorDocenteController::class, 'rechazarDocumentoLegajo'])->name('auditor.legajo_doc.rechazar');
    Route::post('/auditor/documento-legajo/subir', [\App\Http\Controllers\AuditorDocenteController::class, 'subirDocumentoAuditor'])->name('auditor.legajo_doc.subir');

    // --- MÓDULO: PWA DOCENTE / EXPEDIENTES ---
    Route::post('/pwa/expediente/store', [\App\Http\Controllers\ExpedienteAlumnoController::class, 'store'])->name('pwa.expediente.store');
    Route::post('/pwa/factura-arca/store', [\App\Http\Controllers\ExpedienteAlumnoController::class, 'subirFacturaArca'])->name('pwa.factura_arca.store');

    // --- MÓDULO: PORTAL DIRECTORA DE ESCUELA (CERTIFICACIÓN DIGITAL DE ASISTENCIA) ---
    Route::get('/app-directora/demo', [\App\Http\Controllers\DirectoraAsistenciaController::class, 'indexDemo'])->name('directora.demo');
    Route::get('/directora/asistencia', [\App\Http\Controllers\DirectoraAsistenciaController::class, 'indexDemo']);
    Route::get('/directora/asistencias', [\App\Http\Controllers\DirectoraAsistenciaController::class, 'indexDemo'])->name('directora.asistencias.demo');
    Route::post('/directora/asistencia/firmar', [\App\Http\Controllers\DirectoraAsistenciaController::class, 'firmarAsistencia'])->name('directora.asistencia.firmar');

    // --- MÓDULO: PORTAL PADRE / TITULAR (REINTEGROS Y AVAL) ---
    Route::get('/app-padre/demo', [\App\Http\Controllers\PadreAsistenciaController::class, 'indexDemo'])->name('padre.dashboard.demo');
    Route::get('/padre/demo', [\App\Http\Controllers\PadreAsistenciaController::class, 'indexDemo']);
    Route::post('/padre/asistencia/confirmar', [\App\Http\Controllers\PadreAsistenciaController::class, 'confirmarAsistencia'])->name('padre.asistencia.confirmar');

    // --- MÓDULO: EXPORTACIÓN DE EXPEDIENTE EN PDF CON QR DE TRAZABILIDAD ---
    Route::get('/expediente/{id}/pdf', [\App\Http\Controllers\ExpedientePdfController::class, 'descargarPdf'])->name('expediente.pdf');

    // --- MÓDULO: FASE 3 - PRESTADORES MÉDICOS, CLÍNICAS Y AUDITORÍA MÉDICA MUTUAL ---
    Route::get('/prestadores/demo', [\App\Http\Controllers\PrestadorMedicoController::class, 'indexDemo'])->name('prestadores.demo');
    Route::get('/auditor/prestaciones', [\App\Http\Controllers\PrestadorMedicoController::class, 'auditoriaIndex'])->name('auditor.prestaciones.demo');
    Route::post('/auditor/practica/{id}/autorizar', [\App\Http\Controllers\PrestadorMedicoController::class, 'autorizarPractica'])->name('auditor.practica.autorizar');

    // --- MÓDULO: FASE 4 - CUADRO DE MANDO EJECUTIVO (PANEL DEL DUEÑO DE LA MUTUAL - 130.000 CÁPITAS) ---
    Route::get('/owner/mutual-dashboard', [\App\Http\Controllers\MutualExecutiveController::class, 'indexDashboard'])->name('owner.mutual_dashboard');

    // --- MÓDULO: FASE 5 - APP DEL AFILIADO, CREDENCIAL DIGITAL QR Y CARTILLA MÉDICA ---
    Route::get('/app-afiliado/credencial', [\App\Http\Controllers\AfiliadoPortalController::class, 'credencialDemo'])->name('afiliado.credencial.demo');
    Route::get('/app-afiliado/turnos', [\App\Http\Controllers\AfiliadoPortalController::class, 'cartillaTurnos'])->name('afiliado.turnos.demo');

    // --- MÓDULO: FASE 6 - PORTAL DE FARMACIAS CONVENIDAS Y VALIDADOR DE VADEMÉCUM MUTUAL ---
    Route::get('/farmacia/demo', [\App\Http\Controllers\FarmaciaPortalController::class, 'validadorDemo'])->name('farmacia.demo');
    Route::post('/farmacia/validar-receta', [\App\Http\Controllers\FarmaciaPortalController::class, 'validarReceta'])->name('farmacia.validar');


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
// MÓDULO PWA Docentes / Terapeutas (Demo)
// ==========================================
Route::get('/demo', function() { return view('demo_selector'); })->name('demo.selector');
Route::get('/app-docente/demo', [\App\Http\Controllers\PwaDocenteController::class , 'demo'])->name('pwa.docente.demo');
Route::get('/app-directora/demo', [\App\Http\Controllers\DirectoraAsistenciaController::class, 'indexDemo']);
Route::get('/directora/asistencia', [\App\Http\Controllers\DirectoraAsistenciaController::class, 'indexDemo']);
Route::post('/app-docente/upload', [\App\Http\Controllers\PwaDocenteController::class , 'uploadDocument'])->name('pwa.docente.upload');
Route::get('/app-docente/search', [\App\Http\Controllers\PwaDocenteController::class, 'search'])->name('pwa.docente.search');

// ==========================================
// MÓDULO ASISTENTE IA (GEMINI 1.5 FLASH)
// ==========================================
Route::post('/asistente/consultar', [\App\Http\Controllers\AiAssistantController::class, 'query'])->name('ai.assistant.query');

// ==========================================
// MÓDULO PAGOS Y TRANSFERENCIAS (CON IA)
// ==========================================
Route::post('/pagos/reportar', [\App\Http\Controllers\PagoEmpresaController::class, 'reportarPago'])->name('pagos.reportar');
Route::post('/pagos/{pago}/aprobar', [\App\Http\Controllers\PagoEmpresaController::class, 'aprobar'])->name('pagos.aprobar');
Route::post('/pagos/{pago}/rechazar', [\App\Http\Controllers\PagoEmpresaController::class, 'rechazar'])->name('pagos.rechazar');

// ==========================================
// MÓDULO NOTIFICACIONES INTERNAS
// ==========================================
Route::get('/api/notificaciones', [\App\Http\Controllers\NotificacionController::class, 'getNotificaciones'])->name('notificaciones.get');
Route::post('/api/notificaciones/{id}/leer', [\App\Http\Controllers\NotificacionController::class, 'marcarLeida'])->name('notificaciones.marcar_leida');
Route::post('/api/notificaciones/leer-todas', [\App\Http\Controllers\NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.leer_todas');
// ==========================================
// MÓDULOS DE TELEMEDICINA, LIQUIDACIONES, DERIVACIONES & WHATSAPP
// ==========================================
Route::get('/app-afiliado/telemedicina', [\App\Http\Controllers\TelemedicinaController::class, 'indexDemo'])->name('afiliado.telemedicina.demo');
Route::post('/telemedicina/receta/emitir', [\App\Http\Controllers\TelemedicinaController::class, 'emitirRecetaDigital'])->name('telemedicina.receta.emitir');

Route::get('/owner/liquidaciones', [\App\Http\Controllers\LiquidacionPrestadoresController::class, 'indexDemo'])->name('owner.liquidaciones');
Route::post('/liquidacion/procesar-cierre', [\App\Http\Controllers\LiquidacionPrestadoresController::class, 'procesarCierreLiquidacion'])->name('liquidacion.procesar_cierre');

Route::get('/app-afiliado/derivaciones', [\App\Http\Controllers\DerivacionViaticosController::class, 'indexDemo'])->name('afiliado.derivaciones.demo');
Route::post('/derivaciones/emitir-voucher', [\App\Http\Controllers\DerivacionViaticosController::class, 'emitirVoucherTransito'])->name('derivaciones.emitir_voucher');

Route::get('/simulador/whatsapp', [\App\Http\Controllers\WhatsAppBotSimulatorController::class, 'indexDemo'])->name('whatsapp.simulador.demo');
Route::post('/whatsapp/simulador/procesar', [\App\Http\Controllers\WhatsAppBotSimulatorController::class, 'procesarMensajeWhatsapp'])->name('whatsapp.simulador.procesar');

// ==========================================
// AUDITORÍA CENTRAL, PROYECCIÓN BI, SYNC OFFLINE & CERTIFICADOS ARCA
// ==========================================
Route::get('/auditoria/central', [\App\Http\Controllers\AuditoriaCentralController::class, 'indexDemo'])->name('auditoria.central.demo');
Route::post('/auditoria/central/procesar', [\App\Http\Controllers\AuditoriaCentralController::class, 'procesarAuditoria'])->name('auditoria.central.procesar');

Route::get('/owner/proyeccion-presupuesto', [\App\Http\Controllers\ProyeccionPresupuestariaController::class, 'indexDemo'])->name('owner.proyeccion_presupuesto');

Route::post('/api/pwa/offline-sync', [\App\Http\Controllers\PwaOfflineSyncController::class, 'sincronizarLote'])->name('api.pwa.offline_sync');

Route::get('/app-afiliado/certificado-anual', [\App\Http\Controllers\CertificadosAnualesController::class, 'indexDemo'])->name('afiliado.certificado_anual.demo');

/**
 * 3. SISTEMA DE AUTENTICACIÓN (Breeze/Jetstream)
 */
require __DIR__ . '/auth.php';
