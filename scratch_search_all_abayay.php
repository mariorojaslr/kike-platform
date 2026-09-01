<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== BUSCANDO 'Abayay' EN TODA LA BASE DE DATOS ===\n";
$tables = DB::select('SHOW TABLES');
foreach ($tables as $tObj) {
    $t = current((array)$tObj);
    $cols = Schema::getColumnListing($t);
    $q = DB::table($t);
    $q->where(function($query) use ($cols) {
        foreach ($cols as $c) {
            $query->orWhere($c, 'LIKE', '%Abayay%');
        }
    });
    $res = $q->get();
    if ($res->count() > 0) {
        echo "=== Tabla: $t (" . $res->count() . " coincidencias) ===\n";
        print_r($res);
    }
}
