<div class="max-w-2xl mx-auto">
    <?php if (validation_errors()): ?>
        <?php echo alert(validation_errors(), 'error'); ?>
    <?php endif; ?>

    <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Create New Tag</h2>
            <p class="mt-1 text-sm text-gray-600">Add a new tag to organize your blog posts.</p>
        </div>

        <?php echo form_open('tags/create', ['class' => 'space-y-6']); ?>
            
            <div>
                <?php echo form_label_tw('Name', 'name', true); ?>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="<?php echo set_value('name'); ?>" 
                       required
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                       placeholder="Enter tag name">
                <p class="mt-1 text-sm text-gray-500">The display name for this tag (e.g., "Technology", "Design").</p>
            </div>
            
            <div>
                <?php echo form_label_tw('Slug', 'slug'); ?>
                <input type="text" 
                       name="slug" 
                       id="slug" 
                       value="<?php echo set_value('slug'); ?>"
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                       placeholder="auto-generated">
                <p class="mt-1 text-sm text-gray-500">URL-friendly version of the name. Leave blank to auto-generate.</p>
            </div>
            
            <div>
                <?php echo form_label_tw('Description', 'description'); ?>
                <textarea name="description" 
                          id="description" 
                          rows="3"
                          class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary resize-y"
                          placeholder="Describe what this tag represents..."><?php echo set_value('description'); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">A brief description of what this tag represents (optional).</p>
            </div>
            
            <div>
                <?php echo form_label_tw('Color', 'color', true); ?>
                <div class="flex items-center space-x-3">
                    <input type="color" 
                           name="color" 
                           id="color" 
                           value="<?php echo set_value('color', '#007cba'); ?>"
                           class="h-10 w-20 rounded border border-gray-300 cursor-pointer focus:ring-2 focus:ring-primary focus:border-primary">
                    <input type="text" 
                           name="color_hex" 
                           id="color_hex" 
                           value="<?php echo set_value('color', '#007cba'); ?>"
                           class="flex-1 rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                           placeholder="#007cba"
                           pattern="^#[0-9A-Fa-f]{6}$">
                </div>
                <p class="mt-1 text-sm text-gray-500">Choose a color to represent this tag visually.</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Preview</h3>
                <div class="flex items-center space-x-3">
                    <span id="tag-preview" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                          style="background-color: #007cba;">
                        <span id="preview-name">Tag Name</span>
                    </span>
                    <span class="text-sm text-gray-500">This is how your tag will appear</span>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 px-4 py-2 text-sm bg-primary hover:bg-primary-dark text-white focus:ring-primary">
                    Create Tag
                </button>
                <?php echo btn('Cancel', base_url('tags'), 'outline', 'md', 'w-full sm:w-auto'); ?>
            </div>
            
        <?php echo form_close(); ?>
    </div>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('slug').value = slug;
    
    // Update preview
    document.getElementById('preview-name').textContent = name || 'Tag Name';
});

// Sync color picker with hex input
document.getElementById('color').addEventListener('change', function() {
    document.getElementById('color_hex').value = this.value;
    document.getElementById('tag-preview').style.backgroundColor = this.value;
});

document.getElementById('color_hex').addEventListener('input', function() {
    if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
        document.getElementById('color').value = this.value;
        document.getElementById('tag-preview').style.backgroundColor = this.value;
    }
});
</script> 
