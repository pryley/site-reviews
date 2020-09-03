<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="fn-glsr_get_option">
            <span class="title">Get a plugin setting</span>
            <span class="badge code">glsr_get_option()</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="fn-glsr_get_option" class="inside">
        <pre><code class="language-php">/**
 * @param string $path
 * @param mixed $fallback
 * @return string|array
 */
glsr_get_option($path = '', $fallback = '');</code></pre>
        <p>The <code>$path</code> variable is required and is the dot-notation path of the option you want to get. You build a dot-notation string by using the array keys leading up to the value you wish to get.</p>
        <p>The <code>$fallback</code> variable is what you want to return if the option is not found or is empty. Default is an empty string.</p>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="language-php">$requireApproval = glsr_get_option('general.require.approval', 'no');

// OR:

$requireApproval = apply_filters('glsr_get_option', 'no', 'general.require.approval', 'no');</code></pre>
        <p><strong>Helpful Tip:</strong></p>
        <p>You can use the <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-functions'); ?>" data-expand="#fn-glsr_debug">glsr_debug</a></code> helper function to view the whole plugin settings array, this will help you figure out which dot-notation path to use.</p>
        <pre><code class="language-php">glsr_debug(glsr_get_options());</code></pre>
    </div>
</div>
