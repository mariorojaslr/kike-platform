<?php
$user = \App\Models\User::where('empresa_id', 2)->where('role', 'tenant')->first();
if ($user) {
    echo "Found Tenant User for Empresa 2:\n";
    echo "Email: " . $user->email . "\n";
    $user->password = \Illuminate\Support\Facades\Hash::make('12345678');
    $user->save();
    echo "Password reset to: 12345678\n";
} else {
    echo "No tenant user found for Empresa 2.\n";
}
