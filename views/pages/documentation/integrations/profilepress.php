<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="integrations-profilepress">
            <span class="title has-logo">
                <?php 
                    echo \GeminiLabs\SiteReviews\Helpers\Svg::get('assets/images/icons/integrations/profilepress.svg', [
                        'fill' => 'currentColor',
                        'height' => 24,
                        'width' => 24,
                    ]);
                ?>
                ProfilePress
            </span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="integrations-profilepress" class="inside">
        <h3>Enable the ProfilePress integration</h3>
        <p>Go to the <code><?php echo glsr_admin_link(['settings', 'integrations', 'profilepress']); ?></code> page and enable the integration. After the integration is enabled, you will be able to add the "Rating" to the profiles in your Member Directory forms.</p>
        <h3>Edit the Member Directory form</h3>
        <p>The "Member Directory" form is used to display the members on your Member Directory page.</p>
        <ol>
            <li>Edit the <a href="<?php echo esc_url(admin_url('admin.php?page=ppress-directories')); ?>">Member Directory form</a> that is being used on your Member Directory page.</li>
            <li>Click the Rating field to add it to the form and drag it to the desired position.</li>
            <li><strong>Optional:</strong> Change the "Directory Settings > Sorting > Default Sorting method" setting to sort the directory by highest or lowest rating.</li>
            <li>Save the form.</li>
        </ol>
    </div>
</div>
