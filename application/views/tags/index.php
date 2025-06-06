<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <div class="lg:col-span-3">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Tags</h2>
            <?php echo btn('Create New Tag', base_url('tags/create'), 'primary'); ?>
        </div>

        <?php if (!empty($tags)): ?>
            <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">All Tags</h3>
                    <p class="mt-1 text-sm text-gray-500">Manage and organize your blog tags</p>
                </div>

                <div class="divide-y divide-gray-200">
                    <?php foreach ($tags as $tag): ?>
                        <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white"
                                              style="background-color: <?php echo $tag['color']; ?>">
                                            <?php echo htmlspecialchars($tag['name']); ?>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($tag['name']); ?>
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                /<?php echo htmlspecialchars($tag['slug']); ?>
                                            </span>
                                        </div>
                                        <?php if (!empty($tag['description'])): ?>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <?php echo substr(htmlspecialchars($tag['description']), 0, 100); ?><?php echo strlen($tag['description']) > 100 ? '...' : ''; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="text-sm text-gray-500">
                                        <span class="font-medium"><?php echo $tag['posts_count'] ?? 0; ?></span> posts
                                    </div>
                                    <div class="flex space-x-2">
                                        <?php echo btn('Edit', base_url('tags/edit/' . $tag['id']), 'secondary', 'sm'); ?>
                                        <?php echo btn('Delete', base_url('tags/delete/' . $tag['id']), 'danger', 'sm', 'onclick="return confirm(\'Are you sure you want to delete this tag?\')"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No tags found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first tag.</p>
                <div class="mt-6">
                    <?php echo btn('Create First Tag', base_url('tags/create'), 'primary'); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="lg:col-span-1">
        <div class="space-y-6">
            <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Tags</span>
                        <span class="text-2xl font-bold text-gray-900"><?php echo count($tags); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Used Tags</span>
                        <span class="text-2xl font-bold text-green-600">
                            <?php echo count(array_filter($tags, function($t) { return ($t['posts_count'] ?? 0) > 0; })); ?>
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Unused Tags</span>
                        <span class="text-2xl font-bold text-gray-400">
                            <?php echo count(array_filter($tags, function($t) { return ($t['posts_count'] ?? 0) == 0; })); ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if (!empty($tags)): ?>
                <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tag Cloud</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach (array_slice($tags, 0, 10) as $tag): ?>
                            <a href="<?php echo base_url('posts/tag/' . $tag['slug']); ?>" 
                               class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium text-white hover:opacity-80 transition-opacity"
                               style="background-color: <?php echo $tag['color']; ?>">
                                <?php echo htmlspecialchars($tag['name']); ?>
                                <?php if (($tag['posts_count'] ?? 0) > 0): ?>
                                    <span class="ml-1 opacity-75">(<?php echo $tag['posts_count']; ?>)</span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div> 
