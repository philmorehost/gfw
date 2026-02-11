<?php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }
// admin/login.php

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (login_admin($pdo, $email, $password)) {
            // Notify admin of login
            notify_admin_login($pdo, $email);
            redirect('/admin/posts');
        } else {
            $error = 'Invalid credentials.';
        }
    } else {
        $error = 'Invalid session token. Please try again.';
    }
}

if (is_admin_logged_in()) {
    redirect('/admin/posts');
}

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?php echo e($settings['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Barlow+Condensed:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root { --pitch-dark: #05070a; --electric-red: #ff3e3e; }
        body { font-family: 'Inter', sans-serif; background-color: var(--pitch-dark); color: #fff; }
        h1, h2, h3, h4, h5, h6, .font-condensed { font-family: 'Barlow Condensed', sans-serif; text-transform: uppercase; }
        .text-electric-red { color: var(--electric-red); }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-4">
    <div class="w-100" style="max-width: 450px;">
        <div class="bg-[#0a0e17] border border-white/5 rounded-3xl p-10 shadow-2xl">
            <div class="text-center mb-10">
                <h1 class="text-4xl font-condensed font-black text-white italic tracking-tighter mb-2">GFW ADMIN</h1>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Secure Console Access</p>
            </div>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <?php if ($error): ?>
                    <div class="p-4 bg-red-500/10 border border-red-500/20 text-red-500 text-xs font-bold rounded-xl"><?php echo $error; ?></div>
                <?php endif; ?>

                <div>
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 block">Email Address</label>
                    <input
                        type="email"
                        name="email"
                        required
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#ff3e3e]"
                    >
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 block">Password</label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:border-[#ff3e3e]"
                    >
                </div>

                <button class="w-full bg-[#ff3e3e] text-white py-4 rounded-2xl font-black uppercase italic tracking-widest hover:bg-white hover:text-black transition-all shadow-xl">
                    Authenticate
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="/admin/forgot-password" class="text-[10px] font-black text-gray-500 hover:text-white uppercase tracking-widest text-decoration-none">Lost Access Credentials?</a>
            </div>
        </div>
    </div>
</body>
</html>
