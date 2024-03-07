<?php defined('ABSPATH') || exit; ?>

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
    <rect fill="<?= esc_attr($background); ?>" width="64" height="64"/>
    <text fill="<?= esc_attr($color); ?>" alignment-baseline="middle" dominant-baseline="middle" dy=".1em" font-family="-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell,'Helvetica Neue',sans-serif" font-size="24px" font-weight="700" text-anchor="middle" x="50%" y="50%" ><?= esc_html($text); ?></text>
</svg>
