<?php

namespace App\Support;

class SpanishTextCleaner
{
    public static function clean(?string $str, bool $titleCase = false): string
    {
        if ($str === null || $str === '') {
            return '';
        }

        // 1. Quitar bytes nulos de UTF-16LE y BOMs
        $str = str_replace(["\x00", "\xFF\xFE", "\xFE\xFF", "\xEF\xBB\xBF"], '', $str);

        // 2. Si no es UTF-8 válido (ej. Windows-1252 de Excel), convertir a UTF-8
        if (!mb_check_encoding($str, 'UTF-8')) {
            $str = mb_convert_encoding($str, 'UTF-8', 'Windows-1252');
        }

        // 3. Corregir Mojibake común (UTF-8 leído como Latin1) y signos '?' de pérdida de encoding
        $mojibake = [
            'Ã¡' => 'á', 'Ã©' => 'é', 'Ã­' => 'í', 'Ã³' => 'ó', 'Ãº' => 'ú',
            'Ã±' => 'ñ', 'Ã‘' => 'Ñ', 'Ã¼' => 'ü', 'Ãœ' => 'Ü',
            'Ã\x81' => 'Á', 'Ã\x89' => 'É', 'Ã\x8d' => 'Í', 'Ã\x93' => 'Ó', 'Ã\x9a' => 'Ú',
            'Â°' => '°', 'Âº' => '°', 'Â¡' => '¡', 'Â¿' => '¿', 'â€¢' => '•', 'â€”' => '—', 'â€“' => '–',
            'N?' => 'N° ', 'n?' => 'N° ', 'N""' => 'N° ', '""' => '"',
            "\xEF\xBF\xBD" => '°', // Carácter de reemplazo Unicode 
        ];
        $str = strtr($str, $mojibake);

        // 4. Normalizar espacios
        $str = trim(preg_replace('/\s+/', ' ', $str));

        if ($titleCase) {
            $str = mb_convert_case(mb_strtolower($str, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        }

        // 5. Restaurar tildes y eñes en nombres propios, apellidos, patologías y términos educativos
        $reemplazos = [
            '/\bMaria\b/u' => 'María',
            '/\bmaria\b/u' => 'María',
            '/\bJose\b/u' => 'José',
            '/\bjose\b/u' => 'José',
            '/\bBenjamin\b/ui' => 'Benjamín',
            '/\bNicolas\b/ui' => 'Nicolás',
            '/\bAgustin\b/ui' => 'Agustín',
            '/\bMartin\b/ui' => 'Martín',
            '/\bJeremias\b/ui' => 'Jeremías',
            '/\bValentin\b/ui' => 'Valentín',
            '/\bBelen\b/ui' => 'Belén',
            '/\bRocio\b/ui' => 'Rocío',
            '/\bLucia\b/ui' => 'Lucía',
            '/\bSofia\b/ui' => 'Sofía',
            '/\bMatias\b/ui' => 'Matías',
            '/\bJoaquin\b/ui' => 'Joaquín',
            '/\bTobias\b/ui' => 'Tobías',
            '/\bIsaias\b/ui' => 'Isaías',
            '/\bGeronimo\b/ui' => 'Gerónimo',
            '/\bMaximo\b/ui' => 'Máximo',
            '/\bSimon\b/ui' => 'Simón',
            '/\bLeon\b/ui' => 'León',
            '/\bInes\b/ui' => 'Inés',
            '/\bNoemi\b/ui' => 'Noemí',
            '/\bAnahi\b/ui' => 'Anahí',
            '/\bDebora\b/ui' => 'Débora',
            '/\bMonica\b/ui' => 'Mónica',
            '/\bVeronica\b/ui' => 'Verónica',
            '/\bJesica\b/ui' => 'Jésica',
            '/\bAngel\b/ui' => 'Ángel',
            '/\bAngeles\b/ui' => 'Ángeles',
            '/\bHector\b/ui' => 'Héctor',
            '/\bRamon\b/ui' => 'Ramón',
            '/\bSaul\b/ui' => 'Saúl',
            '/\bAdan\b/ui' => 'Adán',
            '/\bHernan\b/ui' => 'Hernán',
            '/\bIvan\b/ui' => 'Iván',
            '/\bDario\b/ui' => 'Darío',
            '/\bRaul\b/ui' => 'Raúl',
            '/\bCesar\b/ui' => 'César',
            '/\bLopez\b/ui' => 'López',
            '/\bGomez\b/ui' => 'Gómez',
            '/\bPerez\b/ui' => 'Pérez',
            '/\bDiaz\b/ui' => 'Díaz',
            '/\bPaez\b/ui' => 'Páez',
            '/\bSanchez\b/ui' => 'Sánchez',
            '/\bSanches\b/ui' => 'Sánchez',
            '/\bRodriguez\b/ui' => 'Rodríguez',
            '/\bFernandez\b/ui' => 'Fernández',
            '/\bGonzalez\b/ui' => 'González',
            '/\bGonzales\b/ui' => 'González',
            '/\bMartinez\b/ui' => 'Martínez',
            '/\bGutierrez\b/ui' => 'Gutiérrez',
            '/\bSuarez\b/ui' => 'Suárez',
            '/\bJuarez\b/ui' => 'Juárez',
            '/\bNuñez\b/ui' => 'Núñez',
            '/\bNunez\b/ui' => 'Núñez',
            '/\bPena\b/ui' => 'Peña',
            '/\bIbanez\b/ui' => 'Ibáñez',
            '/\bYanez\b/ui' => 'Yáñez',
            '/\bVillafane\b/ui' => 'Villafañe',
            '/\bOrmeno\b/ui' => 'Ormeño',
            '/\bCastanar\b/ui' => 'Castañar',
            '/\bAguero\b/ui' => 'Agüero',
            '/\bCordoba\b/ui' => 'Córdoba',
            '/\bCordova\b/ui' => 'Córdoba',
            '/\bAlvarez\b/ui' => 'Álvarez',
            '/\bAvila\b/ui' => 'Ávila',
            '/\bDavila\b/ui' => 'Dávila',
            '/\bCaceres\b/ui' => 'Cáceres',
            '/\bZarate\b/ui' => 'Zárate',
            '/\bLujan\b/ui' => 'Luján',
            '/\bBazan\b/ui' => 'Bazán',
            '/\bGaitan\b/ui' => 'Gaitán',
            '/\bGaetan\b/ui' => 'Gaetán',
            '/\bMelian\b/ui' => 'Melián',
            '/\bGuzman\b/ui' => 'Guzmán',
            '/\bRoldan\b/ui' => 'Roldán',
            '/\bSantillan\b/ui' => 'Santillán',
            '/\bLeguizamon\b/ui' => 'Leguizamón',
            '/\bRamirez\b/ui' => 'Ramírez',
            '/\bBenitez\b/ui' => 'Benítez',
            '/\bCortes\b/ui' => 'Cortés',
            '/\beducacion\b/ui' => 'Educación',
            '/\bedcuacion\b/ui' => 'Educación',
            '/\beducaion\b/ui' => 'Educación',
            '/\besducacion\b/ui' => 'Educación',
            '/\beduacion\b/ui' => 'Educación',
            '/\borientacion\b/ui' => 'Orientación',
            '/\borintacion\b/ui' => 'Orientación',
            '/\borientacin\b/ui' => 'Orientación',
            '/\bpsicopedagogia\b/ui' => 'Psicopedagogía',
            '/\bhipoacusicos\b/ui' => 'Hipoacúsicos',
            '/\bhipoacusia\b/ui' => 'Hipoacusia',
            '/\bgestion\b/ui' => 'Gestión',
            '/\bperturbacion\b/ui' => 'Perturbación',
            '/\bpertubacion\b/ui' => 'Perturbación',
            '/\bperyrbacion\b/ui' => 'Perturbación',
            '/\batencion\b/ui' => 'Atención',
            '/\bespecifico\b/ui' => 'Específico',
            '/\bespecificos\b/ui' => 'Específicos',
            '/\btranstorno\b/ui' => 'Trastorno',
            '/\btranstornos\b/ui' => 'Trastornos',
            '/\bsindrome\b/ui' => 'Síndrome',
            '/\bparalisis\b/ui' => 'Parálisis',
            '/\bniñes\b/ui' => 'niñez',
            '/\bninez\b/ui' => 'niñez',
            '/\bPio Xii\b/ui' => 'Pío XII',
            '/\bJardin\b/ui' => 'Jardín',
            '/\bSeptimo\b/ui' => 'Séptimo',
            '/\bQuinto\b/ui' => 'Quinto',
            '/\bDivision\b/ui' => 'División',
            '/\bManana\b/ui' => 'Mañana',
            '/\bQuinteroscalderonlorenasoledad\b/ui' => 'Quinteros Calderón Lorena Soledad',
        ];

        $str = preg_replace(array_keys($reemplazos), array_values($reemplazos), $str);

        return $str;
    }
}
