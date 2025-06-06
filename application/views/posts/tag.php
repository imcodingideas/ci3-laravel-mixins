<div class="content-wrapper">
    <div class="main-content">
        <!-- Tag Header -->
        <div class="tag-header">
            <h2>
                <span class="tag" style="background-color: <?php echo $tag['color']; ?>">
                    <?php echo htmlspecialchars($tag['name']); ?>
                </span>
                Posts tagged with "<?php echo htmlspecialchars($tag['name']); ?>"
            </h2>
            <?php if (!empty($tag['description'])): ?>
                <p class="tag-description"><?php echo htmlspecialchars($tag['description']); ?></p>
            <?php endif; ?>
            <p class="tag-meta">
                <a href="<?php echo base_url('posts'); ?>">← Back to all posts</a>
            </p>
        </div>

        <!-- Posts Results -->
        <?php if (!empty($posts)): ?>
            <div class="posts-meta">
                <p><?php echo count($posts); ?> posts found with this tag</p>
            </div>

            <?php foreach ($posts as $post): ?>
                <article class="post">
                    <h3><a href="<?php echo base_url('posts/view/' . $post['id']); ?>"><?php echo htmlspecialchars((string) $post['title']); ?></a></h3>
                    
                    <div class="post-meta">
                        <span class="author">
                            <?php if (!empty($post['author'])): ?>
                                <a href="<?php echo base_url('posts/author/' . $post['author']['id']); ?>">
                                    <?php echo htmlspecialchars($post['author']['name']); ?>
                                </a>
                            <?php else: ?>
                                <?php echo htmlspecialchars((string) $post['author']); ?>
                            <?php endif; ?>
                        </span>
                        <span class="date"><?php echo date('F j, Y', strtotime((string) $post['created_at'])); ?></span>
                        <span class="status status-<?php echo $post['status']; ?>"><?php echo ucfirst($post['status']); ?></span>
                    </div>

                    <?php if (!empty($post['tags'])): ?>
                        <div class="post-tags">
                            <?php foreach ($post['tags'] as $tag): ?>
                                <a href="<?php echo base_url('posts/tag/' . $tag['slug']); ?>" 
                                   class="tag" style="background-color: <?php echo $tag['color']; ?>">
                                    <?php echo htmlspecialchars($tag['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="post-excerpt">
                        <?php echo substr(htmlspecialchars((string) $post['content']), 0, 300); ?><?php echo strlen((string) $post['content']) > 300 ? '...' : ''; ?>
                    </div>

                    <div class="post-actions">
                        <a href="<?php echo base_url('posts/view/' . $post['id']); ?>" class="btn">Read More</a>
                        <a href="<?php echo base_url('posts/edit/' . $post['id']); ?>" class="btn btn-secondary">Edit</a>
                        <a href="<?php echo base_url('posts/delete/' . $post['id']); ?>" class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-posts">
                <p>No published posts found with this tag.</p>
                <p><a href="<?php echo base_url(); ?>">View all posts</a> or <a href="<?php echo base_url('create'); ?>">create a new post</a>.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <?php if (!empty($popular_tags)): ?>
            <div class="widget">
                <h4>Popular Tags</h4>
                <div class="tag-cloud">
                    <?php foreach ($popular_tags as $tag): ?>
                        <a href="<?php echo base_url('posts/tag/' . $tag['slug']); ?>" 
                           class="tag" style="background-color: <?php echo $tag['color']; ?>">
                            <?php echo htmlspecialchars($tag['name']); ?>
                            <span class="tag-count">(<?php echo $tag['published_posts_count'] ?? 0; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="widget">
            <h4>Search</h4>
            <form method="get" action="<?php echo base_url('posts'); ?>">
                <div class="search-group">
                    <input type="text" name="search" placeholder="Search posts..." class="search-input">
                    <button type="submit" class="btn search-btn">Go</button>
                </div>
            </form>
        </div>
    </div>
</div> 
