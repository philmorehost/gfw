<?php
// migrate/admin/categories.php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }
$GLOBALS['admin_page'] = 'categories';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {
        if (isset($_POST['save_category'])) {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
            $description = $_POST['description'];

            if ($id) {
                $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=?, description=? WHERE id=?");
                $stmt->execute([$name, $slug, $description, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
                $stmt->execute([$name, $slug, $description]);
            }
            redirect('/admin/categories');
        } elseif (isset($_POST['delete_category'])) {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
            $stmt->execute([$id]);
            redirect('/admin/categories');
        }
    }
}

// Fetch Categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

// If editing
$edit_category = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_category = $stmt->fetch();
}

$show_form = isset($_GET['add']) || $edit_category;

require_once __DIR__ . '/../includes/admin_layout.php';
$content_file = __DIR__ . '/categories_content.php';
render_admin_layout($content_file, $pdo, $settings, [
    'categories' => $categories,
    'show_form' => $show_form,
    'edit_category' => $edit_category
]);
