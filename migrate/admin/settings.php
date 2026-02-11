<?php
// migrate/admin/settings.php

$GLOBALS['admin_page'] = 'settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {
        $name = $_POST['name'];
        $tagline = $_POST['tagline'];
        $logo = $_POST['logo'];
        $whatsapp = $_POST['whatsapp_number'];
        $admin_email = $_POST['admin_email'];
        $smtp_sender = $_POST['smtp_sender'];
        $smtp_host = $_POST['smtp_host'];
        $smtp_port = $_POST['smtp_port'];
        $smtp_user = $_POST['smtp_user'];
        $smtp_pass = $_POST['smtp_pass'];
        $smtp_encryption = $_POST['smtp_encryption'];
        $fb = $_POST['fb_url'];
        $tw = $_POST['tw_url'];
        $ig = $_POST['ig_url'];
        $yt = $_POST['yt_url'];

        $stmt = $pdo->prepare("UPDATE settings SET name=?, tagline=?, logo=?, whatsapp_number=?, admin_email=?, smtp_sender=?, smtp_host=?, smtp_port=?, smtp_user=?, smtp_pass=?, smtp_encryption=?, fb_url=?, tw_url=?, ig_url=?, yt_url=? WHERE id=1");
        $stmt->execute([$name, $tagline, $logo, $whatsapp, $admin_email, $smtp_sender, $smtp_host, $smtp_port, $smtp_user, $smtp_pass, $smtp_encryption, $fb, $tw, $ig, $yt]);

        $success = "Settings updated successfully!";
        // Refresh settings
        $settings = get_site_settings($pdo);
    }
}

require_once __DIR__ . '/../includes/admin_layout.php';
$content_file = __DIR__ . '/settings_content.php';
render_admin_layout($content_file, $pdo, $settings, ['success' => $success ?? '']);
