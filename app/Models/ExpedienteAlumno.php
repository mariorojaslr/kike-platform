<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteAlumno extends Model
{
    protected $table = 'expedientes_alumno';
    protected $guarded = ['id'];

    protected $casts = [
        'resolucion_datos_ia' => 'array',
    ];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function titular()
    {
        return $this->belongsTo(Titular::class, 'titular_id');
    }

    public function alumno()
    {
        return $this->belongsTo(Familiar::class, 'alumno_id');
    }

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'escuela_id');
    }

    public function facturas()
    {
        return $this->hasMany(FacturaDocente::class, 'expediente_id');
    }

    /**
     * Calcula el porcentaje de avance del expediente (0% a 100%)
     */
    public function getPorcentajeProgresoAttribute()
    {
        if ($this->estado_auditoria === 'aprobado') {
            return 100;
        }

        $puntos = 0;
        
        // Paso 1: Resolución (Cargada o autogenerada por Auditoría)
        if ($this->resolucion_url || $this->nro_resolucion) {
            $puntos += 25;
        }
        
        // Paso 2: Identificación Alumno / Titular / Escuela
        if ($this->alumno_id && ($this->escuela_id || $this->titular_id)) {
            $puntos += 25;
        }

        // Paso 3: Declaración de Horas y Asistencia
        if ($this->horas_mensuales_asignadas > 0 || $this->horarios_atencion) {
            $puntos += 25;
        }

        // Paso 4: Factura ARCA subida
        if ($this->facturas()->count() > 0) {
            $puntos += 25;
        }

        return max(25, min(100, $puntos));
    }

    /**
     * Retorna la etiqueta del paso actual
     */
    public function getPasoActualLabelAttribute()
    {
        $pct = $this->porcentaje_progreso;
        if ($pct >= 100) return '100% - Expediente Completo (Listo para Cobro)';
        if ($pct >= 75) return '75% - Horas e Informe Declarado';
        if ($pct >= 50) return '50% - Alumno e Institución Identificados';
        return '25% - Resolución / Ingreso Inicial';
    }
}
