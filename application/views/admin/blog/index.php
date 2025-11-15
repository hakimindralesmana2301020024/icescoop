<div class="admin-page">
    <div class="admin-actions">
        <h2>Blog Posts</h2>
        <button id="btn-create-post" class="btn btn-primary">Create New Post</button>
    </div>

    <?php if ($this->session->flashdata('blog_success')): ?>
        <div class="alert alert-success" style="margin-top:1rem;"><?= $this->session->flashdata('blog_success'); ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('blog_error')): ?>
        <div class="alert alert-danger" style="margin-top:1rem;"><?= $this->session->flashdata('blog_error'); ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('blog_debug')): ?>
        <div class="alert alert-info" style="margin-top:1rem; white-space:pre-wrap;"><?php echo $this->session->flashdata('blog_debug'); ?></div>
    <?php endif; ?>

    <table class="table table-striped" style="width:100%; margin-top:1rem;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Author</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($posts)): foreach ($posts as $p): ?>
            <tr>
                <td><?= $p['id']; ?></td>
                <td><?= htmlspecialchars($p['title']); ?></td>
                <td><?= htmlspecialchars($p['status']); ?></td>
                <td><?= htmlspecialchars($p['author_id']); ?></td>
                <td><?= $p['created_at']; ?></td>
                <td>
                    <a href="<?= base_url('index.php/admin/blog?edit=' . $p['id']); ?>">Edit</a> |
                    <a href="<?= base_url('index.php/admin/blog?delete=' . $p['id']); ?>" onclick="return confirm('Delete this post?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="6">No posts yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div id="create-post-container" style="display:none; margin-top:1rem;">
        <?php $this->load->view('admin/blog/form', ['post' => null]); ?>
    </div>

    <script>
    (function(){
        var btn = document.getElementById('btn-create-post');
        var container = document.getElementById('create-post-container');
        if (!btn || !container) return;
        btn.addEventListener('click', function(e){
            e.preventDefault();
            if (container.style.display === 'none') {
                container.style.display = 'block';
                btn.textContent = 'Hide Editor';
            } else {
                container.style.display = 'none';
                btn.textContent = 'Create New Post';
            }
        });
    })();
    </script>
</div>
