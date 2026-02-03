<?php
// articles_action.php

require_once 'confi.php';  // or your config include

$action = $_REQUEST['action'] ?? '';

if ($action === 'add' || $action === 'update') {
  $title = $_POST['title'] ?? '';
  $content = $_POST['content'] ?? '';
  $status = $_POST['status'] ?? 'published';  // NEW: from form
  $id = $_POST['id'] ?? 0;

  if (empty($title) || empty($content)) {
    echo json_encode(['status' => 'error', 'message' => 'Title and content required']);
    exit;
  }

  if ($action === 'add') {
    $stmt = $pdo->prepare("INSERT INTO articles (title, content, status) VALUES (?, ?, ?)");
    $stmt->execute([$title, $content, $status]);
    echo json_encode(['status' => 'success', 'message' => 'Article ' . ($status === 'draft' ? 'saved as draft' : 'published')]);
  } else {
    $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, status = ? WHERE id = ?");
    $stmt->execute([$title, $content, $status, $id]);
    echo json_encode(['status' => 'success', 'message' => 'Article updated']);
  }
  exit;
}

if ($action === 'fetch') {
  $stmt = $pdo->query("SELECT * FROM articles WHERE status = 'published' ORDER BY id DESC");
  $articles = $stmt->fetchAll();
  echo json_encode(['status' => 'success', 'data' => $articles]);
  exit;
}

if ($action === 'fetch_drafts') {  // NEW
  $stmt = $pdo->query("SELECT * FROM articles WHERE status = 'draft' ORDER BY id DESC");
  $drafts = $stmt->fetchAll();
  echo json_encode(['status' => 'success', 'data' => $drafts]);
  exit;
}

// ... your existing delete code ...
