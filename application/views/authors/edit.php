<div class="content-wrapper">
    <div class="main-content">
        <div class="page-header">
            <h2>Edit Author: <?php echo htmlspecialchars($author['name']); ?></h2>
            <a href="<?php echo base_url('authors'); ?>" class="btn btn-secondary">Back to Authors</a>
        </div>

        <?php echo validation_errors('<div class="error-message">', '</div>'); ?>

        <?php echo form_open('authors/edit/' . $author['id'], array('class' => 'form-horizontal')); ?>
            <div class="form-group">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" value="<?php echo set_value('name', $author['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?php echo set_value('email', $author['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="5"><?php echo set_value('bio', $author['bio']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="website">Website</label>
                <input type="url" id="website" name="website" value="<?php echo set_value('website', $author['website']); ?>" placeholder="https://example.com">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="active" <?php echo set_select('status', 'active', $author['status'] == 'active'); ?>>Active</option>
                    <option value="inactive" <?php echo set_select('status', 'inactive', $author['status'] == 'inactive'); ?>>Inactive</option>
                </select>
            </div>

            <div class="form-section">
                <h3>Social Media</h3>
                
                <?php 
                $social_media = is_array($author['social_media']) ? $author['social_media'] : [];
                ?>
                
                <div class="form-group">
                    <label for="twitter">Twitter</label>
                    <input type="text" id="twitter" name="twitter" value="<?php echo set_value('twitter', $social_media['twitter'] ?? ''); ?>" placeholder="@username">
                </div>

                <div class="form-group">
                    <label for="github">GitHub</label>
                    <input type="text" id="github" name="github" value="<?php echo set_value('github', $social_media['github'] ?? ''); ?>" placeholder="username">
                </div>

                <div class="form-group">
                    <label for="linkedin">LinkedIn</label>
                    <input type="text" id="linkedin" name="linkedin" value="<?php echo set_value('linkedin', $social_media['linkedin'] ?? ''); ?>" placeholder="username">
                </div>

                <div class="form-group">
                    <label for="dribbble">Dribbble</label>
                    <input type="text" id="dribbble" name="dribbble" value="<?php echo set_value('dribbble', $social_media['dribbble'] ?? ''); ?>" placeholder="username">
                </div>

                <div class="form-group">
                    <label for="behance">Behance</label>
                    <input type="text" id="behance" name="behance" value="<?php echo set_value('behance', $social_media['behance'] ?? ''); ?>" placeholder="username">
                </div>

                <div class="form-group">
                    <label for="instagram">Instagram</label>
                    <input type="text" id="instagram" name="instagram" value="<?php echo set_value('instagram', $social_media['instagram'] ?? ''); ?>" placeholder="@username">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Update Author</button>
                <a href="<?php echo base_url('authors'); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        <?php echo form_close(); ?>
    </div>

    <div class="sidebar">
        <div class="widget">
            <h4>Tips</h4>
            <ul>
                <li>Name and email are required fields</li>
                <li>Email must be unique</li>
                <li>Bio supports basic text formatting</li>
                <li>Social media fields are optional</li>
                <li>Status controls visibility</li>
            </ul>
        </div>
    </div>
</div> 
