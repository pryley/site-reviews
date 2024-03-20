<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="fn-glsr_trace">
            <span class="title">Log a backtrace to the plugin console</span>
            <span class="badge code">glsr_trace()</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="fn-glsr_trace" class="inside">
        <div class="glsr-notice-inline components-notice is-warning">
            <p class="components-notice__content">This function will only work if the logging level is set to "Level 0: Debugging", you can change the logging level on the <a href="<?php echo glsr_admin_url('tools', 'console'); ?>">Console</a> page.</p>
        </div>
        <pre><code class="language-php">/**
 * @param int $limit
 * @return void
 */
glsr_trace($limit = 5);</code></pre>
        <p>This function logs a PHP backtrace to the plugin console. The default limit of stack frames logged is 5.</p>
        <p><strong>Example Usage:</strong></p>
        <pre><code class="language-php">glsr_trace(10);

// OR:

apply_filters('glsr_trace', null, 10);</code></pre>
    <p>The logged backtrace will be found in the <code><a href="<?php echo glsr_admin_url('tools', 'console'); ?>">Tools &rarr; Console</a></code>.</p>
    </div>
</div>
