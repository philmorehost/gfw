<?php
// admin/forgot-password.php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $email = $_POST['email'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $stmt->execute([$token, $expires, $user['id']]);

            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $reset_link = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/admin/reset-password?token=' . $token;

            $subject = "Access Credential Reset | " . $settings['name'];
            $message = "You requested a password reset. Click the link below to reset it: <br><br> <a href='$reset_link'>$reset_link</a> <br><br> This link expires in 1 hour.";

            if (send_email($pdo, $email, $subject, $message)) {
                $success = "Tactical reset instructions dispatched to your inbox.";
            } else {
                $error = "Communication failure. Check SMTP configuration.";
            }
        } else {
            // Don't reveal if email exists, but for admin panel it's usually okay.
            $error = "Email not found in intelligence database.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Access | <?php echo e($settings['name']); ?></title>
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
                <h1 class="text-4xl font-condensed font-black text-white italic tracking-tighter mb-2">LOST ACCESS</h1>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Recovery Protocol</p>
            </div>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <?php if ($error): ?>
                    <div class="p-4 bg-red-500/10 border border-red-500/20 text-red-500 text-xs font-bold rounded-xl"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="p-4 bg-green-500/10 border border-green-500/20 text-green-500 text-xs font-bold rounded-xl"><?php echo $success; ?></div>
                <?php endif; ?>

                <div>
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 block">Admin Email</label>
                    <input type="email" name="email" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#ff3e3e]">
                </div>

                <button class="w-full bg-[#ff3e3e] text-white py-4 rounded-2xl font-black uppercase italic tracking-widest hover:bg-white hover:text-black transition-all shadow-xl">
                    Dispatch Token
                </button>

                <div class="text-center mt-6">
                    <a href="/admin/login" class="text-[10px] font-black text-gray-500 hover:text-white uppercase tracking-widest text-decoration-none">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
