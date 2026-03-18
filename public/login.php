<?php

declare(strict_types=1);

require_once __DIR__ . '/../auth/services.php';

require_guest();

$pageTitle = 'Login';
$identifier = '';

if (is_post()) {
    verify_csrf();
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if (attempt_login($identifier, $password)) {
        flash('success', 'Welcome back.');
        redirect('dashboard.php');
    }

    flash('danger', 'Invalid credentials.');
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="auth-shell">
    <section class="auth-stage">
        <div class="auth-aside">
            <p class="eyebrow mb-2">Private writing system</p>
            <h1 class="hero-title mb-3">Write like a designer built the room for you.</h1>
            <p class="hero-copy mb-4">Focused note taking, clean categorization, public sharing when you need it, and a calmer interface than the usual admin panel clutter.</p>
            <div class="auth-feature-list">
                <div class="auth-feature-item"><strong>Rich text</strong><span>Headings, lists, links, and code blocks.</span></div>
                <div class="auth-feature-item"><strong>Fast retrieval</strong><span>Search, filters, pagination, and quick capture.</span></div>
                <div class="auth-feature-item"><strong>Controlled sharing</strong><span>Public links stay opt-in per note.</span></div>
            </div>
        </div>
        <div class="app-card auth-card">
            <p class="eyebrow mb-2">Secure access</p>
            <h2 class="section-title mb-2">Sign in</h2>
            <p class="text-secondary mb-4">Use your username or email and password.</p>
            <form method="post" class="vstack gap-3">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                <div class="field-stack">
                    <label class="field-label">Username or email</label>
                    <input class="form-control" name="identifier" required value="<?= e($identifier) ?>">
                </div>
                <div class="field-stack">
                    <label class="field-label">Password</label>
                    <input class="form-control" type="password" name="password" required>
                </div>
                <button class="btn app-btn app-btn-primary" type="submit">Login</button>
                <p class="small text-secondary mb-0">Need an account? <a href="<?= e(app_url('register.php')) ?>">Register</a></p>
            </form>
        </div>
    </section>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>