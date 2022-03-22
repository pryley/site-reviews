<?php defined('ABSPATH') || die;
    if (empty($youtube_bg)) {
        $youtube_bg = sprintf('https://i.ytimg.com/vi/%s/maxresdefault.jpg', $youtube_id);
    }
?>
<div class="glsr-youtube" style="background-image: url(<?= $youtube_bg; ?>);">
    <button class='glsr-youtube-button' data-id="<?= $youtube_id; ?>" aria-label="Play">
        <svg viewBox="0 0 68 48" height="100%" width="100%">
            <path fill="#f00" class="glsr-youtube-button-bg" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z"/>
            <path fill="#fff" d="M 45,24 27,14 27,34"/>
        </svg>
    </button>
    <span class="glsr-youtube-overlay">
        <svg viewBox="0 0 500 200" preserveAspectRatio="none">
            <radialGradient id="gradient-<?= $youtube_id; ?>" cx=".5" cy="1.25" r="1.15">
                <stop offset="50%" stop-color="#000000"></stop>
                <stop offset="56%" stop-color="#0a0a0a"></stop>
                <stop offset="63%" stop-color="#262626"></stop>
                <stop offset="69%" stop-color="#4f4f4f"></stop>
                <stop offset="75%" stop-color="#808080"></stop>
                <stop offset="81%" stop-color="#b1b1b1"></stop>
                <stop offset="88%" stop-color="#dadada"></stop>
                <stop offset="94%" stop-color="#f6f6f6"></stop>
                <stop offset="100%" stop-color="#ffffff"></stop>
            </radialGradient>
            <mask id="mask-<?= $youtube_id; ?>">
                <rect x="0" y="0" width="500" height="200" fill="url(#gradient-<?= $youtube_id; ?>)"></rect>
            </mask>
            <rect style="height:100%;width:100%;" x="0" width="500" height="250" fill="currentColor" mask="url(#mask-<?= $youtube_id; ?>)"></rect>
        </svg>
    </span>
</div>
