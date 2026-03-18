<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/repositories.php';

require_auth();

$userId = current_user_id();
$categories = get_categories($userId);
$pageTitle = 'Categories';

require_once __DIR__ . '/../includes/header.php';
?>
<section class="hero-panel compact mb-4">
    <div>
        <p class="eyebrow mb-2">Taxonomy</p>
        <h1 class="hero-title mb-2">Categories</h1>
        <p class="hero-copy mb-0">Keep the note library structured without making the workflow rigid.</p>
    </div>
    <div>
        <a class="btn app-btn app-btn-ghost" href="<?= e(app_url('dashboard.php')) ?>">Back</a>
    </div>
</section>

<div class="row g-4">
    <div class="col-lg-4">
        <section class="sidebar-card">
            <p class="eyebrow mb-1">Create</p>
            <h2 class="section-title mb-3">Add category</h2>
            <form method="post" action="<?= e(app_url('category_actions.php')) ?>" class="vstack gap-3">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="create">
                <input class="form-control" name="name" placeholder="Category name" required>
                <button class="btn app-btn app-btn-primary" type="submit">Add category</button>
            </form>
        </section>
    </div>
    <div class="col-lg-8">
        <section class="app-card">
            <div class="panel-header mb-3">
                <div>
                    <p class="eyebrow mb-1">Manage</p>
                    <h2 class="section-title mb-0">Existing categories</h2>
                </div>
            </div>
            <div class="vstack gap-3">
                <?php if ($categories === []): ?>
                    <div class="note-card empty-state">
                        <h3 class="h5 mb-2">No categories yet</h3>
                        <p class="mb-0 text-secondary">Create one on the left, then assign it from the note editor.</p>
                    </div>
                <?php endif; ?>
                <?php foreach ($categories as $category): ?>
                    <div class="note-card">
                        <div class="note-card-top mb-3">
                            <div>
                                <p class="eyebrow mb-1">Category</p>
                                <h3 class="section-title mb-0"><?= e($category['name']) ?></h3>
                            </div>
                            <span class="meta-chip"><?= (int) $category['note_count'] ?> notes</span>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-8">
                                <form method="post" action="<?= e(app_url('category_actions.php')) ?>" class="d-flex gap-2">
                                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="rename">
                                    <input type="hidden" name="category_id" value="<?= (int) $category['id'] ?>">
                                    <input class="form-control" name="name" value="<?= e($category['name']) ?>" required>
                                    <button class="btn app-btn app-btn-ghost" type="submit">Rename</button>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <form method="post" action="<?= e(app_url('category_actions.php')) ?>" onsubmit="return confirm('Delete this category? Notes will stay uncategorized.');">
                                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="category_id" value="<?= (int) $category['id'] ?>">
                                    <button class="btn btn-outline-danger w-100" type="submit">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>