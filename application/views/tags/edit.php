<div class="content-wrapper">
    <div class="main-content">
        <div class="page-header">
            <h2>Edit Tag: <?php echo htmlspecialchars($tag['name']); ?></h2>
            <a href="<?php echo base_url('tags'); ?>" class="btn btn-secondary">Back to Tags</a>
        </div>

        <?php echo validation_errors('<div class="error-message">', '</div>'); ?>

        <?php echo form_open('tags/edit/' . $tag['id'], array('class' => 'form-horizontal')); ?>
            <div class="form-group">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" value="<?php echo set_value('name', $tag['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"><?php echo set_value('description', $tag['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="color">Color *</label>
                <div class="color-input-group">
                    <input type="color" id="color" name="color" value="<?php echo set_value('color', $tag['color']); ?>" required>
                    <span class="color-value"><?php echo set_value('color', $tag['color']); ?></span>
                </div>
                <div class="color-presets">
                    <div class="color-preset" data-color="#3498db" style="background-color: #3498db;" title="Blue"></div>
                    <div class="color-preset" data-color="#e74c3c" style="background-color: #e74c3c;" title="Red"></div>
                    <div class="color-preset" data-color="#2ecc71" style="background-color: #2ecc71;" title="Green"></div>
                    <div class="color-preset" data-color="#f39c12" style="background-color: #f39c12;" title="Orange"></div>
                    <div class="color-preset" data-color="#9b59b6" style="background-color: #9b59b6;" title="Purple"></div>
                    <div class="color-preset" data-color="#1abc9c" style="background-color: #1abc9c;" title="Teal"></div>
                    <div class="color-preset" data-color="#34495e" style="background-color: #34495e;" title="Dark Blue"></div>
                    <div class="color-preset" data-color="#e67e22" style="background-color: #e67e22;" title="Orange"></div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Update Tag</button>
                <a href="<?php echo base_url('tags'); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        <?php echo form_close(); ?>
    </div>

    <div class="sidebar">
        <div class="widget">
            <h4>Tag Info</h4>
            <div class="info-item">
                <strong>Slug:</strong> <?php echo htmlspecialchars($tag['slug']); ?>
            </div>
            <div class="info-item">
                <strong>Created:</strong> <?php echo date('M d, Y', strtotime($tag['created_at'])); ?>
            </div>
            <div class="info-item">
                <strong>Last Updated:</strong> <?php echo date('M d, Y', strtotime($tag['updated_at'])); ?>
            </div>
        </div>

        <div class="widget">
            <h4>Tips</h4>
            <ul>
                <li>Name and color are required fields</li>
                <li>Tag name must be unique</li>
                <li>Slug will be updated if name changes</li>
                <li>Choose a distinctive color</li>
                <li>Description helps users understand the tag's purpose</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorValue = document.querySelector('.color-value');
    const presets = document.querySelectorAll('.color-preset');

    // Update color value display
    colorInput.addEventListener('input', function() {
        colorValue.textContent = this.value;
    });

    // Handle preset clicks
    presets.forEach(preset => {
        preset.addEventListener('click', function() {
            const color = this.dataset.color;
            colorInput.value = color;
            colorValue.textContent = color;
        });
    });
});
</script> 
