<?php echo validation_errors('<div class="error">', '</div>'); ?>

<h2>Edit Post</h2>

<?php echo form_open('posts/edit/' . $post['id']); ?>
    <div class="form-group">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo set_value('title', $post['title']); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="author">Author:</label>
        <input type="text" name="author" id="author" value="<?php echo set_value('author', $post['author']); ?>">
    </div>
    
    <div class="form-group">
        <label for="content">Content:</label>
        <textarea name="content" id="content" required><?php echo set_value('content', $post['content']); ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="published" <?php echo set_select('status', 'published', ($post['status'] === 'published')); ?>>Published</option>
            <option value="draft" <?php echo set_select('status', 'draft', ($post['status'] === 'draft')); ?>>Draft</option>
        </select>
    </div>
    
    <button type="submit" class="btn">Update Post</button>
    <a href="<?php echo base_url('posts/view/' . $post['id']); ?>" class="btn" style="background: #6c757d;">Cancel</a>
<?php echo form_close(); ?> 
