<?php
/**
 * Debug Login Issues
 * This file helps diagnose login problems
 * DELETE THIS FILE after fixing the issue!
 */

require_once __DIR__ . '/../config/config.php';

echo "<h2>Login Debug Information</h2>";
echo "<style>body{font-family:monospace;padding:20px;background:#f5f5f5;} .section{background:white;padding:15px;margin:10px 0;border-left:4px solid #2563eb;} .error{color:red;} .success{color:green;} pre{background:#f0f0f0;padding:10px;overflow:auto;}</style>";

// 1. Check Database Connection
echo "<div class='section'>";
echo "<h3>1. Database Connection</h3>";
try {
    $db = getDB();
    echo "<p class='success'>✓ Database connection successful</p>";
    
    // Check if admins table exists
    $tables = $db->query("SHOW TABLES LIKE 'admins'")->fetchAll();
    if (empty($tables)) {
        echo "<p class='error'>✗ 'admins' table does not exist!</p>";
    } else {
        echo "<p class='success'>✓ 'admins' table exists</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// 2. Check Admin User
echo "<div class='section'>";
echo "<h3>2. Admin User Check</h3>";
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM admins WHERE username = 'admin'");
    $admin = $stmt->fetch();
    
    if (!$admin) {
        echo "<p class='error'>✗ Admin user 'admin' not found!</p>";
        echo "<p>Run this SQL to create admin:</p>";
        echo "<pre>INSERT INTO admins (username, password_hash, email, full_name, is_active) VALUES ('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'admin@sukhniwas.com', 'Administrator', 1);</pre>";
    } else {
        echo "<p class='success'>✓ Admin user found</p>";
        echo "<pre>";
        echo "ID: " . htmlspecialchars($admin['id']) . "\n";
        echo "Username: " . htmlspecialchars($admin['username']) . "\n";
        echo "Email: " . htmlspecialchars($admin['email']) . "\n";
        echo "Full Name: " . htmlspecialchars($admin['full_name']) . "\n";
        echo "Is Active: " . ($admin['is_active'] ? 'Yes' : 'No') . "\n";
        echo "Password Hash: " . substr($admin['password_hash'], 0, 30) . "...\n";
        echo "</pre>";
        
        if (!$admin['is_active']) {
            echo "<p class='error'>✗ Admin user is NOT active! (is_active = 0)</p>";
            echo "<p>Run this SQL to activate:</p>";
            echo "<pre>UPDATE admins SET is_active = 1 WHERE username = 'admin';</pre>";
        } else {
            echo "<p class='success'>✓ Admin user is active</p>";
        }
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error checking admin: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// 3. Test Password Verification
echo "<div class='section'>";
echo "<h3>3. Password Verification Test</h3>";
try {
    $db = getDB();
    $stmt = $db->query("SELECT password_hash FROM admins WHERE username = 'admin'");
    $admin = $stmt->fetch();
    
    if ($admin) {
        $hash = $admin['password_hash'];
        $testPassword = 'admin123';
        
        echo "<p>Testing password: <strong>admin123</strong></p>";
        echo "<p>Hash: <code>" . htmlspecialchars(substr($hash, 0, 50)) . "...</code></p>";
        
        if (password_verify($testPassword, $hash)) {
            echo "<p class='success'>✓ Password verification SUCCESSFUL!</p>";
        } else {
            echo "<p class='error'>✗ Password verification FAILED!</p>";
            echo "<p>The hash in database doesn't match 'admin123'</p>";
            echo "<p>Run this SQL to fix:</p>";
            echo "<pre>UPDATE admins SET password_hash = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy' WHERE username = 'admin';</pre>";
        }
    } else {
        echo "<p class='error'>✗ Cannot test - admin user not found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// 4. Session Check
echo "<div class='section'>";
echo "<h3>4. Session Configuration</h3>";
echo "<pre>";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not Active') . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Save Path: " . session_save_path() . "\n";
if (isset($_SESSION)) {
    echo "Session Data: " . print_r($_SESSION, true) . "\n";
} else {
    echo "No session data\n";
}
echo "</pre>";
echo "</div>";

// 5. Test Login Process
echo "<div class='section'>";
echo "<h3>5. Simulate Login Process</h3>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "<p>Testing login with:</p>";
    echo "<pre>Username: $username\nPassword: [hidden]</pre>";
    
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin) {
            echo "<p class='success'>✓ Admin found in database</p>";
            
            if (password_verify($password, $admin['password_hash'])) {
                echo "<p class='success'>✓ Password matches!</p>";
                echo "<p class='success'>✓ Login should work!</p>";
                
                // Try to set session
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['full_name'];
                
                echo "<p class='success'>✓ Session variables set</p>";
                echo "<p><a href='index.php'>Try accessing dashboard now</a></p>";
            } else {
                echo "<p class='error'>✗ Password does NOT match!</p>";
            }
        } else {
            echo "<p class='error'>✗ Admin not found or not active!</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<form method='POST'>";
    echo "<p>Username: <input type='text' name='username' value='admin'></p>";
    echo "<p>Password: <input type='password' name='password' value='admin123'></p>";
    echo "<p><button type='submit' name='test_login'>Test Login</button></p>";
    echo "</form>";
}
echo "</div>";

// 6. Quick Fix SQL
echo "<div class='section'>";
echo "<h3>6. Quick Fix SQL</h3>";
echo "<p>Copy and run this SQL in phpMyAdmin to completely reset admin:</p>";
echo "<pre>";
echo "DELETE FROM admins WHERE username = 'admin';\n\n";
echo "INSERT INTO admins (username, password_hash, email, full_name, is_active) VALUES\n";
echo "('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'admin@sukhniwas.com', 'Administrator', 1);\n\n";
echo "-- Password: admin123";
echo "</pre>";
echo "</div>";

echo "<hr>";
echo "<p><a href='login.php'>← Back to Login</a></p>";
?>

