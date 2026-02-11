<?php
// install/index.php

// Redirect if already installed
if (file_exists(__DIR__ . '/../config.php')) {
    header("Location: /");
    exit;
}

$stage = isset($_GET['stage']) ? (int)$_GET['stage'] : 1;
if ($stage < 1 || $stage > 4) $stage = 1;

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GFW Deployment Console</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Barlow+Condensed:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root { --pitch-dark: #05070a; --electric-red: #ff3e3e; }
        body { font-family: 'Inter', sans-serif; background-color: var(--pitch-dark); color: #fff; }
        h1, h2, h3, h4, h5, h6, .font-condensed { font-family: 'Barlow Condensed', sans-serif; text-transform: uppercase; }
        .text-electric-red { color: var(--electric-red); }
        .bg-electric-red { background-color: var(--electric-red); }
        .ls-widest { letter-spacing: 0.2em; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container" style="max-width: 600px;">
        <div class="text-center mb-5">
            <h1 class="display-4 font-condensed fw-black italic text-white mb-0">
                <span class="text-electric-red">GFW</span> DEPLOYMENT
            </h1>
            <p class="text-muted fw-bold text-uppercase ls-widest" style="font-size: 10px;">Tactical System Installer v1.0</p>
        </div>

        <div class="row mb-5 g-2">
            <?php for ($i=1; $i<=4; $i++): ?>
                <div class="col">
                    <div class="h-1 <?php echo $i <= $stage ? 'bg-electric-red' : 'bg-white opacity-10'; ?>"></div>
                    <p class="text-center mt-2 font-condensed fw-bold <?php echo $i === $stage ? 'text-white' : 'text-muted'; ?>" style="font-size: 10px;">STAGE <?php echo $i; ?></p>
                </div>
            <?php endfor; ?>
        </div>

        <?php include "stage{$stage}.php"; ?>

        <div class="text-center mt-5">
            <p class="text-muted text-uppercase fw-bold" style="font-size: 9px;">© <?php echo date('Y'); ?> Global Football Watch | Systems Division</p>
        </div>
    </div>
</body>
</html>
