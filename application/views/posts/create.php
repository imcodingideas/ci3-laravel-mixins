<?php echo validation_errors('<div class="error">', '</div>'); ?>

<?php echo form_open('posts/create'); ?>
    <div class="form-group">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo set_value('title'); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="author">Author:</label>
        <input type="text" name="author" id="author" value="<?php echo set_value('author'); ?>">
    </div>
    
    <div class="form-group">
        <label for="content">Content:</label>
        <textarea name="content" id="content" required><?php echo set_value('content'); ?></textarea>
    </div>
    
    <button type="submit" class="btn">Create Post</button>
    <a href="<?php echo base_url('posts'); ?>" class="btn" style="background: #6c757d;">Cancel</a>
<?php echo form_close(); ?> 
