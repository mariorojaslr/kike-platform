<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. LIMPIEZA DE SEGURIDAD
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tablas = [
            'users', 'docentes', 'formacions', 'escuelas',
            'titulars', 'beneficiarios', 'diagnosticos',
            'familiares', 'empresas'
        ];

        foreach ($tablas as $tabla) {
            if (Schema::hasTable($tabla)) {
                DB::table($tabla)->truncate();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. EMPRESA DE PRUEBA
        $empresaId = 1;
        if (Schema::hasTable('empresas')) {
            $empresaId = DB::table('empresas')->insertGetId([
                'nombre' => 'Empresa de Prueba (Demo)',
                'cuit'   => '30-12345678-9',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. DUEÑO DEL SISTEMA (MARIO ROJAS)
        DB::table('users')->insert([
            'name'              => 'Mario Rojas',
            'email'             => 'mario.rojas.coach@gmail.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('Rojas*250007'),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // 4. TABLA MAESTRA: FORMACIONES (Para los Docentes)
        // Agregamos más opciones profesionales para el cliente
        $formaciones = [
            'Psicopedagoga',
            'Maestra Integradora',
            'Terapista Ocupacional',
            'Fonoaudióloga',
            'Psicóloga Infantil',
            'Acompañante Terapéutico',
            'Kinesióloga Neuromotora'
        ];

        $formacionIds = [];
        foreach ($formaciones as $nombre) {
            $formacionIds[] = DB::table('formacions')->insertGetId([
                'nombre' => $nombre,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. TABLA MAESTRA: DIAGNÓSTICOS (Para los Beneficiarios/Familiares)
        $diagnosticos = [
            'Trastorno del Espectro Autista (TEA)',
            'TDAH (Déficit de Atención e Hiperactividad)',
            'Síndrome de Down',
            'Retraso Madurativo Global',
            'Parálisis Cerebral',
            'Trastorno Específico del Lenguaje (TEL)',
            'Discapacidad Intelectual'
        ];

        $diagnosticoIds = [];
        foreach ($diagnosticos as $nombre) {
            $diagnosticoIds[] = DB::table('diagnosticos')->insertGetId([
                'nombre' => $nombre,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 6. ESCUELA DE EJEMPLO
        $escuelaId = DB::table('escuelas')->insertGetId([
            'nombre'     => 'Escuela Primaria N1 - San Martín',
            'cue'        => '30001234',
            'direccion'  => 'Av. Rivadavia 450',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 7. CARGA DE 20 MAESTRAS (Relacionadas con Formaciones reales)
        $nombres = ['Ana', 'Maria', 'Carla', 'Lucia', 'Jimena', 'Florencia', 'Gabriela', 'Paola', 'Natalia', 'Elena'];
        $apellidos = ['Gomez', 'Lopez', 'Rodriguez', 'Sosa', 'Perez', 'Garcia', 'Martinez', 'Torres', 'Diaz', 'Romero'];

        for ($i = 0; $i < 20; $i++) {
            DB::table('docentes')->insert([
                'nombre'             => $nombres[array_rand($nombres)] . ' ' . $apellidos[array_rand($apellidos)],
                'dni'                => rand(25000000, 45000000),
                'formacion_id'       => $formacionIds[array_rand($formacionIds)],
                'validado_auditoria' => (rand(0, 1) == 1),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }

        // 8. CARGA DE DATOS REALES (Isaac Adriel)
        $titularId = DB::table('titulars')->insertGetId([
            'nombre'     => 'REINOZO ALEJANDRO',
            'dni'        => '15888999',
            'n_afiliado' => '10-444-5',
            'resolucion' => 'RES-2024-05',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('beneficiarios')->insert([
            'titular_id'  => $titularId,
            'nombre'      => 'REZINOVSKY ISAAC ADRIEL',
            'dni'         => '50111222',
            'escuela_id'  => $escuelaId,
            'diagnostico' => 'Trastorno del Espectro Autista (TEA)',
            // Aquí si ya tienes la relación por ID en beneficiarios usa:
            // 'diagnostico_id' => $diagnosticoIds[0],
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}
