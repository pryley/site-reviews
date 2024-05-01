<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="upgrade-v6_0_0">
            <span class="title">Version 6.0</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="upgrade-v6_0_0" class="inside">

        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content">
                Site Reviews should automatically migrate itself after updating to v6.0. However, if you are experiencing problems after updating, you may need to manually run the <a href="<?php echo glsr_admin_url('tools', 'general'); ?>" data-expand="#tools-migrate-plugin">Migrate Plugin</a> tool.
            </p>
        </div>

        <h2>Changes to Review HTML</h2>
        <p><em>Likelihood Of Impact: <span class="impact-high">High</span></em></p>
        <ol>
            <li>
                <p><strong>The dash has been removed from the author name when the avatar is hidden.</strong></p>
                <p>In earlier versions of Site Reviews, a "dash" character appeared in front of the author's name if you disabled avatars in the settings, hid the avatar, or changed the order of the review fields.</p>
                <p>If you want to add the dash back, please see the <a data-expand="#faq-add-name-dash" href="<?php echo glsr_admin_url('documentation', 'faq'); ?>">FAQ</a> help page.
            </li>
            <li>
                <p><strong>The review title HTML tag has been changed from &lt;h3&gt; to &lt;h4&gt;.</strong></p>
                <p>If you need to change it back to &lt;h3&gt;, please see the <a data-expand="#faq-change-review-title-tag" href="<?php echo glsr_admin_url('documentation', 'faq'); ?>">FAQ</a> help page.</p>
            </li>
            <li>
                <p><strong>The <code>.glsr-star-rating</code> class was removed from the star rating SELECT element.</strong></p>
                <p>Some themes transform SELECT elements in order to add custom styling to them. Since the star rating is controlled by a hidden SELECT element, these themes would make the hidden rating dropdown visible.</p>
                <p>Site Reviews now adds the following classes to the star rating select element: <code>.browser-default</code>, <code>.no_wrap</code>, <code>.no-wrap</code>; these classes are commonly used by themes to prevent them from transforming specific SELECT elements, so hopefully this will fix the issue for most themes.</p>
                <p>If you previously used the <code>.glsr-star-rating</code> selector with some custom javascript to prevent this from happening in your theme, please update the selector in your code with <code>.browser-default</code>, <code>.no_wrap</code>, or <code>.no-wrap</code>.</p>
            </li>
        </ol>

        <h2>Changes to Plugin Strings</h2>
        <p><em>Likelihood Of Impact: <span class="impact-medium">Medium</span></em></p>
        <ol>
            <li>
                <p><strong>The "← Previous" and "Next →" strings were changed to "Previous" and "Next" (the arrows were removed).</strong></p>
                <p>If you have customised these strings in the settings, please <a href="<?php echo glsr_admin_url('settings', 'strings'); ?>">update them</a>.</p>
            </li>
        </ol>

        <h2>Changes to Plugin Templates</h2>
        <p><em>Likelihood Of Impact: <span class="impact-medium">Medium</span></em></p>
        <ol>
            <li>
                <p><strong>The HTML markup of the <code>form/submit-button.php</code> template has changed.</strong></p>
                <p>If you copied this template file to your theme, please update it.</p>
            </li>
            <li>
                <p><strong>The HTML markup of the <code>load-more-button.php</code> template has changed.</strong></p>
                <p>If you copied this template file to your theme, please update it.</p>
            </li>
            <li>
                <p><strong>The HTML markup of the <code>pagination.php</code> template has changed.</strong></p>
                <p>If you copied this template file to your theme, please edit it and remove the <code>{{ loader }}</code> tag which is no longer used.</p>
            </li>
            <li>
                <p><strong>The HTML markup of the <code>review.php</code> template has changed.</strong></p>
                <p>This template now includes the new <code>{{ verified }}</code> tag. If you copied this template file to your theme, please update it.</p>
            </li>
        </ol>

        <h2>Changes to Javascript events</h2>
        <p><em>Likelihood Of Impact: <span class="impact-low">Low</span></em></p>
        <ol>
            <li>
                <p><strong>The <code>site-reviews/after/submission</code> event has been removed.</strong></p>
                <p>You should use the "site-reviews/form/handle" event instead. Here is an example:</p>
                <pre><code class="language-js">GLSR.Event.on('site-reviews/form/handle', (response, form) => {
    // do something here
});</code></pre>
            </li>
        </ol>

        <h2>Changes to Internal Plugin Classes</h2>
        <p><em>Likelihood Of Impact: <span class="impact-low">Low</span></em></p>
        <ol>
            <li>
                <p><strong>The parameter order of the <code>Str::contains</code> method has changed.</strong></p>
                <p>If you were using the <code>Str::contains($needle, $haystack)</code> method in custom code or in a filter or action hook, you will need to swap the parameter order to <code>Str::contains($haystack, $needle)</code>. This change was made to mirror the native PHP 8.0 <a href="http://www.php.net/manual/en/function.str-contains.php" target="_blank">str_contains</a> function.</p>
            </li>
            <li>
                <p><strong>The parameter order of the <code>Str::endsWith</code> method has changed.</strong></p>
                <p>If you were using the <code>Str::endsWith($needle, $haystack)</code> method in custom code or in a filter or action hook, you will need to swap the parameter order to <code>Str::endsWith($haystack, $needle)</code>. This change was made to mirror the native PHP 8.0 <a href="http://www.php.net/manual/en/function.str-ends-with.php" target="_blank">str_ends_with</a> function.</p>
            </li>
            <li>
                <p><strong>The parameter order of the <code>Str::startsWith</code> method has changed.</strong></p>
                <p>If you were using the <code>Str::startsWith($needle, $haystack)</code> method in custom code or in a filter or action hook, you will need to swap the parameter order to <code>Str::startsWith($haystack, $needle)</code>. This change was made to mirror the native PHP 8.0 <a href="http://www.php.net/manual/en/function.str-starts-with.php" target="_blank">str_starts_with</a> function.</p>
            </li>
        </ol>

    </div>
</div>
