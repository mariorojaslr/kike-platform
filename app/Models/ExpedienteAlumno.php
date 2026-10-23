<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class ExpedienteAlumno extends Model
{
    protected $table = 'expedientes_alumno';
    protected $guarded = ['id'];

    protected $casts = [
        'resolucion_datos_ia' => 'array',
        'ok_mesa_entrada' => 'boolean',
        'ok_docente_padre' => 'boolean',
        'visto_bueno_auditor' => 'boolean',
        'visto_bueno_jefe' => 'boolean',
        'ok_area_1' => 'boolean',
        'ok_area_2' => 'boolean',
        'ok_contabilidad' => 'boolean',
        'aprobado_auditor_at' => 'datetime',
        'aprobado_jefe_at' => 'datetime',
        'fecha_area_1' => 'datetime',
        'fecha_area_2' => 'datetime',
        'fecha_contabilidad' => 'datetime',
    ];

    public static function ensureWorkflowSchema(): void
    {
        if (!Schema::hasTable('expedientes_alumno')) {
            return;
        }

        // Permitir que en Mesa de Entrada el Pre-Expediente nazca SIN DUEÑO (docente_id = NULL) hasta que el padre le pase el N° al docente y este se lo asigne
        try {
            DB::statement('ALTER TABLE expedientes_alumno MODIFY docente_id BIGINT UNSIGNED NULL');
        } catch (\Throwable $e) {
            // Ignorar si ya es nullable
        }

        if (!Schema::hasColumn('expedientes_alumno', 'nro_pre_expediente') || !Schema::hasColumn('expedientes_alumno', 'ok_contabilidad')) {
            Schema::table('expedientes_alumno', function (Blueprint $table) {
                if (!Schema::hasColumn('expedientes_alumno', 'nro_pre_expediente')) {
                    $table->string('nro_pre_expediente', 80)->nullable()->after('origen_resolucion');
                }
                if (!Schema::hasColumn('expedientes_alumno', 'etapa_flujo')) {
                    $table->string('etapa_flujo', 50)->default('mesa_entrada')->after('estado_auditoria');
                }
                if (!Schema::hasColumn('expedientes_alumno', 'ok_mesa_entrada')) {
                    $table->boolean('ok_mesa_entrada')->default(true)->after('etapa_flujo');
                }
                if (!Schema::hasColumn('expedientes_alumno', 'ok_docente_padre')) {
                    $table->boolean('ok_docente_padre')->default(false)->after('ok_mesa_entrada');
                }
                if (!Schema::hasColumn('expedientes_alumno', 'visto_bueno_auditor')) {
                    $table->boolean('visto_bueno_auditor')->default(false)->after('ok_docente_padre');
                }
                if (!Schema::hasColumn('expedientes_alumno', 'visto_bueno_jefe')) {
                    $table->boolean('visto_bueno_jefe')->default(false)->after('visto_bueno_auditor');
                }
                if (!Schema::hasColumn('expedientes_alumno', 'ok_area_1')) {
                    $table->boolean('ok_area_1')->default(false)->after('visto_bueno_jefe');
                }
                if (!Schema::hasColumn('expedientes_alumno', 'ok_area_2')) {
                    $table->boolean('ok_area_2')->default(false)->after('ok_area_1');
                }
                if (!Schema::hasColumn('expedientes_alumno', 'ok_contabilidad')) {
                    $table->boolean('ok_contabilidad')->default(false)->after('ok_area_2');
                }
                if (!Schema::hasColumn('expedientes_alumno', 'responsable_mesa_entrada')) {
                    $table->string('responsable_mesa_entrada', 120)->nullable();
                }
                if (!Schema::hasColumn('expedientes_alumno', 'responsable_auditor')) {
                    $table->string('responsable_auditor', 120)->nullable();
                }
                if (!Schema::hasColumn('expedientes_alumno', 'responsable_area_1')) {
                    $table->string('responsable_area_1', 120)->nullable();
                }
                if (!Schema::hasColumn('expedientes_alumno', 'responsable_area_2')) {
                    $table->string('responsable_area_2', 120)->nullable();
                }
                if (!Schema::hasColumn('expedientes_alumno', 'responsable_contabilidad')) {
                    $table->string('responsable_contabilidad', 120)->nullable();
                }
                if (!Schema::hasColumn('expedientes_alumno', 'aprobado_auditor_at')) {
                    $table->timestamp('aprobado_auditor_at')->nullable();
                }
                if (!Schema::hasColumn('expedientes_alumno', 'aprobado_jefe_at')) {
                    $table->timestamp('aprobado_jefe_at')->nullable();
                }
                if (!Schema::hasColumn('expedientes_alumno', 'fecha_area_1')) {
                    $table->timestamp('fecha_area_1')->nullable();
                }
                if (!Schema::hasColumn('expedientes_alumno', 'fecha_area_2')) {
                    $table->timestamp('fecha_area_2')->nullable();
                }
                if (!Schema::hasColumn('expedientes_alumno', 'fecha_contabilidad')) {
                    $table->timestamp('fecha_contabilidad')->nullable();
                }
            });
        }

        // Asegurar columna CBU y Banco en tabla docentes para validar requisito de CBU obligatorio antes de operar/cobrar
        if (Schema::hasTable('docentes') && !Schema::hasColumn('docentes', 'cbu')) {
            Schema::table('docentes', function (Blueprint $table) {
                $table->string('cbu', 30)->nullable();
                if (!Schema::hasColumn('docentes', 'banco')) {
                    $table->string('banco', 80)->nullable()->default('Banco Rioja S.A.U.');
                }
            });
        }

        // 1. Tabla dinámica de Procedimientos / Requisitos por Área (permite crecer de 1 a 20 procedimientos por sector)
        if (!Schema::hasTable('procedimientos_area')) {
            Schema::create('procedimientos_area', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('empresa_id')->default(2);
                $table->string('area_codigo', 50)->index(); // mesa_entrada, docente_padre, auditoria_docente, area_1, area_2, contabilidad
                $table->string('nombre', 180);
                $table->string('descripcion', 255)->nullable();
                $table->integer('orden')->default(1);
                $table->boolean('es_obligatorio')->default(true);
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        // 2. Tabla de Tildes / Cumplimientos por Expediente y Procedimiento (Trazabilidad de quién autorizó y cuándo)
        if (!Schema::hasTable('cumplimientos_procedimiento_expediente')) {
            Schema::create('cumplimientos_procedimiento_expediente', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('expediente_id')->index('idx_cpe_exp_id');
                $table->unsignedBigInteger('procedimiento_area_id')->index('idx_cpe_proc_id');
                $table->string('area_codigo', 50)->index('idx_cpe_area');
                $table->boolean('cumplido')->default(false);
                $table->string('responsable_nombre', 140)->nullable();
                $table->string('sello_validacion', 120)->nullable();
                $table->string('observaciones', 255)->nullable();
                $table->timestamp('fecha_cumplimiento')->nullable();
                $table->timestamps();
            });
        }

        // Poblar los procedimientos iniciales por defecto si la tabla está vacía
        if (ProcedimientoArea::count() === 0) {
            $iniciales = [
                [
                    'empresa_id' => 2,
                    'area_codigo' => 'mesa_entrada',
                    'nombre' => 'Apertura de Pre-Expediente (Padre Titular + Niño + Escuela)',
                    'descripcion' => 'Registro de solicitud del padre afiliado, datos del niño, escuela y docente (si ya fue hablada).',
                    'orden' => 1,
                    'es_obligatorio' => true,
                    'activo' => true,
                ],
                [
                    'empresa_id' => 2,
                    'area_codigo' => 'docente_padre',
                    'nombre' => 'CBU Bancario del Docente Verificado (22 dígitos)',
                    'descripcion' => 'El docente debe tener cargado su CBU bancario para poder operar y cobrar.',
                    'orden' => 1,
                    'es_obligatorio' => true,
                    'activo' => true,
                ],
                [
                    'empresa_id' => 2,
                    'area_codigo' => 'docente_padre',
                    'nombre' => 'Validación de Datos del Alumno, Escuela y Horario (Máx. 3 hs/día)',
                    'descripcion' => 'Confirmación de institución educativa, CUD vigente y carga horaria de hasta 3 hs diarias.',
                    'orden' => 2,
                    'es_obligatorio' => true,
                    'activo' => true,
                ],
                [
                    'empresa_id' => 2,
                    'area_codigo' => 'auditoria_docente',
                    'nombre' => 'Procedimiento de validación',
                    'descripcion' => 'Visado técnico de Auditoría Docente sobre legajo profesional, CUD y vínculo Padre-Alumno.',
                    'orden' => 1,
                    'es_obligatorio' => true,
                    'activo' => true,
                ],
                [
                    'empresa_id' => 2,
                    'area_codigo' => 'area_1',
                    'nombre' => 'Procedimiento de validación',
                    'descripcion' => 'Sello de validación del Área N° 1 verificando que las etapas previas estén en regla.',
                    'orden' => 1,
                    'es_obligatorio' => true,
                    'activo' => true,
                ],
                [
                    'empresa_id' => 2,
                    'area_codigo' => 'area_2',
                    'nombre' => 'Procedimiento de validación',
                    'descripcion' => 'Sello de validación del Área N° 2 que cierra el circuito y emite la Resolución Oficial Secuenciada.',
                    'orden' => 1,
                    'es_obligatorio' => true,
                    'activo' => true,
                ],
                [
                    'empresa_id' => 2,
                    'area_codigo' => 'contabilidad',
                    'nombre' => 'Procedimiento de validación y Pase a Liquidación / Gobierno (e-SIDIF)',
                    'descripcion' => 'Verifica Resolución Secuenciada y CBU Docente para incluir en el lote de pago mensual.',
                    'orden' => 1,
                    'es_obligatorio' => true,
                    'activo' => true,
                ],
            ];

            foreach ($iniciales as $item) {
                ProcedimientoArea::create($item);
            }
        }
    }

    public static function getConfigAreas(): array
    {
        $path = storage_path('app/apos_areas_config.json');
        $defaults = [
            'mesa_entrada' => 'Mesa de Entrada APOS',
            'area_1' => 'Área N° 1',
            'area_1_req' => 'Procedimiento de validación',
            'area_2' => 'Área N° 2',
            'area_2_req' => 'Procedimiento de validación',
            'contabilidad' => 'Contabilidad y Liquidación (e-SIDIF)',
        ];
        if (file_exists($path)) {
            $loaded = json_decode((string)file_get_contents($path), true);
            if (is_array($loaded)) {
                return array_merge($defaults, $loaded);
            }
        }
        return $defaults;
    }

    public static function saveConfigAreas(array $data): void
    {
        $current = self::getConfigAreas();
        $merged = array_merge($current, $data);
        @file_put_contents(storage_path('app/apos_areas_config.json'), json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public static function generarSiguientePreExpediente(?int $seedId = null): string
    {
        $next = ($seedId ?: ((int)self::max('id') + 1));
        return 'PRE-EXP-' . date('Y') . '-' . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }

    public static function generarSiguienteResolucionSecuenciada(): string
    {
        $maxNum = 1400;
        $existentes = self::where('estado_auditoria', 'aprobado')
            ->whereNotNull('nro_resolucion')
            ->pluck('nro_resolucion');

        foreach ($existentes as $res) {
            if (preg_match('/(\d{3,6})$/', (string)$res, $m)) {
                $val = (int)$m[1];
                if ($val < 90000 && $val > $maxNum) {
                    $maxNum = $val;
                }
            }
        }

        $siguiente = $maxNum + 1;
        return 'RES-APOS-' . date('Y') . '-' . str_pad((string)$siguiente, 4, '0', STR_PAD_LEFT);
    }

    public function tieneResolucionOficial(): bool
    {
        return ($this->attributes['estado_auditoria'] ?? '') === 'aprobado'
            && !empty($this->attributes['nro_resolucion'])
            && !str_contains(strtoupper($this->attributes['nro_resolucion']), 'PRE-EXP')
            && !str_contains(strtoupper($this->attributes['nro_resolucion']), 'TRÁMITE');
    }

    public function getTieneResolucionFinalAttribute(): bool
    {
        return $this->tieneResolucionOficial();
    }

    public function getCodigoPreExpedienteAttribute(): string
    {
        if (!empty($this->attributes['nro_pre_expediente'])) {
            return $this->attributes['nro_pre_expediente'];
        }
        return 'PRE-EXP-' . date('Y') . '-' . str_pad((string)($this->attributes['id'] ?? 1), 4, '0', STR_PAD_LEFT);
    }

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

    public function cumplimientos()
    {
        return $this->hasMany(CumplimientoProcedimientoExpediente::class, 'expediente_id');
    }

    /**
     * Marca todos los procedimientos activos de un área como cumplidos (con tilde y sello en la tabla)
     */
    public function marcarProcedimientosAreaCumplidos(string $areaCodigo, string $responsable): void
    {
        $procs = ProcedimientoArea::where('area_codigo', $areaCodigo)->where('activo', true)->get();
        foreach ($procs as $proc) {
            CumplimientoProcedimientoExpediente::updateOrCreate(
                [
                    'expediente_id' => $this->id,
                    'procedimiento_area_id' => $proc->id,
                ],
                [
                    'area_codigo' => $areaCodigo,
                    'cumplido' => true,
                    'responsable_nombre' => $responsable,
                    'sello_validacion' => 'SELLO-' . strtoupper(substr(md5($this->id . '-' . $proc->id . '-' . $areaCodigo), 0, 8)),
                    'fecha_cumplimiento' => now(),
                ]
            );
        }
    }

    /**
     * Calcula el porcentaje de avance del expediente en el circuito APOS
     */
    public function getPorcentajeProgresoAttribute()
    {
        if ($this->ok_contabilidad) {
            return 100;
        }
        if ($this->estado_auditoria === 'aprobado' || $this->ok_area_2) {
            return 92;
        }
        if ($this->ok_area_1) {
            return 75;
        }
        if ($this->visto_bueno_auditor) {
            return 58;
        }
        if ($this->ok_docente_padre) {
            return 40;
        }
        return 20;
    }

    /**
     * Retorna la etiqueta del paso actual según las áreas de APOS
     */
    public function getPasoActualLabelAttribute()
    {
        $areas = self::getConfigAreas();
        if ($this->ok_contabilidad) {
            return '6/6 • En Contabilidad / Lote Gobierno e-SIDIF (' . ($this->nro_resolucion ?: 'Oficial') . ')';
        }
        if ($this->estado_auditoria === 'aprobado' || $this->ok_area_2) {
            return '5/6 • Resolución Emitida (' . ($this->nro_resolucion ?: 'Oficial') . ') • Listo para Contabilidad';
        }
        if ($this->ok_area_1 && !$this->ok_area_2) {
            return '4/6 • En ' . $areas['area_2'] . ' (Pendiente Sello de Validación)';
        }
        if ($this->visto_bueno_auditor && !$this->ok_area_1) {
            return '3/6 • En ' . $areas['area_1'] . ' (Pendiente Sello de Validación)';
        }
        if ($this->ok_docente_padre && !$this->visto_bueno_auditor) {
            return '2/6 • En Auditoría Docente (Revisión de Legajo y Aval Padre)';
        }
        return '1/6 • En Mesa de Entrada / Docente (' . $this->codigo_pre_expediente . ')';
    }
}
