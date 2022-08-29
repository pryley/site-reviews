<div class="glsr-card postbox is-fullwidth">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="upgrade-v6_0_0">
            <span class="title">Version 6.0.0</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="upgrade-v6_0_0" class="inside">
        <h2>Changes to plugin strings</h2>
        <p><em>Likelihood Of Impact: <span class="required">High</span></em></p>
        <p><strong>The "← Previous" and "Next →" strings were changed to "Previous" and "Next" (the arrows were removed).</strong></p>
        <p>If you have customised these strings in the settings, please <a href="<?= glsr_admin_url('settings', 'translations'); ?>">update them</a>.</p>

        <h2>Changes to review HTML</h2>
        <p><em>Likelihood Of Impact: <span class="required">High</span></em></p>
        <ol>
            <li>
                <p><strong>The dash has been removed from the author name when the avatar is hidden.</strong></p>
                <p>In earlier versions of Site Reviews, a "dash" character appeared in front of the author's name if you disabled avatars in the settings, hid the avatar, or changed the order of the review fields. If you want to add the dash back, please see the <a data-expand="#faq-add-name-dash" href="<?= glsr_admin_url('documentation', 'faq'); ?>">FAQ</a> help page.
            </li>
            <li>
                <p><strong>The review title HTML tag has been changed from &lt;h3&gt; to &lt;h4&gt;.</strong></p>
                <p>If you need to change it back to &lt;h3&gt;, please see the <a data-expand="#faq-change-review-title-tag" href="<?= glsr_admin_url('documentation', 'faq'); ?>">FAQ</a> help page.</p>
            </li>
            <li>
                <p><strong>The <code>.glsr-star-rating</code> class was removed from the star rating SELECT element.</strong></p>
                <p>Some themes transform SELECT elements in order to add custom styling to them. Since the star rating is controlled by a hidden SELECT element, these themes would make the hidden rating dropdown visible.</p>
                <p>Site Reviews now adds the following classes to the star rating select element: <code>.browser-default</code>, <code>.no_wrap</code>, <code>.no-wrap</code>; these classes are commonly used by themes to prevent them from transforming specific SELECT elements, so hopefully this will fix the issue for most themes.</p>
                <p>If you previously used the <code>.glsr-star-rating</code> selector with some custom javascript to prevent this from happening in your theme, please update the selector in your code with <code>.browser-default</code>, <code>.no_wrap</code>, or <code>.no-wrap</code>.</p>
            </li>
        </ol>

        <h2>Changes to plugin templates</h2>
        <p><em>Likelihood Of Impact: Medium</em></p>
        <ol>
            <li>
                <p><strong>The HTML markup of the <code>review.php</code> template has changed.</strong></p>
                <p>This template now includes the new <code>{{ verified }}</code> tag. If you copied this template file to your theme, please update it.</p>
            </li>
            <li>
                <p><strong>The HTML markup of the <code>form/submit-button.php</code> template has changed.</strong></p>
                <p>If you copied this template file to your theme, please update it.</p>
            </li>
            <li>
                <p><strong>The HTML markup of the <code>load-more-button.php</code> template has changed.</strong></p>
                <p>If you copied this template file to your theme, please update it.</p>
            </li>
        </ol>
    </div>
</div>
