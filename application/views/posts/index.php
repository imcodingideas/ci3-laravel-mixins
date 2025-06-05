<?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <h3><a href="<?php echo base_url('posts/view/' . $post['id']); ?>"><?php echo htmlspecialchars((string) $post['title']); ?></a></h3>
            <p><strong>Author:</strong> <?php echo htmlspecialchars((string) $post['author']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars((string) $post['status']); ?></p>
            <p><strong>Created:</strong> <?php echo date('F j, Y', strtotime((string) $post['created_at'])); ?></p>
            <p><?php echo substr(htmlspecialchars((string) $post['content']), 0, 200); ?><?php echo strlen((string) $post['content']) > 200 ? '...' : ''; ?></p>
            <a href="<?php echo base_url('posts/view/' . $post['id']); ?>" class="btn">Read More</a>
            <a href="<?php echo base_url('posts/edit/' . $post['id']); ?>" class="btn">Edit</a>
            <a href="<?php echo base_url('posts/delete/' . $post['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No posts found. <a href="<?php echo base_url('posts/create'); ?>">Create the first post!</a></p>
<?php endif; ?> 
