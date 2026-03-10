<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['formaciones', 'formacions', 'docentes'];

foreach ($tables as $table) {
    echo "=== STRUCTURE DE $table ===\n";
    try {
        $result = DB::select("SHOW CREATE TABLE $table");
        $key = 'Create Table';
        echo $result[0]->$key . "\n\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
echo "--- FIN ---\n";
