<div class="max-w-2xl mx-auto">
    <?php if (validation_errors()): ?>
        <?php echo alert(validation_errors(), 'error'); ?>
    <?php endif; ?>

    <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Create New Author</h2>
            <p class="mt-1 text-sm text-gray-600">Add a new author to your blog.</p>
        </div>

        <?php echo form_open_multipart('authors/create', ['class' => 'space-y-6']); ?>
            
            <div>
                <?php echo form_label_tw('Name', 'name', true); ?>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="<?php echo set_value('name'); ?>" 
                       required
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                       placeholder="Enter author's full name">
            </div>
            
            <div>
                <?php echo form_label_tw('Email', 'email', true); ?>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="<?php echo set_value('email'); ?>" 
                       required
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                       placeholder="author@example.com">
            </div>
            
            <div>
                <?php echo form_label_tw('Bio', 'bio'); ?>
                <textarea name="bio" 
                          id="bio" 
                          rows="4"
                          class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary resize-y"
                          placeholder="Tell us about this author..."><?php echo set_value('bio'); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">A brief description of the author (optional).</p>
            </div>
            
            <div>
                <?php echo form_label_tw('Website', 'website'); ?>
                <input type="url" 
                       name="website" 
                       id="website" 
                       value="<?php echo set_value('website'); ?>"
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                       placeholder="https://example.com">
                <p class="mt-1 text-sm text-gray-500">Author's personal or professional website (optional).</p>
            </div>
            
            <div>
                <?php echo form_label_tw('Avatar', 'avatar'); ?>
                <input type="file" 
                       name="avatar" 
                       id="avatar" 
                       accept="image/*"
                       class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0 file:text-sm file:font-medium file:bg-primary file:text-white hover:file:bg-primary-dark">
                <p class="mt-1 text-sm text-gray-500">Upload a profile picture for the author (optional). JPG, PNG up to 2MB.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <?php echo form_label_tw('Status', 'status', true); ?>
                    <select name="status" 
                            id="status"
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                        <option value="active" <?php echo set_select('status', 'active', true); ?>>Active</option>
                        <option value="inactive" <?php echo set_select('status', 'inactive'); ?>>Inactive</option>
                    </select>
                </div>
                
                <div>
                    <?php echo form_label_tw('Posts per page', 'posts_per_page'); ?>
                    <input type="number" 
                           name="posts_per_page" 
                           id="posts_per_page" 
                           value="<?php echo set_value('posts_per_page', '10'); ?>" 
                           min="1" 
                           max="50"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                           placeholder="10">
                    <p class="mt-1 text-sm text-gray-500">Number of posts to display per page (1-50).</p>
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Social Media (Optional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <?php echo form_label_tw('Twitter', 'twitter'); ?>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">@</span>
                            </div>
                            <input type="text" 
                                   name="social_media[twitter]" 
                                   id="twitter" 
                                   value="<?php echo set_value('social_media[twitter]'); ?>"
                                   class="block w-full pl-8 rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                   placeholder="username">
                        </div>
                    </div>
                    
                    <div>
                        <?php echo form_label_tw('GitHub', 'github'); ?>
                        <input type="text" 
                               name="social_media[github]" 
                               id="github" 
                               value="<?php echo set_value('social_media[github]'); ?>"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                               placeholder="username">
                    </div>
                    
                    <div>
                        <?php echo form_label_tw('LinkedIn', 'linkedin'); ?>
                        <input type="text" 
                               name="social_media[linkedin]" 
                               id="linkedin" 
                               value="<?php echo set_value('social_media[linkedin]'); ?>"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                               placeholder="username">
                    </div>
                    
                    <div>
                        <?php echo form_label_tw('Instagram', 'instagram'); ?>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">@</span>
                            </div>
                            <input type="text" 
                                   name="social_media[instagram]" 
                                   id="instagram" 
                                   value="<?php echo set_value('social_media[instagram]'); ?>"
                                   class="block w-full pl-8 rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                   placeholder="username">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 px-4 py-2 text-sm bg-primary hover:bg-primary-dark text-white focus:ring-primary">
                    Create Author
                </button>
                <?php echo btn('Cancel', base_url('authors'), 'outline', 'md', 'w-full sm:w-auto'); ?>
            </div>
            
        <?php echo form_close(); ?>
    </div>
</div> 
