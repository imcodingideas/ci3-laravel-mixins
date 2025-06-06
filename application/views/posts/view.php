<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <div class="lg:col-span-3">
        <article class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
            <header class="p-6 border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars((string) $post['title']); ?></h1>
                
                <div class="space-y-4">
                    <?php if (!empty($post['author'])): ?>
                        <div class="flex items-start space-x-3">
                            <?php 
                            $avatarUrl = '';
                            if (!empty($post['author']['avatar'])) {
                                $avatarUrl = base_url('uploads/avatars/' . $post['author']['avatar']);
                            } else {
                                $hash = md5(strtolower(trim($post['author']['email'])));
                                $avatarUrl = "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=48";
                            }
                            ?>
                            <img src="<?php echo $avatarUrl; ?>" 
                                 alt="<?php echo htmlspecialchars($post['author']['name']); ?>" 
                                 class="w-12 h-12 rounded-full">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">
                                    <a href="<?php echo base_url('posts/author/' . $post['author']['id']); ?>" 
                                       class="hover:text-primary transition-colors">
                                        <?php echo htmlspecialchars($post['author']['name']); ?>
                                    </a>
                                </div>
                                <?php if (!empty($post['author']['bio'])): ?>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <?php echo substr(htmlspecialchars($post['author']['bio']), 0, 100); ?><?php echo strlen($post['author']['bio']) > 100 ? '...' : ''; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            <span>By <?php echo htmlspecialchars((string) $post['author']); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex flex-wrap items-center text-sm text-gray-500 space-x-4">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            Published: <?php echo date('F j, Y g:i A', strtotime((string) $post['created_at'])); ?>
                        </span>
                        <?php if ($post['updated_at'] !== $post['created_at']): ?>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                Updated: <?php echo date('F j, Y g:i A', strtotime((string) $post['updated_at'])); ?>
                            </span>
                        <?php endif; ?>
                        <span><?php echo status_badge($post['status']); ?></span>
                    </div>
                </div>

                <?php if (!empty($post['tags'])): ?>
                    <div class="mt-4">
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($post['tags'] as $tag): ?>
                                <a href="<?php echo base_url('posts/tag/' . $tag['slug']); ?>" 
                                   class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white hover:opacity-80 transition-opacity"
                                   style="background-color: <?php echo $tag['color']; ?>">
                                    <?php echo htmlspecialchars($tag['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </header>
            
            <div class="p-6">
                <div class="prose max-w-none text-gray-700 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars((string) $post['content'])); ?>
                </div>
            </div>
            
            <footer class="p-6 border-t border-gray-200 bg-gray-50">
                <div class="flex flex-wrap gap-3">
                    <?php echo btn('← Back to Posts', base_url('posts'), 'outline'); ?>
                    <?php echo btn('Edit', base_url('posts/edit/' . $post['id']), 'secondary'); ?>
                    <?php echo btn('Delete', base_url('posts/delete/' . $post['id']), 'danger', 'md', 'onclick="return confirm(\'Are you sure you want to delete this post?\')"'); ?>
                </div>
            </footer>
        </article>

        <!-- Related Posts -->
        <?php if (!empty($related_posts)): ?>
            <section class="mt-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Related Posts</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($related_posts as $related): ?>
                        <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <h4 class="font-medium text-gray-900 mb-2">
                                <a href="<?php echo base_url('posts/view/' . $related['id']); ?>" 
                                   class="hover:text-primary transition-colors">
                                    <?php echo htmlspecialchars($related['title']); ?>
                                </a>
                            </h4>
                            <div class="text-sm text-gray-500 mb-3">
                                <?php if (!empty($related['author'])): ?>
                                    <span class="mr-3">
                                        <a href="<?php echo base_url('posts/author/' . $related['author']['id']); ?>" 
                                           class="hover:text-primary">
                                            <?php echo htmlspecialchars($related['author']['name']); ?>
                                        </a>
                                    </span>
                                <?php endif; ?>
                                <span><?php echo date('M j, Y', strtotime($related['created_at'])); ?></span>
                            </div>
                            <?php if (!empty($related['tags'])): ?>
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach (array_slice($related['tags'], 0, 3) as $tag): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-white"
                                              style="background-color: <?php echo $tag['color']; ?>">
                                            <?php echo htmlspecialchars($tag['name']); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <div class="space-y-6">
            <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Search Posts</h4>
                <?php echo search_form('', base_url('posts')); ?>
            </div>

            <?php if (!empty($post['author']) && !empty($post['author']['social_media'])): ?>
                <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">
                        Follow <?php echo htmlspecialchars($post['author']['name']); ?>
                    </h4>
                    <div class="space-y-2">
                        <?php 
                        $social = $post['author']['social_media'] ?: [];
                        $platformUrls = [
                            'twitter' => 'https://twitter.com/',
                            'github' => 'https://github.com/',
                            'linkedin' => 'https://linkedin.com/in/',
                            'dribbble' => 'https://dribbble.com/',
                            'behance' => 'https://behance.net/',
                            'instagram' => 'https://instagram.com/'
                        ];
                        foreach ($social as $platform => $username): 
                            if (isset($platformUrls[$platform])):
                                $username = str_replace('@', '', $username);
                        ?>
                            <a href="<?php echo $platformUrls[$platform] . $username; ?>" 
                               target="_blank" 
                               rel="noopener" 
                               class="flex items-center text-sm text-gray-600 hover:text-primary transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                                </svg>
                                <?php echo ucfirst($platform); ?>
                            </a>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div> 
