<?php
// Script to test fgetcsv parsing directly to see if it Infinite Loops
$path = __DIR__ . '/../plantilla_vacia_resumen A.csv';
if (!file_exists($path)) {
    die("File not found");
}
$file = fopen($path, "r");
$count = 0;
while (($row = fgetcsv($file, 2000, ";")) !== FALSE) {
    if (count($row) < 5 || empty(trim($row[0]))) {
        continue;
    }
    $titularNombre = trim($row[0] ?? '');
    if (str_contains(strtolower($titularNombre), 'sep=') || str_contains(strtolower($titularNombre), 'titular_apellido')) {
        continue;
    }
    $count++;
    if ($count > 10000) {
        die("INFINITE LOOP DETECTED!\n");
    }
}
fclose($file);
echo "SUCCESS. Lines processed: $count\n";
