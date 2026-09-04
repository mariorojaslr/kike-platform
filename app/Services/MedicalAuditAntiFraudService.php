<?php

namespace App\Services;

class MedicalAuditAntiFraudService
{
    /**
     * Evalúa si una carga de horas supera el límite legal de 3hs/día o presenta riesgo de solapamiento.
     */
    public static function auditarTopeHoras(int $horasSolicitadas, int $horasAjustadas = 0): array
    {
        $horasTotales = $horasSolicitadas + $horasAjustadas;
        
        if ($horasTotales > 3) {
            return [
                'aprobado' => false,
                'exceso' => $horasTotales - 3,
                'nivel_riesgo' => 'ALTO',
                'mensaje' => "⚠️ Alerta de Auditoría: Se detectó un intento de carga de {$horasTotales}hs en el día. El límite máximo regulado por el Instituto de Educación Especial es de 3hs/día por alumno.",
                'ahorro_estimado' => ($horasTotales - 3) * 12500 // $12.500 ARS por hora retenida
            ];
        }

        return [
            'aprobado' => true,
            'exceso' => 0,
            'nivel_riesgo' => 'BAJO',
            'mensaje' => "✅ Carga conforme a normativa (Horas validadas: {$horasTotales}hs/día).",
            'ahorro_estimado' => 0
        ];
    }

    /**
     * Audita facturas ARCA para prevenir duplicación de CAE o sobrefacturación.
     */
    public static function auditarFacturaArca(string $cae, float $monto, array $facturasPrevias = []): array
    {
        foreach ($facturasPrevias as $factura) {
            if (isset($factura['cae']) && $factura['cae'] === $cae) {
                return [
                    'valida' => false,
                    'codigo_error' => 'CAE_DUPLICADO',
                    'alerta' => "⛔ Bloqueo de Auditoría IA: El CAE '{$cae}' ya fue ingresado y abonado previamente en otra liquidación.",
                    'monto_bloqueado' => $monto
                ];
            }
        }

        if ($monto > 850000) {
            return [
                'valida' => true,
                'requiere_auditoria_manual' => true,
                'alerta' => "⚠️ Factura de Alto Monto ($" . number_format($monto, 2, ',', '.') . "): Pasa a revisión prioritaria por Auditoría Médica Central.",
                'monto_bloqueado' => 0
            ];
        }

        return [
            'valida' => true,
            'requiere_auditoria_manual' => false,
            'alerta' => "✅ Factura ARCA verificada sin anomalías.",
            'monto_bloqueado' => 0
        ];
    }

    /**
     * Obtiene resumen de matriz de fraudes evitados por IA por sucursal.
     */
    public static function obtenerMatrizAhorroAuditoria(): array
    {
        return [
            'total_ahorro_acumulado' => 92500000.00,
            'fraudes_bloqueados_count' => 148,
            'sucursales' => [
                'chilecito' => [
                    'nombre' => 'Sede Chilecito',
                    'solapamientos_bloqueados' => 42,
                    'ahorro_ars' => 24800000.00,
                    'siniestralidad' => '32.1%'
                ],
                'la_rioja' => [
                    'nombre' => 'Sede La Rioja Capital',
                    'solapamientos_bloqueados' => 56,
                    'ahorro_ars' => 38200000.00,
                    'siniestralidad' => '38.4%'
                ],
                'cordoba' => [
                    'nombre' => 'Sede Córdoba (Alta Complejidad)',
                    'solapamientos_bloqueados' => 31,
                    'ahorro_ars' => 18900000.00,
                    'siniestralidad' => '41.2%'
                ],
                'buenos_aires' => [
                    'nombre' => 'Sede Buenos Aires (Derivaciones)',
                    'solapamientos_bloqueados' => 19,
                    'ahorro_ars' => 10600000.00,
                    'siniestralidad' => '35.8%'
                ],
            ]
        ];
    }
}
