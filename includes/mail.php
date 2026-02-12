<?php
// includes/mail.php

/**
 * Simple SMTP Client Implementation
 */
class SimpleSMTP {
    private $host, $port, $user, $pass, $encryption, $timeout;
    private $connection, $logs = [];

    public function __construct($host, $port, $user, $pass, $encryption = 'tls', $timeout = 10) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->encryption = strtolower($encryption);
        $this->timeout = $timeout;
    }

    private function connect() {
        $remote = ($this->encryption === 'ssl' ? 'ssl://' : '') . $this->host;
        $this->connection = fsockopen($remote, $this->port, $errno, $errstr, $this->timeout);
        if (!$this->connection) return false;
        $this->getResponse();

        $this->sendCommand("EHLO " . $_SERVER['HTTP_HOST']);

        if ($this->encryption === 'tls') {
            $this->sendCommand("STARTTLS");
            stream_socket_enable_crypto($this->connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $this->sendCommand("EHLO " . $_SERVER['HTTP_HOST']);
        }

        if ($this->user && $this->pass) {
            $this->sendCommand("AUTH LOGIN");
            $this->sendCommand(base64_encode($this->user));
            $this->sendCommand(base64_encode($this->pass));
        }
        return true;
    }

    private function sendCommand($command) {
        fputs($this->connection, $command . "\r\n");
        return $this->getResponse();
    }

    private function getResponse() {
        $response = "";
        while ($line = fgets($this->connection, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == " ") break;
        }
        return $response;
    }

    public function send($from, $to, $subject, $body, $site_name) {
        if (!$this->connect()) return false;

        $this->sendCommand("MAIL FROM: <$from>");
        $this->sendCommand("RCPT TO: <$to>");
        $this->sendCommand("DATA");

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: $site_name <$from>\r\n";
        $headers .= "To: <$to>\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        $headers .= "\r\n";

        $this->sendCommand($headers . $body . "\r\n.");
        $this->sendCommand("QUIT");
        fclose($this->connection);
        return true;
    }
}

/**
 * Modern HTML Template wrapper
 */
function wrap_email_template($content, $settings, $to_email = null) {
    $site_name = $settings['name'] ?? 'GFW';
    $primary_color = '#ff3e3e';
    $bg_color = '#05070a';

    $unsubscribe_link = '';
    if ($to_email) {
        $hash = md5($to_email . $site_name);
        $base_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $unsubscribe_link = $base_url . "/unsubscribe?email=" . urlencode($to_email) . "&hash=" . $hash;
    }

    return "
    <html>
    <body style='margin: 0; padding: 0; font-family: sans-serif; background-color: $bg_color; color: #ffffff;'>
        <table width='100%' cellpadding='0' cellspacing='0' style='background-color: $bg_color; padding: 40px 20px;'>
            <tr>
                <td align='center'>
                    <table width='600' cellpadding='0' cellspacing='0' style='background-color: #0a0e17; border: 1px solid rgba(255,255,255,0.1);'>
                        <tr>
                            <td style='padding: 40px; text-align: center; border-bottom: 4px solid $primary_color;'>
                                <h1 style='color: #ffffff; margin: 0; text-transform: uppercase; letter-spacing: 2px; font-weight: 800;'>$site_name</h1>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding: 40px; line-height: 1.6; color: #d1d5db; font-size: 16px;'>
                                $content
                            </td>
                        </tr>
                        <tr>
                            <td style='padding: 40px; text-align: center; border-top: 1px solid rgba(255,255,255,0.05); background-color: #05070a;'>
                                <p style='color: #6b7280; font-size: 12px; margin: 0;'>&copy; " . date('Y') . " $site_name. All rights reserved.</p>
                                " . ($unsubscribe_link ? "<p style='margin-top: 10px;'><a href='$unsubscribe_link' style='color: #ff3e3e; text-decoration: none; font-size: 11px; font-weight: bold; text-transform: uppercase;'>Unsubscribe from scouting list</a></p>" : "") . "
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>";
}

/**
 * Send email using settings from DB
 */
function send_email($pdo, $to, $subject, $message, $is_subscriber = false) {
    $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
    $settings = $stmt->fetch();

    if (!$settings) return false;

    $sender_email = $settings['smtp_sender'] ?: $settings['admin_email'];
    $site_name = $settings['name'];

    // Wrap message in modern template
    $html_body = wrap_email_template($message, $settings, $is_subscriber ? $to : null);

    // If SMTP host is configured, use SMTP
    if (!empty($settings['smtp_host'])) {
        $smtp = new SimpleSMTP(
            $settings['smtp_host'],
            $settings['smtp_port'],
            $settings['smtp_user'],
            $settings['smtp_pass'],
            $settings['smtp_encryption']
        );
        return $smtp->send($sender_email, $to, $subject, $html_body, $site_name);
    }

    // Fallback to mail()
    $headers = "From: $site_name <$sender_email>\r\n";
    $headers .= "Reply-To: $sender_email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    return mail($to, $subject, $html_body, $headers);
}

/**
 * Send Admin Login Notification
 */
function notify_admin_login($pdo, $admin_email) {
    $subject = "Admin Login Notification";
    $message = "A login to the admin panel was detected at " . date('Y-m-d H:i:s');
    return send_email($pdo, $admin_email, $subject, $message);
}

/**
 * Send New Post Notification to subscribers
 */
function notify_subscribers_new_post($pdo, $post_title, $post_url) {
    $stmt = $pdo->query("SELECT email, favorite_team_name FROM subscribers");
    $subscribers = $stmt->fetchAll();

    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $full_post_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $post_url;

    foreach ($subscribers as $sub) {
        $subject = "Scouting Alert: $post_title";
        $team_prefix = $sub['favorite_team_name'] ? "[{$sub['favorite_team_name']}] " : "";
        $msg_subject = $team_prefix . $subject;

        $message = "
            <h2 style='color: #ffffff; font-style: italic;'>TACTICAL ALERT</h2>
            <p>A new scouting report has been filed and is ready for your review.</p>
            <div style='background-color: #111827; padding: 20px; border-left: 4px solid #ff3e3e; margin: 20px 0;'>
                <h3 style='margin-top: 0; color: #ffffff;'>$post_title</h3>
                <a href='$full_post_url' style='display: inline-block; background-color: #ff3e3e; color: #ffffff; padding: 12px 24px; text-decoration: none; font-weight: bold; text-transform: uppercase; font-size: 14px; margin-top: 10px;'>Read Full Report</a>
            </div>
        ";

        send_email($pdo, $sub['email'], $msg_subject, $message, true);
    }
}
