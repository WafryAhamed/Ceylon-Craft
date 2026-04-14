<?php

require 'bootstrap/app.php';

$app = require_once 'bootstrap/providers.php';

$user = \App\Models\User::first();

echo "=== Sanctum Token Test ===\n";
echo "User: " . ($user ? $user->name : 'None found') . "\n";

if ($user) {
    echo "Has createToken method: " . (method_exists($user, 'createToken') ? 'Yes' : 'No') . "\n";
    
    // Try creating a token
    try {
        $token = $user->createToken('test-token')->plainTextToken;
        echo "Token created successfully\n";
        echo "Token preview: " . substr($token, 0, 20) . '...' . "\n";
    } catch (\Exception $e) {
        echo "Token creation failed: " . $e->getMessage() . "\n";
    }
}
