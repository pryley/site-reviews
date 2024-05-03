<?php defined('ABSPATH') || exit; ?>

<div class="glsr-welcome wrap about-wrap about-wrap-content">
    <h1>Welcome to <?php echo glsr()->name; ?></h1>
    <div class="glsr-about-text about-text">Site Reviews is a free WordPress review plugin with advanced features that makes it easy to manage reviews on your website. Follow the instructions below to get started!</div>
    <div class="badge">Version <?php echo glsr()->version; ?></div>
    <p class="about-buttons">
        <a class="components-button is-primary dashicon dashicons-book" href="<?php echo glsr_admin_url('documentation'); ?>">Read the Documentation</a>
        <a class="components-button is-secondary dashicon dashicons-facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A//wordpress.org/plugins/site-reviews/">Share</a>
        <a class="components-button is-secondary dashicon dashicons-twitter" target="_blank" href="https://twitter.com/intent/tweet?text=Site Reviews is a fantastic WordPress review plugin with advanced features that makes it easy to manage reviews on your website.&url=https://wordpress.org/plugins/site-reviews/&hashtags=WordPress,reviewplugins,">Tweet</a>
    </p>
    <nav class="glsr-nav-tab-wrapper nav-tab-wrapper">
        <?php foreach ($tabs as $id => $title) : ?>
        <a class="glsr-nav-tab nav-tab" data-id="<?php echo $id; ?>" href="<?php echo glsr_admin_url('welcome', $id); ?>" tabindex="0"><?php echo $title; ?></a>
        <?php endforeach; ?>
    </nav>
    <?php foreach ($tabs as $id => $title) : ?>
    <div class="glsr-nav-view ui-tabs-hide" id="<?php echo $id; ?>">
        <?php glsr('Modules\Html\Template')->render("pages/welcome/{$id}", $data); ?>
    </div>
    <?php endforeach; ?>
    <input type="hidden" name="_active_tab">
    <input type="hidden" name="_wp_http_referer" value="<?php echo $http_referer; ?>">
</div>
