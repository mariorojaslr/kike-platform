<?php

namespace App\Services;

class MedicalAuditAntiFraudService
{
    /**
     * Evalúa un conjunto de prestaciones o asistencias declaradas para detectar patrones anómalos o sospecha de fraude.
     */
    public function auditarPrestaciones(array $items): array
    {
        $alertas = [];
        $puntuacionesRiesgo = 0;

        foreach ($items as $item) {
            // 1. Detección de Exceso de Horas Diarias por Alumno (> 3hs/día)
            if (isset($item['horas_diarias']) && $item['horas_diarias'] > 3) {
                $alertas[] = [
                    'nivel' => 'CRÍTICO',
                    'codigo' => 'EXCESO_LIMITE_RESOLUCION',
                    'descripcion' => "El alumno {$item['alumno']} supera el máximo regulado de 3 horas diarias ({$item['horas_diarias']} hs declaradas).",
                    'item_id' => $item['id'] ?? null
                ];
                $puntuacionesRiesgo += 40;
            }

            // 2. Detección de Solapamiento Horario Docente
            if (isset($item['horario']) && isset($item['docente_id'])) {
                // Algoritmo de cruzamiento de turnos
                if (isset($item['solapado_detectado']) && $item['solapado_detectado'] === true) {
                    $alertas[] = [
                        'nivel' => 'ALTO',
                        'codigo' => 'SOLAPAMIENTO_HORARIO_DOCENTE',
                        'descripcion' => "La docente {$item['docente_nombre']} declara atender 2 alumnos simultáneamente en el rango {$item['horario']}.",
                        'item_id' => $item['id'] ?? null
                    ];
                    $puntuacionesRiesgo += 35;
                }
            }

            // 3. Detección de Factura ARCA Duplicada / CUIT Inexistente
            if (isset($item['cuit_emisor']) && strlen(preg_replace('/[^0-9]/', '', $item['cuit_emisor'])) !== 11) {
                $alertas[] = [
                    'nivel' => 'MEDIO',
                    'codigo' => 'CUIT_EMISOR_INVALIDO',
                    'descripcion' => "La factura presentada posee un CUIT emisor inconsistente ({$item['cuit_emisor']}).",
                    'item_id' => $item['id'] ?? null
                ];
                $puntuacionesRiesgo += 25;
            }
        }

        return [
            'total_analizados' => count($items),
            'alertas_count' => count($alertas),
            'score_riesgo_global' => min(100, $puntuacionesRiesgo),
            'estado_auditoria' => $puntuacionesRiesgo > 50 ? 'REQUIERE_JUNTA_EVALUADORA' : 'APROBADO_CON_OBSERVACIONES',
            'alertas' => $alertas
        ];
    }
}
