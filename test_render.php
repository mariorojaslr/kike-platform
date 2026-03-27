<?php
$titulares = App\Models\Titular::paginate(10);
$search = 'A';
try {
    echo view('dashboards.tenant.partials.titulares_table_rows', compact('titulares', 'search'))->render();
    echo "\nOK\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
