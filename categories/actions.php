<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/repositories.php';

require_auth();

if (!is_post()) {
    redirect('categories.php');
}

verify_csrf();

$userId = current_user_id();
$action = $_POST['action'] ?? '';
$name = trim($_POST['name'] ?? '');
$categoryId = (int) ($_POST['category_id'] ?? 0);

if ($userId === null) {
    redirect('login.php');
}

if ($action === 'create') {
    if ($name === '') {
        flash('warning', 'Category name is required.');
    } elseif (category_name_exists($userId, $name)) {
        flash('warning', 'Category already exists.');
    } else {
        create_category($userId, $name);
        flash('success', 'Category created.');
    }
}

if ($action === 'rename') {
    if ($name === '') {
        flash('warning', 'Category name is required.');
    } elseif (category_name_exists($userId, $name, $categoryId)) {
        flash('warning', 'Category already exists.');
    } else {
        update_category($userId, $categoryId, $name);
        flash('success', 'Category renamed.');
    }
}

if ($action === 'delete') {
    delete_category($userId, $categoryId);
    flash('success', 'Category deleted.');
}

redirect('categories.php');
