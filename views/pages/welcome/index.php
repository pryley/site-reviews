<?php defined('WPINC') || die; ?>

<div class="glsr-welcome wrap about-wrap about-wrap-content">
    <h1>Welcome to <?= glsr()->name; ?></h1>
    <div class="about-text">Site Reviews is a free WordPress review plugin with advanced features that makes it easy to manage reviews on your website. Follow the instructions below to get started!</div>
    <div class="wp-badge">Version <?= glsr()->version; ?></div>
    <p class="about-buttons">
        <a class="button" href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation'); ?>">Documentation</a>
        <a class="button" href="https://wordpress.org/support/plugin/site-reviews/">Support</a>
        <a target="_blank" class="button" href="https://www.facebook.com/sharer/sharer.php?u=https%3A//geminilabs.io/site-reviews"><span class="dashicons dashicons-facebook-alt"></span> Share</a>
        <a target="_blank" class="button" href="https://twitter.com/intent/tweet?text=Site Reviews is a fantastic WordPress review plugin with advanced features that makes it easy to manage reviews on your website.&url=https://geminilabs.io/site-reviews&hashtags=WordPress,reviewplugins,"><span class="dashicons dashicons-twitter"></span> Tweet</a>
    </p>
    <nav class="glsr-nav-tab-wrapper nav-tab-wrapper">
        <?php foreach ($tabs as $id => $title) : ?>
        <a class="glsr-nav-tab nav-tab" href="#<?= $id; ?>"><?= $title; ?></a>
        <?php endforeach; ?>
    </nav>
    <?php foreach ($tabs as $id => $title) : ?>
    <div class="glsr-nav-view ui-tabs-hide" id="<?= $id; ?>">
        <?php $template->render('pages/welcome/'.$id, $data); ?>
    </div>
    <?php endforeach; ?>
    <input type="hidden" name="_active_tab">
    <input type="hidden" name="_wp_http_referer" value="<?= $http_referer; ?>">
</div>
