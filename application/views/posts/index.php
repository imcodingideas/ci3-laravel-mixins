<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <div class="lg:col-span-3 min-w-0">
        <!-- Search Form -->
        <?php echo search_form($search ?? '', base_url('posts')); ?>

        <!-- Active Filters -->
        <?php if ($search || $current_tag || $current_author): ?>
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800">
                            <strong>Active filters:</strong>
                            <?php if ($search): ?>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Search: "<?php echo htmlspecialchars($search); ?>"
                                </span>
                            <?php endif; ?>
                            <?php if ($current_tag): ?>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Tag: <?php echo htmlspecialchars($current_tag); ?>
                                </span>
                            <?php endif; ?>
                            <?php if ($current_author): ?>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Author ID: <?php echo htmlspecialchars($current_author); ?>
                                </span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Posts Results -->
        <?php if (!empty($posts)): ?>
            <div class="mb-6">
                <p class="text-sm text-gray-600">
                    <span class="font-medium"><?php echo $total_posts; ?></span> posts found 
                    <?php if ($current_page > 1 || $total_pages > 1): ?>
                        (Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>)
                    <?php endif; ?>
                </p>
            </div>

            <div class="space-y-6">
                <?php foreach ($posts as $post): ?>
                    <article class="bg-white shadow-sm border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="mb-4">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">
                                <a href="<?php echo base_url('posts/view/' . $post['id']); ?>" class="hover:text-primary transition-colors">
                                    <?php echo htmlspecialchars((string) $post['title']); ?>
                                </a>
                            </h2>
                            
                            <?php 
                            $author_name = !empty($post['author']) && is_array($post['author']) 
                                ? $post['author']['name'] 
                                : (string) $post['author'];
                            $author_url = !empty($post['author']) && is_array($post['author']) && isset($post['author']['id'])
                                ? base_url('posts/author/' . $post['author']['id'])
                                : '#';
                            ?>
                            
                            <div class="flex flex-wrap items-center text-sm text-gray-500 space-x-4 mb-4">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    <?php if ($author_url !== '#'): ?>
                                        <a href="<?php echo $author_url; ?>" class="hover:text-primary">
                                            By <?php echo htmlspecialchars($author_name); ?>
                                        </a>
                                    <?php else: ?>
                                        By <?php echo htmlspecialchars($author_name); ?>
                                    <?php endif; ?>
                                </span>
                                
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <?php echo date('F j, Y', strtotime((string) $post['created_at'])); ?>
                                </span>
                                
                                <span><?php echo status_badge($post['status']); ?></span>
                            </div>
                        </div>

                        <?php if (!empty($post['tags'])): ?>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <?php foreach ($post['tags'] as $tag): ?>
                                    <a href="<?php echo base_url('posts/tag/' . $tag['slug']); ?>" 
                                       class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white hover:opacity-80 transition-opacity"
                                       style="background-color: <?php echo $tag['color']; ?>">
                                        <?php echo htmlspecialchars($tag['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="text-gray-700 mb-4">
                            <?php echo substr(htmlspecialchars((string) $post['content']), 0, 300); ?><?php echo strlen((string) $post['content']) > 300 ? '...' : ''; ?>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <?php echo btn('Read More', base_url('posts/view/' . $post['id']), 'primary', 'sm'); ?>
                            <?php echo btn('Edit', base_url('posts/edit/' . $post['id']), 'secondary', 'sm'); ?>
                            <?php echo btn('Delete', base_url('posts/delete/' . $post['id']), 'danger', 'sm', 'onclick="return confirm(\'Are you sure you want to delete this post?\')"'); ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav class="flex justify-center mt-8">
                    <div class="flex space-x-1">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php 
                            $params = [];
                            if ($search) $params['search'] = $search;
                            if ($current_tag) $params['tag'] = $current_tag;
                            if ($current_author) $params['author'] = $current_author;
                            $params['page'] = $i;
                            $query_string = http_build_query($params);
                            $active_class = ($i == $current_page) ? 'bg-primary text-white' : 'bg-white text-gray-500 hover:bg-gray-50';
                            ?>
                            <a href="<?php echo base_url('posts?' . $query_string); ?>" 
                               class="px-3 py-2 text-sm font-medium border border-gray-300 <?php echo $active_class; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No posts found</h3>
                <?php if ($search || $current_tag || $current_author): ?>
                    <p class="mt-1 text-sm text-gray-500">
                        Try adjusting your search or 
                        <a href="<?php echo base_url(); ?>" class="text-primary hover:text-primary-dark">view all posts</a>
                    </p>
                    <div class="mt-6">
                        <?php echo btn('Create New Post', base_url('create'), 'primary'); ?>
                    </div>
                <?php else: ?>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first post.</p>
                    <div class="mt-6">
                        <?php echo btn('Create First Post', base_url('posts/create'), 'primary'); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 min-w-0">
        <div class="space-y-6">
            <?php if (!empty($popular_tags)): ?>
                <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Popular Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($popular_tags as $tag): ?>
                            <a href="<?php echo base_url('posts/tag/' . $tag['slug']); ?>" 
                               class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white hover:opacity-80 transition-opacity"
                               style="background-color: <?php echo $tag['color']; ?>">
                                <?php echo htmlspecialchars($tag['name']); ?>
                                <span class="ml-1 text-xs opacity-75">(<?php echo $tag['published_posts_count'] ?? 0; ?>)</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($recent_authors)): ?>
                <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Active Authors</h3>
                    <div class="space-y-3">
                        <?php foreach ($recent_authors as $author): ?>
                            <div class="flex items-center justify-between">
                                <a href="<?php echo base_url('posts/author/' . $author['id']); ?>" 
                                   class="text-sm font-medium text-gray-900 hover:text-primary">
                                    <?php echo htmlspecialchars($author['name']); ?>
                                </a>
                                <span class="text-xs text-gray-500"><?php echo $author['published_posts_count']; ?> posts</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div> 
