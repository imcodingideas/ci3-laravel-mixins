<div class="post-detail">
    <h1><?php echo htmlspecialchars((string) $post['title']); ?></h1>
    
    <div class="post-meta">
        <p><strong>Author:</strong> <?php echo htmlspecialchars((string) $post['author']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars((string) $post['status']); ?></p>
        <p><strong>Created:</strong> <?php echo date('F j, Y g:i A', strtotime((string) $post['created_at'])); ?></p>
        <?php if ($post['updated_at'] !== $post['created_at']): ?>
            <p><strong>Updated:</strong> <?php echo date('F j, Y g:i A', strtotime((string) $post['updated_at'])); ?></p>
        <?php endif; ?>
    </div>
    
    <div class="post-content">
        <?php echo nl2br(htmlspecialchars((string) $post['content'])); ?>
    </div>
    
    <div class="post-actions">
        <a href="<?php echo base_url('posts'); ?>" class="btn">← Back to Posts</a>
        <a href="<?php echo base_url('posts/edit/' . $post['id']); ?>" class="btn">Edit</a>
        <a href="<?php echo base_url('posts/delete/' . $post['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
    </div>
</div> 
