<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Titular;
use App\Models\Familiar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear usuario básico para que no falle el login (Solo campos esenciales)
        User::create([
            'name' => 'Mario Rojas',
            'email' => 'mario@admin.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Crear Titular: Mario Rojas (El código será 12233344402)
        $t1 = Titular::create([
            'nombre' => 'Mario Rojas',
            'dni' => '22333444',
            'cuil' => '20-22333444-9'
        ]);

        // 3. Crear Familiares vinculados (Grupo Familiar)
        Familiar::create([
            'titular_id' => $t1->id,
            'nombre' => 'Lucas Rojas',
            'dni' => '55111222',
            'parentesco' => 'Hijo',
            'tiene_patologia' => true,
            'diagnostico' => 'Diabetes Tipo 1'
        ]);

        Familiar::create([
            'titular_id' => $t1->id,
            'nombre' => 'Ana Rojas',
            'dni' => '55111333',
            'parentesco' => 'Hijo',
            'tiene_patologia' => false
        ]);

        // 4. Crear otro Titular: Elena (El código será 13055566601)
        $t2 = Titular::create([
            'nombre' => 'Elena Gomez',
            'dni' => '30555666',
            'cuil' => '27-30555666-4'
        ]);

        Familiar::create([
            'titular_id' => $t2->id,
            'nombre' => 'Hijo de Elena',
            'dni' => '60000001',
            'parentesco' => 'Hijo',
            'tiene_patologia' => false
        ]);
    }
}
