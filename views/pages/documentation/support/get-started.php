<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="support-get-started">
            <span class="title">Getting Started</span>
            <span class="badge code">Video Series</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="support-get-started" class="inside">
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">See also the basic "getting started" overview on the <a href="<?= glsr_admin_url('welcome'); ?>">Welcome to Site Reviews</a> page.</p>
        </div>
        <p>These screen recordings (no audio yet, just video) demonstrate the basic features of Site Reviews on a demo website that collects film reviews. Feel free to use the same techniques and apply them to your website.</p>
        <div class="glsr-videos">
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
                            <a class="<?= (0 === $index) ? 'is-active' : ''; ?>" href="https://youtu.be/<?= $video['id']; ?>" data-id="<?= $video['id']; ?>" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><path fill="currentColor" d="M14.4 13.2h19.2c.66 0 1.2.54 1.2 1.2v19.2l-21.6-.024V14.4c0-.66.54-1.2 1.2-1.2zm4.8 1.2L16.8 18H18l2.4-3.6h-1.2zm4.8 0h-1.2L20.4 18h1.2l2.4-3.6zm3.6 0h-1.2L24 18h1.2l2.4-3.6zm3.6 0H30L27.6 18h1.2l2.4-3.6zm1.2 16.8v-12H15.6v12h16.8zM21.6 20.4l7.2 4.8-7.2 4.8v-9.6z"/></svg>
                                <span><?= $video['title']; ?></span>
                                <span aria-label="<?= $duration; ?>"><?= $video['duration']; ?></span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
