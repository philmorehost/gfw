<?php
// admin/pages.php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }
$GLOBALS['admin_page'] = 'pages';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {
        if (isset($_POST['save_page'])) {
            $id = $_POST['id'];
            $title = $_POST['title'];
            $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
            $content = $_POST['content'];

            if ($id) {
                $stmt = $pdo->prepare("UPDATE pages SET title=?, slug=?, content=? WHERE id=?");
                $stmt->execute([$title, $slug, $content, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO pages (title, slug, content) VALUES (?, ?, ?)");
                $stmt->execute([$title, $slug, $content]);
            }
            redirect('/admin/pages');
        } elseif (isset($_POST['delete_page'])) {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM pages WHERE id=?");
            $stmt->execute([$id]);
            redirect('/admin/pages');
        }
    }
}

// Fetch Pages
$stmt = $pdo->query("SELECT * FROM pages ORDER BY title ASC");
$pages = $stmt->fetchAll();

// If editing
$edit_page = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_page = $stmt->fetch();
}

$show_form = isset($_GET['add']) || $edit_page;

require_once __DIR__ . '/../includes/admin_layout.php';
$content_file = __DIR__ . '/pages_content.php';
render_admin_layout($content_file, $pdo, $settings, [
    'pages' => $pages,
    'show_form' => $show_form,
    'edit_page' => $edit_page
]);
