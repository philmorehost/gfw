<?php
// pages/unsubscribe.php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }

$email = $_GET['email'] ?? '';
$hash = $_GET['hash'] ?? '';
$success = false;
$error = '';

if ($email && $hash) {
    // Simple hash verification: md5(email + site_name)
    $expected_hash = md5($email . $settings['name']);

    if ($hash === $expected_hash) {
        if ($pdo) {
            $stmt = $pdo->prepare("DELETE FROM subscribers WHERE email = ?");
            $stmt->execute([$email]);
            $success = true;
        } else {
            $error = "System offline. Please try again later.";
        }
    } else {
        $error = "Invalid unsubscription link.";
    }
} else {
    $error = "Missing required parameters.";
}

$page_title = "Unsubscribe | " . $settings['name'];
include __DIR__ . '/../includes/header.php';
?>

<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="bg-dark p-5 rounded-3 border border-white border-opacity-10 shadow-2xl">
                <h1 class="font-condensed fw-black italic text-white mb-4">UNSUBSCRIBE</h1>

                <?php if ($success): ?>
                    <div class="alert alert-success bg-green-600/20 text-green-400 border-0 rounded-0 fw-bold text-uppercase italic">
                        You have been successfully removed from our scouting list.
                    </div>
                    <p class="text-white-50 mt-4">We're sorry to see you go. You can rejoin anytime from our homepage.</p>
                    <a href="/" class="btn btn-danger rounded-0 fw-black italic text-uppercase mt-4 px-5 py-3">RETURN TO HOME</a>
                <?php else: ?>
                    <div class="alert alert-danger bg-red-600/20 text-red-400 border-0 rounded-0 fw-bold text-uppercase italic">
                        <?php echo e($error); ?>
                    </div>
                    <p class="text-white-50 mt-4">If you're having trouble, please contact support.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
