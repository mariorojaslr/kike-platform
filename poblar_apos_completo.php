<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Titular;
use App\Models\Familiar;
use App\Models\Docente;
use App\Models\Escuela;
use App\Models\Diagnostico;
use App\Models\Formacion;
use App\Models\ExpedienteAlumno;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== 1. VERIFICANDO Y ACTUALIZANDO ESQUEMA DE BASE DE DATOS ===\n";

// 1.1 Columnas en empresas
Schema::table('empresas', function (Blueprint $table) {
    if (!Schema::hasColumn('empresas', 'parent_id')) {
        $table->unsignedBigInteger('parent_id')->nullable()->after('id');
    }
    if (!Schema::hasColumn('empresas', 'tipo_entidad')) {
        $table->string('tipo_entidad')->default('cliente')->after('nombre');
    }
    if (!Schema::hasColumn('empresas', 'subagencia_nombre')) {
        $table->string('subagencia_nombre')->nullable()->after('tipo_entidad');
    }
    if (!Schema::hasColumn('empresas', 'capitas_afiliados')) {
        $table->integer('capitas_afiliados')->default(0)->after('limite_usuarios');
    }
});

// 1.2 Columnas en diagnosticos
Schema::table('diagnosticos', function (Blueprint $table) {
    if (!Schema::hasColumn('diagnosticos', 'codigo')) {
        $table->string('codigo', 50)->nullable()->after('empresa_id');
    }
});

// 1.3 Columnas en familiares
Schema::table('familiares', function (Blueprint $table) {
    if (!Schema::hasColumn('familiares', 'n_afiliado')) {
        $table->string('n_afiliado')->nullable()->after('dni');
    }
    if (!Schema::hasColumn('familiares', 'parentesco')) {
        $table->string('parentesco')->default('Hijo/a')->after('n_afiliado');
    }
    if (!Schema::hasColumn('familiares', 'tiene_patologia')) {
        $table->boolean('tiene_patologia')->default(true)->after('parentesco');
    }
    if (Schema::hasColumn('familiares', 'diagnostico') && !Schema::hasColumn('familiares', 'diagnostico_detalle')) {
        $table->renameColumn('diagnostico', 'diagnostico_detalle');
    } elseif (!Schema::hasColumn('familiares', 'diagnostico_detalle')) {
        $table->text('diagnostico_detalle')->nullable()->after('diagnostico_id');
    }
    if (!Schema::hasColumn('familiares', 'escuela_id')) {
        $table->unsignedBigInteger('escuela_id')->nullable()->after('diagnostico');
    }
    if (!Schema::hasColumn('familiares', 'docente_id')) {
        $table->unsignedBigInteger('docente_id')->nullable()->after('escuela_id');
    }
    if (!Schema::hasColumn('familiares', 'resolucion')) {
        $table->string('resolucion')->nullable()->after('docente_id');
    }
    if (!Schema::hasColumn('familiares', 'grado_division')) {
        $table->string('grado_division')->nullable();
    }
    if (!Schema::hasColumn('familiares', 'turno')) {
        $table->string('turno')->nullable();
    }
    if (!Schema::hasColumn('familiares', 'horario')) {
        $table->string('horario')->nullable();
    }
    if (!Schema::hasColumn('familiares', 'foto_perfil')) {
        $table->string('foto_perfil')->nullable();
    }
});

echo "=== 2. CONFIGURANDO SUBAGENCIA (SOLUCIONES DIGITALES MCR) Y CLIENTE (APOS LA RIOJA) ===\n";

$provinciaLaRioja = DB::table('provincias')->where('nombre', 'LIKE', '%Rioja%')->first();
$provinciaId = $provinciaLaRioja ? $provinciaLaRioja->id : 12;

// Empresa ID 1 -> Subagencia: Soluciones Digitales MCR (Provincia de La Rioja)
DB::table('empresas')->updateOrInsert(
    ['id' => 1],
    [
        'parent_id' => null,
        'nombre' => 'Soluciones Digitales MCR',
        'tipo_entidad' => 'subagencia',
        'subagencia_nombre' => 'Agencia Oficial Provincial - La Rioja',
        'cuit' => '30-71654321-9',
        'logo' => 'logos_empresas/mcr_logo.svg',
        'color_primario' => '#6366f1',
        'color_secundario' => '#1e1b4b',
        'provincia_id' => $provinciaId,
        'localidad_id' => null, // Toda la provincia
        'capitas_afiliados' => 160000,
        'limite_usuarios' => 100,
        'limite_mb' => 5000,
        'consumo_actual_mb' => 120.0,
        'estado_cuenta' => 'al_dia',
        'plan_tipo' => 'personalizado',
        'proximo_vencimiento' => now()->addMonths(6),
        'updated_at' => now(),
    ]
);

// Empresa ID 2 -> Cliente Final: APOS - Obra Social Provincial (La Rioja)
DB::table('empresas')->updateOrInsert(
    ['id' => 2],
    [
        'parent_id' => 1,
        'nombre' => 'APOS - Obra Social Provincial',
        'tipo_entidad' => 'obra_social',
        'subagencia_nombre' => 'Soluciones Digitales MCR (La Rioja)',
        'cuit' => '30-67185933-9',
        'logo' => 'logos_empresas/apos_logo.svg',
        'color_primario' => '#0284c7',
        'color_secundario' => '#0f172a',
        'provincia_id' => $provinciaId,
        'localidad_id' => null, // Cobertura en toda la Provincia de La Rioja
        'capitas_afiliados' => 160000,
        'limite_usuarios' => 500,
        'limite_mb' => 10000,
        'consumo_actual_mb' => 485.4,
        'estado_cuenta' => 'al_dia',
        'plan_tipo' => 'personalizado',
        'modalidad_cobro' => 'por_afiliado',
        'proximo_vencimiento' => now()->addMonths(3),
        'notas_facturacion' => 'Cliente Institucional de Soluciones Digitales MCR (Provincia de La Rioja). Padrón Global: 160.000 afiliados. Módulo Activo: Área Docentes Integradores / Educación Especial.',
        'updated_at' => now(),
    ]
);

$aposId = 2;

// Configurar usuarios clave
DB::table('users')->where('id', 2)->update([
    'name' => 'Administración General APOS',
    'empresa_id' => $aposId,
    'role' => 'empresa',
    'updated_at' => now(),
]);

// Crear usuario para la Subagencia Soluciones Digitales MCR si no existe
User::updateOrCreate(
    ['email' => 'mcr@solucionesdigitales.com.ar'],
    [
        'name' => 'Dirección Soluciones Digitales MCR (La Rioja)',
        'password' => Hash::make('12345678'),
        'role' => 'empresa',
        'empresa_id' => 1,
    ]
);

// Crear usuario Auditor de APOS si no existe
User::updateOrCreate(
    ['email' => 'auditoria.docentes@aposlarioja.gob.ar'],
    [
        'name' => 'Jefe de Área & Auditoría Docente APOS',
        'password' => Hash::make('12345678'),
        'role' => 'auditor',
        'empresa_id' => $aposId,
    ]
);

echo "=== 3. LIMPIANDO DATOS DE PRUEBA HUÉRFANOS Y NORMALIZANDO DOCENTES ===\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
// Borrar los 20 docentes falsos sin empresa_id creados por el viejo DemoSeeder
DB::table('docentes')->whereNull('empresa_id')->delete();
DB::table('beneficiarios')->truncate();
DB::table('titulars')->whereNull('empresa_id')->delete();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// Asegurar que todas las formaciones pertenezcan a APOS o sean globales limpias
DB::table('formaciones')->where('empresa_id', 1)->update(['empresa_id' => $aposId]);

// Cargar padrón maestro de 125 docentes con especialidad, teléfono, email y dirección desde TSV si existe
$tsvDocentes = __DIR__ . '/database/seeders/apos_docentes.tsv';
if (file_exists($tsvDocentes)) {
    $tsvLines = file($tsvDocentes, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($tsvLines as $tIdx => $tLine) {
        if ($tIdx === 0) continue; // cabecera
        $parts = explode("\t", $tLine);
        if (count($parts) < 2) continue;
        $dNombre = trim($parts[0] ?? '');
        $dDni    = preg_replace('/[^0-9]/', '', $parts[1] ?? '');
        $dRes    = ($parts[2] ?? 'NULL') !== 'NULL' ? trim($parts[2]) : null;
        $dTel    = ($parts[3] ?? 'NULL') !== 'NULL' ? trim($parts[3]) : null;
        $dEmail  = ($parts[4] ?? 'NULL') !== 'NULL' ? trim($parts[4]) : null;
        $dDir    = ($parts[5] ?? 'NULL') !== 'NULL' ? trim($parts[5]) : null;
        $dForm   = ($parts[6] ?? 'NULL') !== 'NULL' ? trim($parts[6]) : 'Profesora en Educación Especial';

        if (!$dDni) continue;

        $formObj = Formacion::firstOrCreate(
            ['nombre' => $dForm],
            ['empresa_id' => $aposId]
        );

        Docente::updateOrCreate(
            ['dni' => $dDni],
            [
                'empresa_id'   => $aposId,
                'nombre'       => $dNombre,
                'resolucion'   => $dRes,
                'telefono'     => $dTel,
                'email'        => $dEmail,
                'direccion'    => $dDir,
                'formacion_id' => $formObj->id,
            ]
        );
    }
}

// Función auxiliar para embellecer nombres y textos
function cleanName($str) {
    $str = trim(preg_replace('/\s+/', ' ', $str));
    // Reemplazar signos de pregunta por Ñ o N° según contexto
    $str = str_replace(['N?', 'n?'], 'N° ', $str);
    return mb_convert_case(mb_strtolower($str, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
}

function cleanSchoolName($raw) {
    $s = trim(preg_replace('/\s+/', ' ', $raw));
    $s = str_replace(['"', "''", 'N?', 'n?', 'N""'], ['', '', 'N° ', 'N° ', 'N° '], $s);
    $lower = mb_strtolower($s, 'UTF-8');

    if (str_contains($lower, 'eva per')) return 'Escuela de Gestión Comunitaria Eva Perón';
    if (str_contains($lower, 'gabriela mistral')) return 'Escuela N° 252 Gabriela Mistral';
    if (str_contains($lower, 'telechea')) return 'Escuela N° 244 Francisco Telechea';
    if (str_contains($lower, 'mariano moreno') || str_contains($lower, '177')) return 'Escuela N° 177 Dr. Mariano Moreno';
    if (str_contains($lower, 'pio xii') || str_contains($lower, 'pio 12') || str_contains($lower, 'pio12')) return 'Centro Educativo Diocesano Pío XII';
    if (str_contains($lower, 'castro barros') && str_contains($lower, '245')) return 'Escuela N° 245 Dr. Pedro I. de Castro Barros';
    if (str_contains($lower, 'castro barros') || str_contains($lower, 'escuela normal')) return 'Escuela Normal N° 1 Dr. Pedro Ignacio de Castro Barros';
    if (str_contains($lower, 'timoteo gordillo') || str_contains($lower, '39')) return 'Escuela N° 39 Timoteo Gordillo';
    if (str_contains($lower, 'federalito') && str_contains($lower, 'jardin')) return 'Jardín Privado Federalito';
    if (str_contains($lower, 'federalito')) return 'Escuela Privada Federalito';
    if (str_contains($lower, 'santo tomas') || str_contains($lower, 'santo tomás')) return 'Colegio Privado Santo Tomás de Aquino';
    if (str_contains($lower, 'fangio')) return 'Escuela Municipal N° 1 Juan Manuel Fangio';
    if (str_contains($lower, 'belgrano') && str_contains($lower, '249')) return 'Escuela N° 249 Gral. Manuel Belgrano';
    if (str_contains($lower, 'humberto pereyra') || str_contains($lower, '369')) return 'Escuela N° 369 Maestro Humberto Pereyra';
    if (str_contains($lower, 'albert sabin')) return 'Jardín Maternal Municipal N° 2 Dr. Albert Sabin';
    if (str_contains($lower, 'santa cecilia')) return 'Instituto Privado Santa Cecilia';
    if (str_contains($lower, 'sagrado corazon') || str_contains($lower, 'sagrado corazón')) return 'Colegio Sagrado Corazón de Jesús';
    if (str_contains($lower, 'san francisco de as')) return 'Centro Educativo Franciscano San Francisco de Asís';
    if (str_contains($lower, 'dante aligh')) return 'Escuela Privada Bilingüe Dante Alighieri';
    if (str_contains($lower, 'pestalozzi')) return 'Instituto Privado Juan Enrique Pestalozzi';
    if (str_contains($lower, 'benjamin de la vega') || str_contains($lower, 'benjamín de la vega') || str_contains($lower, '243')) return 'Escuela N° 243 Gob. Benjamín de la Vega';
    if (str_contains($lower, 'rosario vera pe') && str_contains($lower, 'jardin')) return 'Jardín de Infantes N° 6 Rosario Vera Peñaloza';
    if (str_contains($lower, 'rosario vera pe') || str_contains($lower, '258')) return 'Escuela N° 258 Rosario Vera Peñaloza';
    if (str_contains($lower, 'zelada') || str_contains($lower, '361')) return 'Escuela N° 361 Expedición Zelada y Dávila';
    if (str_contains($lower, 'enrique f') || str_contains($lower, 'comercio n° 1') || str_contains($lower, 'comercio n1') || str_contains($lower, 'comercio n 1')) return 'Escuela Prov. de Comercio N° 1 Prof. Enrique F. Clara';
    if (str_contains($lower, '417')) return 'Escuela N° 417';
    if (str_contains($lower, '410')) return 'Escuela N° 410';
    if (str_contains($lower, '408')) return 'Escuela N° 408 Faldeo del Velasco Sur';
    if (str_contains($lower, '418') || str_contains($lower, 'los obreros')) return 'Escuela N° 418 Los Obreros';
    if (str_contains($lower, 'geronima barros') || str_contains($lower, 'gerónima barros') || str_contains($lower, '248')) return 'Escuela N° 248 Francisca Gerónima Barros';
    if (str_contains($lower, 'ceferino namuncur')) return 'Escuela N° 251 Ceferino Namuncurá';
    if (str_contains($lower, 'happy kid')) return 'Jardín de Infantes Happy Kids';
    if (str_contains($lower, 'montessori') || str_contains($lower, 'montasori')) return 'Jardín Maternal N° 2 María Montessori';
    if (str_contains($lower, 'colegio provincial n° 13') || str_contains($lower, 'colegio provincial n 13') || str_contains($lower, 'colegio provincial n13')) return 'Colegio Provincial N° 13';
    if (str_contains($lower, 'colegio provincial n° 10') || str_contains($lower, 'colegio provincial n 10') || str_contains($lower, 'colegio 10') || str_contains($lower, 'colegio n°10')) return 'Colegio Provincial N° 10';
    if (str_contains($lower, 'colegio provincial n° 19') || str_contains($lower, 'colegio n° 19') || str_contains($lower, 'colegio 19') || str_contains($lower, 'colegio n 19')) return 'Colegio Provincial N° 19';
    if (str_contains($lower, 'joaquín v') || str_contains($lower, 'joaquin v') || str_contains($lower, 'colegio provincial n 1') || str_contains($lower, 'colegio provincial numero 1') || str_contains($lower, 'colegio provincial n°1')) return 'Colegio Provincial N° 1 Joaquín V. González';

    return mb_convert_case($lower, MB_CASE_TITLE, 'UTF-8');
}

// Clasificador inteligente de Patología CUD para asociar al catálogo maestro con su código CIE-10 / CUD
function classifyDiagnostico($rawDiag, $aposId) {
    $lower = mb_strtolower(trim($rawDiag), 'UTF-8');
    
    if (str_contains($lower, 'asperger')) {
        $nombre = 'Síndrome de Asperger / Trastorno del Espectro Autista (TEA)';
        $codigo = 'CUD-F84.5';
    } elseif (str_contains($lower, 'autis') || str_contains($lower, 'tea') || str_contains($lower, 'f840')) {
        if (str_contains($lower, 'habla') || str_contains($lower, 'lenguaje')) {
            $nombre = 'Autismo en la Niñez (TEA) y Trastorno del Desarrollo del Habla y Lenguaje';
            $codigo = 'CUD-F84.0/F80';
        } else {
            $nombre = 'Trastorno del Espectro Autista (TEA) - Autismo en la Niñez';
            $codigo = 'CUD-F84.0';
        }
    } elseif (str_contains($lower, 'down') || str_contains($lower, 'dow')) {
        $nombre = 'Síndrome de Down y Trastorno del Desarrollo del Habla y Lenguaje';
        $codigo = 'CUD-Q90.9';
    } elseif (str_contains($lower, 'paralisis cerebral') || str_contains($lower, 'parálisis cerebral') || str_contains($lower, 'hemiplejia') || str_contains($lower, 'paraplejia') || str_contains($lower, 'silla de ruedas') || str_contains($lower, 'distrofia muscular')) {
        $nombre = 'Parálisis Cerebral Infantil / Trastorno Motor y de la Movilidad';
        $codigo = 'CUD-G80.9';
    } elseif (str_contains($lower, 'hipoacusia') || str_contains($lower, 'audición')) {
        $nombre = 'Hipoacusia Neurosensorial Bilateral y Trastorno del Habla';
        $codigo = 'CUD-H90.3';
    } elseif (str_contains($lower, 'ceguera') || str_contains($lower, 'vision subnormal') || str_contains($lower, 'atrofia optica')) {
        $nombre = 'Disminución Visual Severa / Atrofia Óptica y Apoyo a la Integración';
        $codigo = 'CUD-H54.0';
    } elseif (str_contains($lower, 'epilepsia') || str_contains($lower, 'meningitis')) {
        $nombre = 'Epilepsia / Secuela Neurológica y Trastorno de Habilidades Escolares';
        $codigo = 'CUD-G40.9';
    } elseif (str_contains($lower, 'retraso mental') || str_contains($lower, 'discapacidad intelectual') || str_contains($lower, 'retraso madurativo')) {
        $nombre = 'Discapacidad Intelectual / Retraso Madurativo Global';
        $codigo = 'CUD-F79.9';
    } elseif (str_contains($lower, 'dislexia') || str_contains($lower, 'lectura')) {
        $nombre = 'Dislexia, Alexia y Trastorno Específico de las Habilidades Escolares';
        $codigo = 'CUD-F81.0';
    } elseif (str_contains($lower, 'actividad y de la atencion') || str_contains($lower, 'actividad y la atencion') || str_contains($lower, 'hiperactividad') || str_contains($lower, 'hipercinetico') || str_contains($lower, 'hipercinético') || str_contains($lower, 'tdah')) {
        $nombre = 'Perturbación de la Actividad y la Atención (TDAH) y Habilidades Escolares';
        $codigo = 'CUD-F90.0';
    } elseif (str_contains($lower, 'generalizado del desarrollo')) {
        $nombre = 'Trastorno Generalizado del Desarrollo (TGD)';
        $codigo = 'CUD-F84.9';
    } elseif (str_contains($lower, 'habla') || str_contains($lower, 'lenguaje')) {
        $nombre = 'Trastorno Específico del Desarrollo del Habla y del Lenguaje';
        $codigo = 'CUD-F80.9';
    } elseif (str_contains($lower, 'habilidades escolares') || str_contains($lower, 'aprendizaje')) {
        $nombre = 'Trastorno Específico del Desarrollo de las Habilidades Escolares';
        $codigo = 'CUD-F81.9';
    } else {
        $nombre = 'Trastorno del Neurodesarrollo y Apoyo a la Integración Escolar';
        $codigo = 'CUD-F89';
    }

    $diag = Diagnostico::where('nombre', $nombre)->first();
    if (!$diag) {
        $diag = Diagnostico::create([
            'empresa_id' => $aposId,
            'codigo' => $codigo,
            'nombre' => $nombre,
        ]);
    } else {
        $diag->update([
            'empresa_id' => $aposId,
            'codigo' => $codigo,
        ]);
    }

    return $diag;
}

// Actualizar los 7 diagnósticos iniciales con códigos CIE-10 / CUD oficiales
$codigosIniciales = [
    1 => ['codigo' => 'CUD-F84.0', 'nombre' => 'Trastorno del Espectro Autista (TEA) - Autismo en la Niñez'],
    2 => ['codigo' => 'CUD-F90.0', 'nombre' => 'Perturbación de la Actividad y la Atención (TDAH) y Habilidades Escolares'],
    3 => ['codigo' => 'CUD-Q90.9', 'nombre' => 'Síndrome de Down y Trastorno del Desarrollo del Habla y Lenguaje'],
    4 => ['codigo' => 'CUD-F88',   'nombre' => 'Retraso Madurativo Global'],
    5 => ['codigo' => 'CUD-G80.9', 'nombre' => 'Parálisis Cerebral Infantil / Trastorno Motor y de la Movilidad'],
    6 => ['codigo' => 'CUD-F80.9', 'nombre' => 'Trastorno Específico del Desarrollo del Habla y del Lenguaje'],
    7 => ['codigo' => 'CUD-F79.9', 'nombre' => 'Discapacidad Intelectual / Retraso Madurativo Global'],
];
foreach ($codigosIniciales as $idDiag => $infoDiag) {
    DB::table('diagnosticos')->where('id', $idDiag)->update([
        'empresa_id' => $aposId,
        'codigo' => $infoDiag['codigo'],
        'nombre' => $infoDiag['nombre'],
    ]);
}

echo "=== 4. PROCESANDO ARCHIVO A (RESUMEN: TITULARES, HIJOS, DOCENTES Y RESOLUCIONES) ===\n";

$csvResumenPath = __DIR__ . '/plantilla_vacia_resumen A.csv';
$resumenRows = [];
if (file_exists($csvResumenPath)) {
    $lines = file($csvResumenPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $idx => $line) {
        if ($idx === 0 || str_contains(strtolower($line), 'titular_apellido')) continue;
        $cols = str_getcsv($line, ';');
        if (count($cols) < 5) continue;
        $resumenRows[] = [
            'titular_nombre'   => cleanName($cols[0] ?? ''),
            'titular_afiliado' => preg_replace('/[^0-9]/', '', $cols[1] ?? ''),
            'alumno_nombre'    => cleanName($cols[2] ?? ''),
            'alumno_afiliado'  => preg_replace('/[^0-9]/', '', $cols[3] ?? ''),
            'docente_nombre'   => cleanName($cols[4] ?? ''),
            'docente_dni'      => preg_replace('/[^0-9]/', '', $cols[5] ?? ''),
            'resolucion'       => trim($cols[6] ?? ''),
        ];
    }
}

echo "Filas leídas de Resumen A: " . count($resumenRows) . "\n";

// También leemos Archivo B (Alumnos con DNI, Diagnóstico CUD, Escuela, Grado, Turno, Horario)
echo "=== 5. PROCESANDO ARCHIVO B (ALUMNOS, DNI, PATOLOGÍAS CUD, ESCUELAS Y HORARIOS) ===\n";

$csvAlumnosPath = __DIR__ . '/plantilla_vacia_alumnos B.csv';
$alumnosDetalleMap = []; // indexado por alumno_afiliado y por nombre normalizado
if (file_exists($csvAlumnosPath)) {
    $content = file_get_contents($csvAlumnosPath);
    $fp = fopen('php://memory', 'r+');
    fwrite($fp, $content);
    rewind($fp);
    $rowIdx = 0;
    while (($cols = fgetcsv($fp, 4000, ';')) !== false) {
        $rowIdx++;
        if ($rowIdx === 1 || count($cols) < 4) continue;
        $alNombre = cleanName($cols[0] ?? '');
        if (empty($alNombre) || str_contains(strtolower($alNombre), 'alumno_apellido')) continue;

        $alDni      = preg_replace('/[^0-9]/', '', $cols[1] ?? '');
        $alAfiliado = preg_replace('/[^0-9]/', '', $cols[2] ?? '');
        $diagRaw    = trim(preg_replace('/\s+/', ' ', $cols[3] ?? ''));
        $escRaw     = trim(preg_replace('/\s+/', ' ', $cols[4] ?? ''));
        $grado      = trim($cols[5] ?? '');
        $turno      = trim($cols[6] ?? '');
        $horario    = trim($cols[7] ?? '');

        $data = [
            'nombre'         => $alNombre,
            'dni'            => $alDni,
            'n_afiliado'     => $alAfiliado,
            'diagnostico_raw'=> $diagRaw,
            'escuela_raw'    => $escRaw,
            'grado_division' => $grado,
            'turno'          => $turno,
            'horario'        => $horario,
        ];

        if ($alAfiliado) {
            $alumnosDetalleMap['afiliado:' . $alAfiliado] = $data;
        }
        $alumnosDetalleMap['nombre:' . mb_strtolower($alNombre, 'UTF-8')] = $data;
    }
    fclose($fp);
}

echo "Detalles de alumnos indexados desde Archivo B: " . count($alumnosDetalleMap) . "\n";

// Limpiar tablas de familiares y expedientes_alumno antes de reconstruir el padrón limpio de APOS
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Si en el servidor expedientes_alumno tenía FK apuntando a la vieja tabla 'familiars' en vez de 'familiares', la quitamos
try {
    DB::statement('ALTER TABLE expedientes_alumno DROP FOREIGN KEY expedientes_alumno_alumno_id_foreign');
} catch (\Throwable $e) {
    // Si ya no existe, ignoramos
}

DB::table('familiares')->truncate();
if (Schema::hasTable('familiars')) {
    DB::table('familiars')->truncate();
}
DB::table('expedientes_alumno')->truncate();
DB::table('escuelas')->where('id', '<=', 2)->delete(); // quitar las 2 escuelas de prueba viejas ("Pepita", "Juanita")


// Embellecer nombres de los docentes existentes en APOS
$docentesExistentes = Docente::where('empresa_id', $aposId)->get();
foreach ($docentesExistentes as $doc) {
    $doc->nombre = cleanName($doc->nombre);
    $doc->save();
}

$titularesProcesados = 0;
$alumnosProcesados = 0;
$expedientesCreados = 0;

foreach ($resumenRows as $row) {
    // 1. Extraer DNI del titular a partir del N° de afiliado de APOS (Ej: 1 + 20253400 + 00 -> 20253400)
    $titAfil = $row['titular_afiliado'];
    $dniExtraido = null;
    if (strlen($titAfil) >= 10 && str_starts_with($titAfil, '1')) {
        $dniExtraido = substr($titAfil, 1, 8);
    } elseif (strlen($titAfil) >= 8) {
        $dniExtraido = substr($titAfil, 0, 8);
    }

    // Buscar Titular por n_afiliado o por DNI
    $titular = null;
    if ($titAfil) {
        $titular = Titular::where('n_afiliado', $titAfil)->first();
    }
    if (!$titular && $dniExtraido) {
        $titular = Titular::where('dni', $dniExtraido)->first();
    }
    if (!$titular) {
        $titular = Titular::where('nombre', $row['titular_nombre'])->first();
    }

    $resolucionFormateada = $row['resolucion'] ? ('RES-' . $row['resolucion']) : null;

    if (!$titular) {
        $dniFinal = $dniExtraido ?: (string)rand(20000000, 42000000);
        // Evitar colisión unique en DNI
        if (Titular::where('dni', $dniFinal)->exists()) {
            $titular = Titular::where('dni', $dniFinal)->first();
        } else {
            $titular = Titular::create([
                'empresa_id' => $aposId,
                'nombre'     => $row['titular_nombre'],
                'dni'        => $dniFinal,
                'cuil'       => '20-' . $dniFinal . '-4',
                'n_afiliado' => $titAfil ?: ('1' . $dniFinal . '00'),
                'resolucion' => $resolucionFormateada,
            ]);
            $titularesProcesados++;
        }
    } else {
        $updTit = [
            'empresa_id' => $aposId,
            'nombre'     => $row['titular_nombre'],
        ];
        if ($titAfil) $updTit['n_afiliado'] = $titAfil;
        if ($resolucionFormateada) $updTit['resolucion'] = $resolucionFormateada;
        if (!$titular->cuil && $titular->dni) {
            $updTit['cuil'] = '27-' . preg_replace('/[^0-9]/', '', $titular->dni) . '-4';
        }
        $titular->update($updTit);
        $titularesProcesados++;
    }

    // 2. Buscar o crear Docente vinculada
    $docente = null;
    if ($row['docente_dni']) {
        $docente = Docente::where('dni', $row['docente_dni'])->first();
        if (!$docente) {
            $formacionDefault = Formacion::where('empresa_id', $aposId)->first();
            $docente = Docente::create([
                'empresa_id'         => $aposId,
                'nombre'             => $row['docente_nombre'],
                'dni'                => $row['docente_dni'],
                'resolucion'         => $resolucionFormateada,
                'formacion_id'       => $formacionDefault ? $formacionDefault->id : null,
                'validado_auditoria' => 1,
                'estado_legajo'      => 'completo',
                'porcentaje_legajo'  => 100,
            ]);
        } else {
            $docente->update([
                'empresa_id' => $aposId,
                'resolucion' => $resolucionFormateada ?: $docente->resolucion,
            ]);
        }
    }

    // 3. Buscar detalles del Alumno en el mapa de Archivo B
    $alAfil = $row['alumno_afiliado'];
    $alNombreKey = 'nombre:' . mb_strtolower($row['alumno_nombre'], 'UTF-8');
    $detalle = $alumnosDetalleMap['afiliado:' . $alAfil] ?? $alumnosDetalleMap[$alNombreKey] ?? null;

    $diagObj = null;
    $diagTexto = 'Trastorno del Neurodesarrollo y Apoyo a la Integración Escolar';
    $escuelaObj = null;
    $alDni = null;
    $grado = 'Primaria';
    $turno = 'Turno Mañana';
    $horario = '08:00 a 11:00 hs';

    if ($detalle) {
        $alDni = $detalle['dni'] ?: null;
        if (!empty($detalle['diagnostico_raw'])) {
            $diagTexto = $detalle['diagnostico_raw'];
            $diagObj = classifyDiagnostico($diagTexto, $aposId);
        }
        if (!empty($detalle['escuela_raw'])) {
            $nombreEscLimpio = cleanSchoolName($detalle['escuela_raw']);
            $escuelaObj = Escuela::firstOrCreate(
                ['nombre' => $nombreEscLimpio, 'empresa_id' => $aposId],
                [
                    'cue'                => '4600' . rand(1000, 9999),
                    'direccion'          => 'La Rioja, Provincia de La Rioja',
                    'telefono'           => '3804-' . rand(400000, 699999),
                    'contacto_principal' => 'Dirección Escolar',
                    'activo'             => 1,
                ]
            );
        }
        if (!empty($detalle['grado_division'])) $grado = $detalle['grado_division'];
        if (!empty($detalle['turno'])) $turno = $detalle['turno'];
        if (!empty($detalle['horario'])) $horario = $detalle['horario'];
    } else {
        $diagObj = classifyDiagnostico($diagTexto, $aposId);
    }

    if (!$alDni) {
        // Si no vino DNI en B, generamos un DNI infantil consistente
        $alDni = '5' . substr(preg_replace('/[^0-9]/', '', $alAfil ?: (string)rand(1000000, 9999999)), -7);
    }

    // Crear o actualizar Familiar (Alumno/Hijo)
    $familiar = null;
    if ($alAfil) {
        $familiar = Familiar::where('n_afiliado', $alAfil)->first();
    }
    if (!$familiar && $alDni) {
        $familiar = Familiar::where('dni', $alDni)->first();
    }

    $famData = [
        'empresa_id'          => $aposId,
        'titular_id'          => $titular->id,
        'nombre'              => $row['alumno_nombre'],
        'dni'                 => $alDni,
        'n_afiliado'          => $alAfil ?: ('1' . $titular->dni . '01'),
        'parentesco'          => 'Hijo/a',
        'tiene_patologia'     => 1,
        'diagnostico_id'      => $diagObj ? $diagObj->id : 1,
        'diagnostico_detalle' => $diagTexto,
        'escuela_id'          => $escuelaObj ? $escuelaObj->id : null,
        'docente_id'          => $docente ? $docente->id : null,
        'resolucion'          => $resolucionFormateada,
        'grado_division'      => $grado,
        'turno'               => $turno,
        'horario'             => $horario,
    ];

    if ($familiar) {
        $familiar->update($famData);
    } else {
        $familiar = Familiar::create($famData);
        $alumnosProcesados++;
    }

    // 4. Crear Expediente Alumno activo para conectar Docente + Hijo + Padre + Escuela + Resolución
    if ($docente && $familiar) {
        $estadoExp = ($expedientesCreados % 3 === 0) ? 'aprobado' : 'pendiente';
        ExpedienteAlumno::updateOrCreate(
            [
                'empresa_id' => $aposId,
                'alumno_id'  => $familiar->id,
                'docente_id' => $docente->id,
            ],
            [
                'titular_id'                => $titular->id,
                'escuela_id'                => $escuelaObj ? $escuelaObj->id : null,
                'origen_resolucion'         => 'resolucion_apos',
                'nro_resolucion'            => $resolucionFormateada ?: ('RES-' . rand(100, 999)),
                'diagnostico'               => $diagObj ? $diagObj->nombre : $diagTexto,
                'horarios_atencion'         => $turno . ' (' . $horario . ')',
                'horas_mensuales_asignadas' => 3, // 3 hs diarias tope reglamentario
                'estado_auditoria'          => $estadoExp,
            ]
        );
        $expedientesCreados++;
    }
}

// También agregamos al titular Mario Rojas en APOS para pruebas verbales inmediatas ("Mario Rojas")
$marioTitular = Titular::updateOrCreate(
    ['dni' => '22333444'],
    [
        'empresa_id' => $aposId,
        'nombre'     => 'Rojas Mario',
        'cuil'       => '20-22333444-9',
        'n_afiliado' => '12233344400',
        'resolucion' => 'RES-999',
    ]
);
$diagTea = Diagnostico::where('codigo', 'LIKE', '%F84.0%')->first();
$escNormal = Escuela::where('empresa_id', $aposId)->first();
$docPrimera = Docente::where('empresa_id', $aposId)->first();

Familiar::updateOrCreate(
    ['dni' => '55111222'],
    [
        'empresa_id'      => $aposId,
        'titular_id'      => $marioTitular->id,
        'nombre'          => 'Rojas Lucas (Hijo 01)',
        'n_afiliado'      => '12233344401',
        'parentesco'      => 'Hijo/a',
        'tiene_patologia'     => 1,
        'diagnostico_id'      => $diagTea ? $diagTea->id : 1,
        'diagnostico_detalle' => 'Trastorno del Espectro Autista (TEA) - Apoyo a la Integración Escolar',
        'escuela_id'          => $escNormal ? $escNormal->id : null,
        'docente_id'      => $docPrimera ? $docPrimera->id : null,
        'resolucion'      => 'RES-999',
        'grado_division'  => '3° Grado A',
        'turno'           => 'Turno Mañana',
        'horario'         => '08:00 a 11:00 hs',
    ]
);

Familiar::updateOrCreate(
    ['dni' => '55111333'],
    [
        'empresa_id'      => $aposId,
        'titular_id'      => $marioTitular->id,
        'nombre'          => 'Rojas Ana (Hija 02)',
        'n_afiliado'      => '12233344402',
        'parentesco'      => 'Hijo/a',
        'tiene_patologia'     => 0,
        'diagnostico_id'      => null,
        'diagnostico_detalle' => null,
        'escuela_id'          => $escNormal ? $escNormal->id : null,
        'grado_division'  => '1° Grado B',
        'turno'           => 'Turno Mañana',
        'horario'         => '08:00 a 11:00 hs',
    ]
);

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "\n=== RESUMEN FINAL DE POBLACIÓN APOS LA RIOJA ===\n";
echo "Empresas configuradas: " . Empresa::count() . " (1: Soluciones Digitales MCR | 2: APOS La Rioja)\n";
echo "Titulares en APOS: " . Titular::where('empresa_id', $aposId)->count() . "\n";
echo "Alumnos/Hijos (Familiares) en APOS: " . Familiar::where('empresa_id', $aposId)->count() . "\n";
echo "Docentes/Terapeutas en APOS: " . Docente::where('empresa_id', $aposId)->count() . "\n";
echo "Escuelas de La Rioja en APOS: " . Escuela::where('empresa_id', $aposId)->count() . "\n";
echo "Patologías / Diagnósticos CUD: " . Diagnostico::count() . "\n";
echo "Especialidades / Formaciones Docentes: " . Formacion::count() . "\n";
echo "Expedientes Vinculados Activos: " . ExpedienteAlumno::where('empresa_id', $aposId)->count() . "\n";
