<div class="max-w-2xl mx-auto">
    <?php if (validation_errors()): ?>
        <?php echo alert(validation_errors(), 'error'); ?>
    <?php endif; ?>

    <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Post</h2>
            <p class="mt-1 text-sm text-gray-600">Update the details of your blog post.</p>
        </div>

        <?php echo form_open('posts/edit/' . $post['id'], ['class' => 'space-y-6']); ?>
            
            <?php echo text_input('title', 'Title', set_value('title', $post['title']), 'Enter post title', true); ?>
            
            <?php 
            $author_options = ['' => 'Select an author'];
            if (isset($authors)) {
                foreach ($authors as $author) {
                    $author_options[$author['id']] = htmlspecialchars($author['name']) . ' (' . htmlspecialchars($author['email']) . ')';
                }
            }
            echo select_input('author_id', 'Author', $author_options, set_value('author_id', $post['author_id']), true);
            ?>
            
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
                                    <?php echo (in_array($tag['id'], array_column($post_tags ?? [], 'id'))) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tag['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="mt-1 text-sm text-gray-500">Hold Ctrl/Cmd to select multiple tags</p>
            </div>
            
            <?php echo textarea_input('content', 'Content', set_value('content', $post['content']), 'Write your post content here...', 12, true, ['class' => 'resize-y']); ?>
            
            <?php 
            $status_options = [
                'draft' => 'Draft',
                'published' => 'Published'
            ];
            echo select_input('status', 'Status', $status_options, set_value('status', $post['status']), true);
            ?>
            
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 px-4 py-2 text-sm bg-primary hover:bg-primary-dark text-white focus:ring-primary">
                    Update Post
                </button>
                <?php echo btn('Cancel', base_url('posts/view/' . $post['id']), 'outline', 'md', 'w-full sm:w-auto'); ?>
                <?php echo btn('Delete', base_url('posts/delete/' . $post['id']), 'danger', 'md', 'w-full sm:w-auto onclick="return confirm(\'Are you sure you want to delete this post?\')"'); ?>
            </div>
            
        <?php echo form_close(); ?>
    </div>
</div> 
