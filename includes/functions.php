<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function app_url(string $path = ''): string
{
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');

    if ($basePath === '' || $basePath === '.') {
        $basePath = '';
    }

    return $basePath . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . app_url($path));
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function verify_csrf(): void
{
    $token = $_POST['_csrf'] ?? '';

    if (!hash_equals($_SESSION['_csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid CSRF token.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $messages;
}

function old(string $key, string $default = ''): string
{
    return $_SESSION['_old'][$key] ?? $default;
}

function store_old_input(array $input): void
{
    $_SESSION['_old'] = $input;
}

function clear_old_input(): void
{
    unset($_SESSION['_old']);
}

function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function current_user(): ?array
{
    $userId = current_user_id();

    if ($userId === null) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, username, email, created_at FROM users WHERE id = :id');
    $stmt->execute(['id' => $userId]);

    $user = $stmt->fetch();

    return $user ?: null;
}

function require_guest(): void
{
    if (current_user_id() !== null) {
        redirect('dashboard.php');
    }
}

function require_auth(): void
{
    if (current_user_id() === null) {
        flash('warning', 'Please sign in first.');
        redirect('login.php');
    }
}

function format_datetime(?string $value): string
{
    if (!$value) {
        return '-';
    }

    return (new DateTimeImmutable($value))->format('Y-m-d H:i');
}

function excerpt(?string $html, int $limit = 140): string
{
    $text = trim(strip_tags((string) $html));

    if (mb_strlen($text) <= $limit) {
        return $text;
    }

    return mb_substr($text, 0, $limit - 3) . '...';
}

function clean_html(string $html): string
{
    $allowed = '<p><br><strong><em><u><ol><ul><li><blockquote><pre><code><h1><h2><h3><h4><a><span>';
    $clean = strip_tags($html, $allowed);

    return preg_replace('/javascript:/i', '', $clean) ?? '';
}

function build_query(array $params): string
{
    return http_build_query(array_filter($params, static fn ($value) => $value !== null && $value !== ''));
}
