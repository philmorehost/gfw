<?php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }
// migrate/admin/profile.php

$GLOBALS['admin_page'] = 'profile';

// Fetch current user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {
        if (isset($_POST['update_profile'])) {
            $email = $_POST['email'];
            $new_password = $_POST['new_password'];

            if ($new_password) {
                $hashed = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE users SET email=?, password=? WHERE id=?");
                $stmt->execute([$email, $hashed, $user['id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET email=? WHERE id=?");
                $stmt->execute([$email, $user['id']]);
            }

            $success = "Profile updated successfully!";
            // Refresh user
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $user = $stmt->fetch();
        }
    }
}

require_once __DIR__ . '/../includes/admin_layout.php';
$content_file = __DIR__ . '/profile_content.php';
render_admin_layout($content_file, $pdo, $settings, ['user' => $user, 'success' => $success ?? '']);
