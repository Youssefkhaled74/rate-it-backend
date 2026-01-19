<?php

/**
 * Fix Existing Users with Double-Hashed Passwords
 * 
 * Run this in Laravel Tinker to fix users affected by the double-hashing bug:
 * 
 * php artisan tinker
 * 
 * Then paste and run this code:
 */

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Get all users
$users = User::all();

$fixed = 0;
$alreadyOk = 0;

foreach ($users as $user) {
    $hash = $user->password;
    
    // Check if hash looks corrupted (e.g., starts with $2y$ but has unusual length or pattern)
    // Normal bcrypt is 60 chars
    if (!$hash || strlen($hash) !== 60 || !str_starts_with($hash, '$2y$')) {
        echo "User #{$user->id} ({$user->phone}): Skipping - no valid hash or unusual format\n";
        continue;
    }
    
    // Prompt for the REAL plain password for this user
    // In production, you'd need to reset via email/SMS OTP or have users re-register
    // For testing/dev with known password:
    
    // Example: If you know all test users have "Password123!" as password:
    $knownPassword = 'Password123!';
    
    // Try to verify with known password
    if (Hash::check($knownPassword, $hash)) {
        echo "User #{$user->id} ({$user->phone}): Already correct\n";
        $alreadyOk++;
        continue;
    }
    
    // If check fails, the hash is likely double-hashed
    // Solution: Reset to known password using the 'hashed' cast
    echo "User #{$user->id} ({$user->phone}): Fixing double-hash by resetting password\n";
    
    $user->password = $knownPassword; // Let cast handle proper hashing
    $user->save();
    
    // Verify fix
    $user->refresh();
    if (Hash::check($knownPassword, $user->password)) {
        echo "  ✓ Fixed and verified\n";
        $fixed++;
    } else {
        echo "  ✗ Fix failed - manual intervention needed\n";
    }
}

echo "\nSummary:\n";
echo "Already OK: {$alreadyOk}\n";
echo "Fixed: {$fixed}\n";
echo "Total processed: " . ($alreadyOk + $fixed) . "\n";

/**
 * Alternative: Force ALL users to reset password via forgot-password flow
 * This is safer for production:
 * 
 * 1. Notify all users via SMS/email that passwords need to be reset
 * 2. They use the forgot-password flow which properly sets password via cast
 * 3. Old double-hashed passwords get replaced with correct hashes
 */
