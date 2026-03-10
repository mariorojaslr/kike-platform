<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocenteDocumento;
use App\Models\NotificacionAuditor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckExpiredDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docs:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica la vigencia de los documentos de terapeutas y cambia su estado si vencieron. (Próximamente dispara notificaciones de DB a Roles Auditores)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hoy = Carbon::now()->startOfDay();

        // 1. Encontrar todos los documentos que tengan fecha límite, que NO estén marcados como vencido 
        // y cuya fecha sea menor estricto al día de hoy (Acaban de caducar o caducaron hace tiempo)
        $documentosExpirados = DocenteDocumento::whereNotNull('fecha_vencimiento')
            ->where('estado', '!=', 'vencido')
            ->whereDate('fecha_vencimiento', '<', $hoy)
            ->get();

        $totalVencidos = 0;

        foreach ($documentosExpirados as $doc) {
            // Cambiar su estado a vencido para que los auditores lo vean en rojo
            $doc->estado = 'vencido';
            $doc->save();
            
            // 4. Registrar la alerta silenciosa en la base de datos de auditoría
            NotificacionAuditor::create([
                'empresa_id' => $doc->docente->empresa_id,
                'titulo' => 'Alerta de Auditoría: Documento Vencido',
                'mensaje' => 'El documento "' . $doc->tipo_documento . '" del profesional ' . $doc->docente->nombre . ' (DNI: ' . $doc->docente->dni . ') ha cauducado el ' . Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') . '.',
                'tipo' => 'vencimiento'
            ]);

            $totalVencidos++;

            // [TODO FASE PRÓXIMA]: Identificar el empresa_id del docente ($doc->docente->empresa_id)
            // buscar a todos los usuarios que sean 'admin' o 'auditor' de esa empresa
            // e inyectar un registro Notification::$user->notify(new DocExpiredNotification($doc))
            
            Log::info("Documento Vencido Detectado y Actualizado: ID {$doc->id} (Docente ID {$doc->docente_id})");
        }

        $this->info("CronJob ejecutado con éxito. $totalVencidos documentos fueron trasladados a estado 'vencido' y notificados.");
    }
}
