<?php
session_start();
include 'confi.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'DB connection failed']);
    exit;
}

if (!isset($_SESSION['email'])) {   
    http_response_code(403);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Unauthorized - not logged in (session missing)'
    ]);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $title   = trim($_POST['title']   ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $content === '') {
        echo json_encode(['status' => 'error', 'message' => 'Title and content required']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO articles (title, content, created_at) VALUES (?, ?, NOW())");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
        exit;
    }

    $stmt->bind_param("ss", $title, $content);
    $success = $stmt->execute();

    echo json_encode([
        'status'  => $success ? 'success' : 'error',
        'message' => $success ? 'Article added successfully' : 'Insert failed: ' . $stmt->error
    ]);

    $stmt->close();
    exit;
}

if ($action === 'fetch') {
    $result = $conn->query("SELECT * FROM articles ORDER BY id DESC");
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
        exit;
    }

    $articles = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data'   => $articles
    ]);
    exit;
}

/* ================= DELETE ARTICLE ================= */
if ($action === 'delete') {
    $id = $_POST['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Valid numeric ID required for delete'
        ]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
    if (!$stmt) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Prepare failed: ' . $conn->error
        ]);
        exit;
    }

    $stmt->bind_param("i", $id);
    $success = $stmt->execute();

    echo json_encode([
        'status'  => $success ? 'success' : 'error',
        'message' => $success ? 'Article deleted successfully' : 'Delete failed: ' . $stmt->error
    ]);

    $stmt->close();
    exit;
}

/* ================= UPDATE ARTICLE ================= */
if ($action === 'update') {
    $id      = $_POST['id'] ?? null;
    $title   = trim($_POST['title']   ?? '');
    $content = trim($_POST['content'] ?? '');

    if (!$id || !is_numeric($id) || $title === '' || $content === '') {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ID, title and content required for update'
        ]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE articles SET title = ?, content = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Prepare failed: ' . $conn->error
        ]);
        exit;
    }

    $stmt->bind_param("ssi", $title, $content, $id);
    $success = $stmt->execute();

    echo json_encode([
        'status'  => $success ? 'success' : 'error',
        'message' => $success ? 'Article updated successfully' : 'Update failed: ' . $stmt->error
    ]);

    $stmt->close();
    exit;
}

// fallback
echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
