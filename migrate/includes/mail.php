<?php
// migrate/includes/mail.php

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
 * Send email using settings from DB
 */
function send_email($pdo, $to, $subject, $message) {
    $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
    $settings = $stmt->fetch();

    if (!$settings) return false;

    $sender_email = $settings['smtp_sender'] ?: $settings['admin_email'];
    $site_name = $settings['name'];

    // If SMTP host is configured, use SMTP
    if (!empty($settings['smtp_host'])) {
        $smtp = new SimpleSMTP(
            $settings['smtp_host'],
            $settings['smtp_port'],
            $settings['smtp_user'],
            $settings['smtp_pass'],
            $settings['smtp_encryption']
        );
        return $smtp->send($sender_email, $to, $subject, $message, $site_name);
    }

    // Fallback to mail()
    $headers = "From: $site_name <$sender_email>\r\n";
    $headers .= "Reply-To: $sender_email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    return mail($to, $subject, $message, $headers);
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
    $stmt = $pdo->query("SELECT email FROM subscribers");
    $subscribers = $stmt->fetchAll();

    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $full_post_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $post_url;

    $subject = "New Post: $post_title";
    $message = "A new post has been published: <a href='$full_post_url'>$post_title</a>";

    foreach ($subscribers as $sub) {
        send_email($pdo, $sub['email'], $subject, $message);
    }
}
