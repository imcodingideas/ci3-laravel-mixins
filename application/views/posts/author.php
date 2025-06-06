<div class="content-wrapper">
    <div class="main-content">
        <!-- Author Header -->
        <div class="author-header">
            <div class="author-info">
                <div class="author-avatar">
                    <?php 
                    $avatarUrl = '';
                    if (!empty($author['avatar'])) {
                        $avatarUrl = base_url('uploads/avatars/' . $author['avatar']);
                    } else {
                        $hash = md5(strtolower(trim($author['email'])));
                        $avatarUrl = "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=80";
                    }
                    ?>
                    <img src="<?php echo $avatarUrl; ?>" alt="<?php echo htmlspecialchars($author['name']); ?>" width="80" height="80">
                </div>
                <div class="author-details">
                    <h2>Posts by <?php echo htmlspecialchars($author['name']); ?></h2>
                    <?php if (!empty($author['bio'])): ?>
                        <p class="author-bio"><?php echo htmlspecialchars($author['bio']); ?></p>
                    <?php endif; ?>
                    <div class="author-meta">
                        <?php if (!empty($author['website'])): ?>
                            <a href="<?php echo htmlspecialchars($author['website']); ?>" target="_blank" rel="noopener">Website</a>
                        <?php endif; ?>
                        <?php if (!empty($author['social_media'])): ?>
                            <?php 
                            $social = $author['social_media'] ?: [];
                            foreach ($social as $platform => $username): 
                                $platformUrls = [
                                    'twitter' => 'https://twitter.com/',
                                    'github' => 'https://github.com/',
                                    'linkedin' => 'https://linkedin.com/in/',
                                    'dribbble' => 'https://dribbble.com/',
                                    'behance' => 'https://behance.net/',
                                    'instagram' => 'https://instagram.com/'
                                ];
                                if (isset($platformUrls[$platform])):
                                    $username = str_replace('@', '', $username);
                            ?>
                                <a href="<?php echo $platformUrls[$platform] . $username; ?>" target="_blank" rel="noopener">
                                    <?php echo ucfirst($platform); ?>
                                </a>
                            <?php endif; endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <p class="author-nav">
                        <a href="<?php echo base_url('posts'); ?>">← Back to all posts</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Posts Results -->
        <?php if (!empty($posts)): ?>
            <div class="posts-meta">
                <p><?php echo count($posts); ?> published posts by this author</p>
            </div>

            <?php foreach ($posts as $post): ?>
                <article class="post">
                    <h3><a href="<?php echo base_url('posts/view/' . $post['id']); ?>"><?php echo htmlspecialchars((string) $post['title']); ?></a></h3>
                    
                    <div class="post-meta">
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
                <p>This author hasn't published any posts yet.</p>
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
