<?php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }
// migrate/admin/index.php

$GLOBALS['admin_page'] = 'posts';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {
        if (isset($_POST['save_post'])) {
            $id = $_POST['id'];
            $title = $_POST['title'];
            $category_id = $_POST['category_id'];
            $excerpt = $_POST['excerpt'];
            $content = $_POST['content'];
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));

            $image = $_POST['image_url'];
            if (!empty($_FILES['image_file']['name'])) {
                $uploaded_image = handle_image_upload($_FILES['image_file'], 'assets/uploads/posts/');
                if ($uploaded_image) {
                    $image = $uploaded_image;
                }
            }
            $date = $_POST['date'] ?: date('Y-m-d');
            $is_top_story = isset($_POST['is_top_story']) ? 1 : 0;
            $meta_title = $_POST['meta_title'];
            $meta_description = $_POST['meta_description'];
            $meta_keywords = $_POST['meta_keywords'];

            if ($id) {
                $stmt = $pdo->prepare("UPDATE posts SET title=?, slug=?, category_id=?, excerpt=?, content=?, image=?, date=?, is_top_story=?, meta_title=?, meta_description=?, meta_keywords=? WHERE id=?");
                $stmt->execute([$title, $slug, $category_id, $excerpt, $content, $image, $date, $is_top_story, $meta_title, $meta_description, $meta_keywords, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO posts (title, slug, category_id, excerpt, content, image, date, is_top_story, meta_title, meta_description, meta_keywords, author) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Admin')");
                $stmt->execute([$title, $slug, $category_id, $excerpt, $content, $image, $date, $is_top_story, $meta_title, $meta_description, $meta_keywords]);

                // Notify subscribers of new post
                $new_post_id = $pdo->lastInsertId();
                $post_url = "/post/" . $new_post_id;
                notify_subscribers_new_post($pdo, $title, $post_url);

                // Autopost to Social Media
                autopost_to_social($pdo, $title, $post_url);
            }
            redirect('/admin/posts');
        } elseif (isset($_POST['delete_post'])) {
            $id = $_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id=?");
            $stmt->execute([$id]);
            redirect('/admin/posts');
        }
    }
}

// Fetch Posts
$stmt = $pdo->query("SELECT * FROM posts ORDER BY date DESC");
$posts = $stmt->fetchAll();

// If editing
$edit_post = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_post = $stmt->fetch();
}

$show_form = isset($_GET['add']) || $edit_post;

require_once __DIR__ . '/../includes/image_handler.php';
require_once __DIR__ . '/../includes/admin_layout.php';

// Internal content file for the layout
$content_file = __DIR__ . '/posts_content.php';

// I'll create posts_content.php next, but for now I'll just write the main logic here.
// Actually, to keep it clean, I'll write the content to a separate file.
render_admin_layout($content_file, $pdo, $settings, [
    'posts' => $posts,
    'show_form' => $show_form,
    'edit_post' => $edit_post
]);
