<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <div class="lg:col-span-3">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Authors</h2>
            <?php echo btn('Create New Author', base_url('authors/create'), 'primary'); ?>
        </div>

        <?php if (!empty($authors)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($authors as $author): ?>
                    <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex flex-col items-center text-center">
                            <div class="mb-4">
                                <?php 
                                $avatarUrl = '';
                                if (!empty($author['avatar'])) {
                                    $avatarUrl = base_url('uploads/avatars/' . $author['avatar']);
                                } else {
                                    $hash = md5(strtolower(trim($author['email'])));
                                    $avatarUrl = "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=80";
                                }
                                ?>
                                <img src="<?php echo $avatarUrl; ?>" 
                                     alt="<?php echo htmlspecialchars($author['name']); ?>" 
                                     class="w-20 h-20 rounded-full border-2 border-gray-200">
                            </div>
                            
                            <div class="w-full">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    <?php echo htmlspecialchars($author['name']); ?>
                                </h3>
                                
                                <div class="space-y-2 mb-4">
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($author['email']); ?></p>
                                    <div class="flex items-center justify-center space-x-4">
                                        <?php echo status_badge($author['status']); ?>
                                        <span class="text-xs text-gray-500">
                                            <?php echo $author['published_posts_count'] ?? 0; ?> posts
                                        </span>
                                    </div>
                                </div>

                                <?php if (!empty($author['bio'])): ?>
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                                        <?php echo substr(htmlspecialchars($author['bio']), 0, 120); ?><?php echo strlen($author['bio']) > 120 ? '...' : ''; ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($author['website'])): ?>
                                    <div class="mb-4">
                                        <a href="<?php echo htmlspecialchars($author['website']); ?>" 
                                           target="_blank" 
                                           class="text-sm text-primary hover:text-primary-dark transition-colors inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                                            </svg>
                                            Website
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex flex-wrap gap-2 w-full">
                                <?php echo btn('Edit', base_url('authors/edit/' . $author['id']), 'secondary', 'sm', 'w-full'); ?>
                                <?php echo btn('Delete', base_url('authors/delete/' . $author['id']), 'danger', 'sm', 'w-full onclick="return confirm(\'Are you sure you want to delete this author?\')"'); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No authors found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first author.</p>
                <div class="mt-6">
                    <?php echo btn('Create First Author', base_url('authors/create'), 'primary'); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Stats</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Authors</span>
                    <span class="text-2xl font-bold text-gray-900"><?php echo count($authors); ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Active Authors</span>
                    <span class="text-2xl font-bold text-green-600">
                        <?php echo count(array_filter($authors, function($a) { return $a['status'] == 'active'; })); ?>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Inactive Authors</span>
                    <span class="text-2xl font-bold text-gray-400">
                        <?php echo count(array_filter($authors, function($a) { return $a['status'] != 'active'; })); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div> 
