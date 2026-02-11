<?php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }
// admin/comments.php

$GLOBALS['admin_page'] = 'comments';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {
        if (isset($_POST['update_status'])) {
            $id = $_POST['id'];
            $status = $_POST['status'];
            $stmt = $pdo->prepare("UPDATE comments SET status=? WHERE id=?");
            $stmt->execute([$status, $id]);
            redirect('/admin/comments');
        } elseif (isset($_POST['delete_comment'])) {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id=?");
            $stmt->execute([$id]);
            redirect('/admin/comments');
        }
    }
}

// Fetch Comments
$stmt = $pdo->query("SELECT c.*, p.title as post_title FROM comments c LEFT JOIN posts p ON c.post_id = p.id ORDER BY c.date DESC");
$comments = $stmt->fetchAll();

require_once __DIR__ . '/../includes/admin_layout.php';
$content_file = __DIR__ . '/comments_content.php';
render_admin_layout($content_file, $pdo, $settings, [
    'comments' => $comments
]);
