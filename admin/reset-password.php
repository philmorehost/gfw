<?php
// admin/reset-password.php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (!$token) {
    redirect('/admin/login');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Invalid or expired token.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        if ($password === $confirm) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            $stmt->execute([$hashed, $user['id']]);
            $success = "Credentials updated. Proceed to login.";
        } else {
            $error = "Passwords do not match.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Credentials | <?php echo e($settings['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #05070a; color: #fff; }
        .font-condensed { font-family: 'Barlow Condensed', sans-serif; text-transform: uppercase; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-4">
    <div class="w-100" style="max-width: 450px;">
        <div class="bg-[#0a0e17] border border-white/5 rounded-3xl p-10 shadow-2xl">
            <div class="text-center mb-10">
                <h1 class="text-4xl font-condensed font-black text-white italic tracking-tighter mb-2">RESET ACCESS</h1>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">New Credentials Setup</p>
            </div>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <?php if ($error): ?>
                    <div class="p-4 bg-red-500/10 border border-red-500/20 text-red-500 text-xs font-bold rounded-xl"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="p-4 bg-green-500/10 border border-green-500/20 text-green-500 text-xs font-bold rounded-xl">
                        <?php echo $success; ?>
                        <div class="mt-2"><a href="/admin/login" class="underline">Login Now</a></div>
                    </div>
                <?php else: ?>
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 block">New Password</label>
                        <input type="password" name="password" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#ff3e3e]">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 block">Confirm Password</label>
                        <input type="password" name="confirm_password" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#ff3e3e]">
                    </div>

                    <button class="w-full bg-[#ff3e3e] text-white py-4 rounded-2xl font-black uppercase italic tracking-widest hover:bg-white hover:text-black transition-all shadow-xl">
                        Update Credentials
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
