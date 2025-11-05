<?php
/**
 * Admin Password Reset Tool
 * Use this to reset the admin password if you can't login
 * DELETE THIS FILE after use for security!
 */

require_once __DIR__ . '/../config/config.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($newPassword)) {
        $message = 'Password cannot be empty.';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Passwords do not match.';
    } elseif (strlen($newPassword) < 6) {
        $message = 'Password must be at least 6 characters.';
    } else {
        try {
            $db = getDB();
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update admin password
            $stmt = $db->prepare("UPDATE admins SET password_hash = ? WHERE username = 'admin'");
            $stmt->execute([$passwordHash]);
            
            if ($stmt->rowCount() > 0) {
                $success = true;
                $message = 'Password reset successfully! You can now login with your new password.';
            } else {
                // Admin doesn't exist, create it
                $stmt = $db->prepare("INSERT INTO admins (username, password_hash, email, full_name, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute(['admin', $passwordHash, 'admin@sukhniwas.com', 'Administrator', 1]);
                $success = true;
                $message = 'Admin account created successfully! You can now login.';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Admin Password</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
    <style>
        .warning-box {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            color: #92400e;
        }
        .warning-box strong {
            display: block;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box" style="max-width: 500px;">
            <div class="login-header">
                <h1>Reset Admin Password</h1>
                <p>Emergency Password Reset Tool</p>
            </div>
            
            <div class="warning-box">
                <strong>⚠️ Security Warning:</strong>
                Delete this file (reset-password.php) after use! This tool bypasses normal authentication.
            </div>
            
            <?php if ($message): ?>
                <div class="alert <?php echo $success ? 'alert-success' : 'alert-error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$success): ?>
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required minlength="6">
                    <small>Minimum 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
            </form>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem 0;">
                    <a href="login.php" class="btn btn-primary">Go to Login</a>
                </div>
            <?php endif; ?>
            
            <div class="login-footer">
                <p><a href="login.php">← Back to Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>

