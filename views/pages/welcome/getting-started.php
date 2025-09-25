<?php defined('ABSPATH') || exit; ?>

<div class="about__container is-fullwidth">

    <?php if (!empty($videos)) { ?>
    <div class="about__section is-fullwidth">
        <div class="glsr-videos is-responsive">
            <div class="glsr-videos__video">
                <?php
                    glsr()->render('views/partials/youtube', [
                        'youtube_bg' => glsr()->url('assets/images/video-cover.png'),
                        'youtube_id' => $videos[0]['id'],
                    ]);
                ?>
            </div>
            <div class="glsr-videos__playlist">
                <ul>
                    <?php foreach ($videos as $index => $video) { ?>
                        <?php
                            $digits = explode(':', $video['duration']);
                            $duration = sprintf(_x('%s minutes, %s seconds', 'admin-text', 'site-reviews'), glsr_get($digits, 0), glsr_get($digits, 1));
                        ?>
                        <li>
                            <a class="<?php echo (0 === $index) ? 'is-active' : ''; ?>" href="https://youtu.be/<?php echo $video['id']; ?>" data-id="<?php echo $video['id']; ?>" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><path fill="currentColor" d="M14.4 13.2h19.2c.66 0 1.2.54 1.2 1.2v19.2l-21.6-.024V14.4c0-.66.54-1.2 1.2-1.2zm4.8 1.2L16.8 18H18l2.4-3.6h-1.2zm4.8 0h-1.2L20.4 18h1.2l2.4-3.6zm3.6 0h-1.2L24 18h1.2l2.4-3.6zm3.6 0H30L27.6 18h1.2l2.4-3.6zm1.2 16.8v-12H15.6v12h16.8zM21.6 20.4l7.2 4.8-7.2 4.8v-9.6z"/></svg>
                                <span><?php echo $video['title']; ?></span>
                                <span aria-label="<?php echo $duration; ?>"><?php echo $video['duration']; ?></span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <p>These screen recordings (no audio yet, just video) demonstrate the basic features of Site Reviews on a demo website that collects film reviews. Feel free to use the same techniques and apply them to your website.</p>
    </div>
    <?php } ?>

    <div class="about__section has-2-columns is-wider-left is-fullwidth">
        <div class="column is-edge-to-edge">
            <h3>How To Add Reviews to Your Website</h3>
            <p>If you are using the WordPress blocks editor (also known as Gutenberg), simply add the Site Reviews blocks to your pages.</p>
            <p>If you are using Elementor, you can use the Site Reviews Elementor widgets.</p>
            <p>If you are using the Classic Editor or a page builder plugin like Divi Builder, use the Site Reviews shortcodes. Each shortcode has a bunch of options, and you can learn more about them on the <a href="<?php echo glsr_admin_url('documentation', 'shortcodes'); ?>">shortcodes documentation</a> page.</p>
            <p>You can assign reviews to any public page or post type on your website. To do this, use the <a href="<?php echo glsr_admin_url('documentation', 'shortcodes'); ?>"><code>assigned_posts</code></a> option on the shortcodes, or change the assignment options on the blocks or Elementor widgets. And you're not limited to pages; you can also assign reviews to Users and Categories!</p>
            <p>Site Reviews comes with tons of plugin settings. Did you know that any Site Reviews text visible to your website visitors can easily be changed? <a href="<?php echo glsr_admin_url('settings', 'strings'); ?>">Check it out</a>!</p>
        </div>
        <div class="column is-edge-to-edge">
            <img class="glsr-screenshot screenshot" src="<?php echo glsr()->url('assets/images/about/blocks.png'); ?>" alt="Editor Blocks" height="400" width="400" />
        </div>
    </div>

    <div class="about__section has-2-columns is-fullwidth">
        <div class="column is-edge-to-edge">
            <img class="glsr-screenshot screenshot" src="<?php echo glsr()->url('assets/images/about/reviews.png'); ?>" alt="Latest Reviews Screenshot" />
        </div>
        <div class="column is-edge-to-edge">
            <h3>Latest Reviews</h3>
            <p>This block shows your latest reviews. You can hide any of the fields, change the number of reviews displayed, and add pagination or a Load More button. The shortcode for the Latest Reviews is: <?php echo glsr_admin_link('documentation.shortcodes', '[site_reviews]', '#shortcode-site_reviews'); ?></p>
            <p>Site Reviews can generate Schema Markup to provide additional information about your reviews and ratings to search engines and your users. It can also integrate with other Schema and SEO plugins. Enable the schema option on the block or shortcode to generate the Schema Markup, and enable an integration on the <a href="<?php echo glsr_admin_url('settings', 'schema'); ?>">Settings page</a>.</p>
        </div>
    </div>

    <div class="about__section has-2-columns is-fullwidth">
        <div class="column is-edge-to-edge">
            <img class="glsr-screenshot screenshot" src="<?php echo glsr()->url('assets/images/about/summary.png'); ?>" alt="Rating Summary Screenshot" />
        </div>
        <div class="column is-edge-to-edge">
            <h3>Rating Summary</h3>
            <p>This block shows the rating summary of your reviews. You can hide any of the fields and change the text on the Settings page. The shortcode for the Rating Summary is: <?php echo glsr_admin_link('documentation.shortcodes', '[site_reviews_summary]', '#shortcode-site_reviews_summary'); ?></p>
        </div>
    </div>

    <div class="about__section has-2-columns is-fullwidth">
        <div class="column is-edge-to-edge">
            <img class="glsr-screenshot screenshot" src="<?php echo glsr()->url('assets/images/about/form.png'); ?>" alt="Rating Summary Screenshot" />
        </div>
        <div class="column is-edge-to-edge">
            <h3>Review Form</h3>
            <p>This block shows the review form. You can hide any of the fields and change the text on the Settings page. The shortcode for the Review Form is: <?php echo glsr_admin_link('documentation.shortcodes', '[site_reviews_form]', '#shortcode-site_reviews_form'); ?></p>
            <p>Site Reviews has built-in spam protection to help protect your site from pesky spammers and allows you to use popular spam fighting methods like Cloudflare Turnstile, Google reCAPTCHA, hCaptcha, FriendlyCaptcha, and Akismet. You can even limit reviews based on the email address, IP address, or username and require approval for all new review submissions. Enable these options and more on the <a href="<?php echo glsr_admin_url('settings', 'forms'); ?>">Settings page</a>.</p>
        </div>
    </div>
</div>
