<?php defined('WPINC') || die; ?>

<p>Hooks (also known as <a href="https://developer.wordpress.org/plugins/hooks/index.html">Actions and Filters</a>) are used in your theme's <code>functions.php</code> file to make changes to the plugin.</p>

<?php

include trailingslashit(__DIR__).'hooks/filter-form-field-order.php';
include trailingslashit(__DIR__).'hooks/filter-star-images.php';
include trailingslashit(__DIR__).'hooks/filter-scripts.php';
include trailingslashit(__DIR__).'hooks/filter-styles.php';
include trailingslashit(__DIR__).'hooks/filter-polyfill.php';
include trailingslashit(__DIR__).'hooks/do-something-after-submission.php';
include trailingslashit(__DIR__).'hooks/filter-schema.php';
include trailingslashit(__DIR__).'hooks/filter-submitted-review-values.php';
