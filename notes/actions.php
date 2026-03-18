<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/repositories.php';

require_auth();

$userId = current_user_id();
$action = $_POST['action'] ?? '';
$isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

if (!is_post()) {
    redirect('dashboard.php');
}

verify_csrf();

if ($userId === null) {
    redirect('login.php');
}

if ($action === 'quick_create') {
    $title = trim($_POST['title'] ?? 'Untitled note');
    $noteId = create_note($userId, $title !== '' ? $title : 'Untitled note', null, '');
    flash('success', 'Note created.');
    redirect('note.php?id=' . $noteId);
}

if ($action === 'save') {
    $noteId = (int) ($_POST['note_id'] ?? 0);
    $title = trim($_POST['title'] ?? 'Untitled note');
    $content = trim($_POST['content'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $isPublic = isset($_POST['is_public']) ? 1 : 0;
    $newCategory = trim($_POST['new_category'] ?? '');

    if ($newCategory !== '') {
        if (!category_name_exists($userId, $newCategory)) {
            $categoryId = create_category($userId, $newCategory);
        } else {
            $existing = array_values(array_filter(get_categories($userId), static fn ($category) => $category['name'] === $newCategory));
            $categoryId = isset($existing[0]['id']) ? (int) $existing[0]['id'] : 0;
        }
    }

    $categoryId = $categoryId > 0 ? $categoryId : null;

    if ($noteId > 0) {
        update_note($userId, $noteId, $title !== '' ? $title : 'Untitled note', $categoryId, $content, $isPublic);
        flash('success', 'Note updated.');
    } else {
        $noteId = create_note($userId, $title !== '' ? $title : 'Untitled note', $categoryId, $content, $isPublic);
        flash('success', 'Note created.');
    }

    redirect('note.php?id=' . $noteId);
}

if ($action === 'autosave') {
    $noteId = (int) ($_POST['note_id'] ?? 0);

    if ($noteId > 0) {
        update_note(
            $userId,
            $noteId,
            trim($_POST['title'] ?? 'Untitled note') ?: 'Untitled note',
            (int) ($_POST['category_id'] ?? 0) ?: null,
            trim($_POST['content'] ?? ''),
            (int) ($_POST['is_public'] ?? 0)
        );
    }

    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'saved_at' => date('H:i:s')]);
    exit;
}

if ($action === 'delete') {
    delete_note($userId, (int) ($_POST['note_id'] ?? 0));
    flash('success', 'Note deleted.');
    redirect('dashboard.php');
}

if ($action === 'regenerate_share') {
    $noteId = (int) ($_POST['note_id'] ?? 0);
    regenerate_share_token($userId, $noteId);
    flash('success', 'Share link refreshed.');
    redirect('note.php?id=' . $noteId);
}

redirect('dashboard.php');
