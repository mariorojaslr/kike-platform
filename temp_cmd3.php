<?php
$user = \App\Models\User::where('empresa_id', 2)->whereIn('role', ['empresa', 'tenant'])->first();
if ($user) {
    echo "Email: " . $user->email . "\n";
    $user->password = \Illuminate\Support\Facades\Hash::make('12345678');
    $user->save();
    echo "Password set to 12345678\n";
} else {
    echo "No tenant user found for Empresa 2.\n";
    $empresa = \App\Models\Empresa::find(2);
    if($empresa) {
         $user = \App\Models\User::create([
             'name' => 'Admin ' . $empresa->nombre,
             'email' => 'director@prueba2.com',
             'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
             'role' => 'empresa',
             'empresa_id' => 2
         ]);
         echo "Created user: director@prueba2.com\n";
    }
}
