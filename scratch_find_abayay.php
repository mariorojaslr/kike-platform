<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== BUSCANDO ABAYAY EN TITULARS ===\n";
$res = DB::table('titulars')->where('nombre', 'LIKE', '%Abayay%')->orWhere('dni', 'LIKE', '%Abayay%')->get();
print_r($res);

echo "\n=== PRIMEROS 10 TITULARES EN DB ===\n";
$all = DB::table('titulars')->take(10)->get();
foreach($all as $t) {
    echo "ID: {$t->id} | Nombre: {$t->nombre} | DNI: {$t->dni} | n_afiliado: " . ($t->n_afiliado ?? 'N/A') . " | EmpresaID: " . ($t->empresa_id ?? 'N/A') . "\n";
}
