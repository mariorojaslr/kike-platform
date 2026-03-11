<?php
$empresa = \App\Models\Empresa::find(2);
if ($empresa) {
    $user = \App\Models\User::firstOrCreate(
        ['email' => 'director@' . strtolower(str_replace(' ', '', $empresa->nombre)) . '.com'],
        [
            'name' => 'Director ' . $empresa->nombre,
            'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
            'role' => 'tenant',
            'empresa_id' => $empresa->id
        ]
    );
    echo "User created/found!\n";
    echo "Email: " . $user->email . "\n";
    echo "Password: 12345678\n";
} else {
    echo "Empresa 2 not found.\n";
}
