<div class="glsr-card postbox open">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="true" aria-controls="support-get-started">
            <span class="title">Getting Started</span>
            <span class="badge code">Video Series</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="support-get-started" class="inside">
        <?php
            glsr()->render('views/partials/youtube', [
                'youtube_bg' => glsr()->url('assets/images/video.png'),
                'youtube_id' => 'PLn-nTn-jOuWnsMviIHjIMRIitM47aAanA',
            ]);
        ?>
        <p>Get introduced to Site Reviews by watching our "Getting Started" videos. These screen recordings demonstrate how to use Site Reviews on your website.</p>
    </div>
</div>
