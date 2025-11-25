<div class="admin-page">
    <?php $is_edit = !empty($post); ?>
    <h2><?= $is_edit ? 'Edit Post' : 'Create Post'; ?></h2>

    <?php if ($this->session->flashdata('blog_error')): ?>
        <div style="background:#ffd6d6;border:1px solid #ff9b9b;padding:10px;margin:10px 0;color:#700;">
            <?= htmlspecialchars($this->session->flashdata('blog_error')); ?>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('blog_success')): ?>
        <div style="background:#e6ffea;border:1px solid #b7efc1;padding:10px;margin:10px 0;color:#066;">
            <?= htmlspecialchars($this->session->flashdata('blog_success')); ?>
        </div>
    <?php endif; ?>

    <?php
    // Determine action: create vs edit. When included inline, ensure create posts go to admin/blog/create
    if ($is_edit) {
        // When editing via Admin::blog proxy, submit to admin/blog?update={id}
        $action = base_url('index.php/admin/blog?update=' . (int)$post['id']);
    } else {
        // Submit to admin/blog (Admin controller proxy) so POST reaches server
        $action = base_url('index.php/admin/blog');
    }
    ?>
    <form method="post" action="<?= $action; ?>" onsubmit="return submitQuill();" enctype="multipart/form-data">
        <div>
            <label>Title</label>
            <input type="text" name="title" id="title" value="<?= $is_edit ? htmlspecialchars($post['title']) : ''; ?>" required style="width:100%; padding:8px;">
        </div>
        <div style="margin-top:.5rem;">
            <label>Slug (optional)</label>
            <input type="text" name="slug" id="slug" value="<?= $is_edit ? htmlspecialchars($post['slug']) : ''; ?>" style="width:100%; padding:8px;">
        </div>
        <div style="margin-top:.5rem;">
            <label>Excerpt</label>
            <textarea name="excerpt" rows="2" style="width:100%;"><?= $is_edit ? htmlspecialchars($post['excerpt']) : ''; ?></textarea>
        </div>

        <div style="margin-top:.5rem; display:flex; gap:12px; align-items:center;">
            <div>
                <label>Cover Image</label>
                <input type="file" name="featured_image" accept="image/*">
            </div>
            <?php if ($is_edit && !empty($post['featured_image'])): ?>
                <div style="max-width:220px;">
                    <label>Current cover</label>
                    <div style="border:1px solid #ddd;padding:6px;background:#fff;">
                        <img src="<?= base_url('assets/images/' . $post['featured_image']); ?>" alt="cover" style="width:200px;height:auto;display:block;">
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top:.5rem;">
            <label>Content</label>
                <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                <style>
                    /* contain floated images inside the editor so they don't overlap footer */
                    #quill-editor { overflow: auto; box-sizing: border-box; }
                    #quill-editor::after { content: ""; display: table; clear: both; }
                    /* ensure our alignment class doesn't break responsiveness */
                    #quill-editor img.quill-auto-aligned { max-width: 100%; height: auto; }
                    /* make editor text justified (rata kiri kanan) by default */
                    #quill-editor .ql-editor { text-align: justify; text-justify: inter-word; }
                </style>
                <div id="quill-editor" style="height:320px; background:#fff;"></div>
                <input type="file" id="quill-image-input" accept="image/*" style="display:none">
            <input type="hidden" name="content_html" id="content_html">
            <input type="hidden" name="content_delta" id="content_delta">
        </div>

        <div style="margin-top:.5rem;">
            <label>Status</label>
            <select name="status">
                <option value="draft" <?= $is_edit && $post['status']=='draft' ? 'selected' : ''; ?>>Draft</option>
                <option value="published" <?= $is_edit && $post['status']=='published' ? 'selected' : ''; ?>>Published</option>
                <option value="archived" <?= $is_edit && $post['status']=='archived' ? 'selected' : ''; ?>>Archived</option>
            </select>
            <label style="margin-left:1rem;"><input type="checkbox" name="is_featured" <?= $is_edit && $post['is_featured'] ? 'checked' : ''; ?>> Featured</label>
        </div>

        <div style="margin-top:1rem;">
            <button type="submit" class="btn btn-primary"><?= $is_edit ? 'Update' : 'Save'; ?></button>
            <a href="<?= base_url('index.php/admin/blog'); ?>" class="btn">Cancel</a>
        </div>
    </form>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        // toolbar with image button + basic formatting
        var toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'header': 1 }, { 'header': 2 }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'align': [] }],
            ['link', 'image'],
            ['clean']
        ];

        var quill = new Quill('#quill-editor', { theme: 'snow', modules: { toolbar: toolbarOptions } });

        // Image upload handler: use a hidden file input, read as DataURL and insert as base64
        var imageInput = document.getElementById('quill-image-input');
        function selectLocalImage() {
            imageInput.click();
        }
        // When file selected, read and insert
        imageInput.addEventListener('change', function() {
            var file = imageInput.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function(e) {
                var range = quill.getSelection(true);
                // insert image embed at current selection
                quill.insertEmbed(range.index, 'image', e.target.result, 'user');
                // insert a newline after the image so the next typed text wraps beside the floated image
                quill.insertText(range.index + 1, '\n', 'user');
                // place caret in the newly created paragraph after the image
                quill.setSelection(range.index + 2, 0);
                quill.focus();
                // adjust inserted images (alternate left/right)
                setTimeout(function(){ adjustQuillImages(); }, 50);
                imageInput.value = '';
            };
            reader.readAsDataURL(file);
        });

        // Replace default image handler with our local selector
        var toolbar = quill.getModule('toolbar');
        toolbar.addHandler('image', selectLocalImage);

        // Adjust Quill images: alternate left/right and set width so two images fit side-by-side
        function adjustQuillImages() {
            var container = document.getElementById('quill-editor');
            if (!container) return;
            var imgs = container.querySelectorAll('img');
            imgs.forEach(function(img, idx){
                img.style.display = 'block';
                img.style.height = 'auto';
                img.style.maxWidth = '100%';
                img.style.width = '48%';
                img.style.marginTop = '6px';
                img.style.marginBottom = '6px';
                if (idx % 2 === 0) {
                    img.style.float = 'left';
                    img.style.marginRight = '6px';
                    img.style.marginLeft = '0';
                } else {
                    img.style.float = 'right';
                    img.style.marginLeft = '6px';
                    img.style.marginRight = '0';
                }
                img.classList.add('quill-auto-aligned');
            });
            // ensure a clear at the end so layout below editor is not affected
            var last = container.querySelector('.quill-auto-clear');
            if (last) last.parentNode.removeChild(last);
            var br = document.createElement('div');
            br.className = 'quill-auto-clear';
            br.style.clear = 'both';
            container.appendChild(br);
        }
    // populate editor if editing
    <?php if ($is_edit && !empty($post['content_delta'])): ?>
        try {
            var delta = JSON.parse(<?= json_encode($post['content_delta']); ?>);
            quill.setContents(delta);
        } catch (e) {
            document.getElementById('quill-editor').innerHTML = <?= json_encode($post['content_html']); ?>;
        }
    <?php elseif ($is_edit && !empty($post['content_html'])): ?>
        document.getElementById('quill-editor').innerHTML = <?= json_encode($post['content_html']); ?>;
    <?php endif; ?>
    // After editor populated, ensure images are adjusted
    setTimeout(function(){ adjustQuillImages(); }, 150);

    // auto-generate slug from title if empty
    document.getElementById('title').addEventListener('input', function(){
        var s = document.getElementById('slug');
        if (!s.value) {
            s.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');
        }
    });

    function submitQuill(){
        var html = quill.root.innerHTML;
        var delta = quill.getContents();
        document.getElementById('content_html').value = html;
        document.getElementById('content_delta').value = JSON.stringify(delta);
        return true;
    }
    </script>
</div>
