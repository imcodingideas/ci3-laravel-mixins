<div class="max-w-2xl mx-auto">
    <?php if (validation_errors()): ?>
        <?php echo alert(validation_errors(), 'error'); ?>
    <?php endif; ?>

    <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Create New Post</h2>
            <p class="mt-1 text-sm text-gray-600">Fill in the details below to create a new blog post.</p>
        </div>

        <?php echo form_open('create', ['class' => 'space-y-6']); ?>
            
            <div>
                <?php echo form_label_tw('Title', 'title', true); ?>
                <?php echo form_input_tw('title', 'text', set_value('title'), 'Enter post title'); ?>
            </div>
            
            <div>
                <?php echo form_label_tw('Author', 'author_id', true); ?>
                <select name="author_id" 
                        id="author_id" 
                        required
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="">Select an author</option>
                    <?php if (isset($authors)): ?>
                        <?php foreach ($authors as $author): ?>
                            <option value="<?php echo $author['id']; ?>" <?php echo set_select('author_id', $author['id']); ?>>
                                <?php echo htmlspecialchars($author['name']); ?> (<?php echo htmlspecialchars($author['email']); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div>
                <?php echo form_label_tw('Tags', 'tags'); ?>
                <select name="tags[]" 
                        id="tags" 
                        multiple
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary min-h-[120px]">
                    <?php if (isset($tags)): ?>
                        <?php foreach ($tags as $tag): ?>
                            <option value="<?php echo $tag['id']; ?>" 
                                    style="background-color: <?php echo $tag['color']; ?>20;"
                                    <?php echo (is_array(set_value('tags')) && in_array($tag['id'], set_value('tags'))) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tag['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="mt-1 text-sm text-gray-500">Hold Ctrl/Cmd to select multiple tags</p>
            </div>
            
            <div>
                <?php echo form_label_tw('Content', 'content', true); ?>
                <textarea name="content" 
                          id="content" 
                          required
                          rows="12"
                          class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary resize-y"
                          placeholder="Write your post content here..."><?php echo set_value('content'); ?></textarea>
            </div>
            
            <div>
                <?php echo form_label_tw('Status', 'status', true); ?>
                <select name="status" 
                        id="status"
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    <option value="draft" <?php echo set_select('status', 'draft', true); ?>>Draft</option>
                    <option value="published" <?php echo set_select('status', 'published'); ?>>Published</option>
                </select>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 px-4 py-2 text-sm bg-primary hover:bg-primary-dark text-white focus:ring-primary">
                    Create Post
                </button>
                <?php echo btn('Cancel', base_url(), 'outline', 'md', 'w-full sm:w-auto'); ?>
            </div>
            
        <?php echo form_close(); ?>
    </div>
</div> 
