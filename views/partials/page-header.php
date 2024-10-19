<?php defined('ABSPATH') || exit; ?>

<div id="glsr-page-header" class="glsr-page-header-wrap">
    <div class="glsr-page-header">
        <div class="glsr-page-icon"><?php echo $logo; ?></div>
        <h1 class="wp-heading-inline"><?php echo $title; ?></h1>
        <div>
            <?php
                foreach ($buttons as $button) {
                    echo glsr('Modules\Html\Builder')->a($button);
                }
            ?>
            <div>
                <?php if ($hasScreenOptions) { ?>
                    <button type="button" class="glsr-screen-meta-toggle components-button has-icon glsr-tooltip" aria-controls="screen-options-wrap" aria-label="<?php echo _x('Open the Screen Options', 'admin-text', 'site-reviews'); ?>" data-tippy-content="<?php echo _x('Screen Options', 'admin-text', 'site-reviews'); ?>" data-tippy-placement="bottom">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" aria-hidden="true" focusable="false"><path d="M14.5 13.8c-1.1 0-2.1.7-2.4 1.8H4V17h8.1c.3 1 1.3 1.8 2.4 1.8s2.1-.7 2.4-1.8H20v-1.5h-3.1c-.3-1-1.3-1.7-2.4-1.7zM11.9 7c-.3-1-1.3-1.8-2.4-1.8S7.4 6 7.1 7H4v1.5h3.1c.3 1 1.3 1.8 2.4 1.8s2.1-.7 2.4-1.8H20V7h-8.1z" /></svg>
                    </button>
                <?php } ?>
                <a class="components-button has-icon glsr-tooltip" href="<?php echo glsr_admin_url('welcome'); ?>" aria-label="<?php echo _x('Open the Getting Started page', 'admin-text', 'site-reviews'); ?>" data-tippy-content="<?php echo _x('Read the Getting Started Guide', 'admin-text', 'site-reviews'); ?>" data-tippy-placement="bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28"><path d="M22,12 C22,6.47142857 17.5142857,2 12,2 C6.47142857,2 2,6.47142857 2,12 C2,17.5285714 6.47142857,22 12,22 C17.5142857,22 22,17.5285714 22,12 Z M13,14.1142857 L10.7714286,14.1142857 L10.7714286,13.5 C10.7714286,12.9571429 10.8857143,12.5 11.1142857,12.1 C11.3428571,11.7 11.7714286,11.2857143 12.3714286,10.8285714 C12.9571429,10.4142857 13.3428571,10.0714286 13.5285714,9.81428571 C13.7285714,9.55714286 13.8142857,9.25714286 13.8142857,8.92857143 C13.8142857,8.57142857 13.6857143,8.3 13.4142857,8.1 C13.1428571,7.91428571 12.7714286,7.82857143 12.2857143,7.82857143 C11.4571429,7.82857143 10.5,8.1 9.42857143,8.64285714 L8.51428571,6.81428571 C9.75714286,6.11428571 11.0857143,5.75714286 12.4714286,5.75714286 C13.6285714,5.75714286 14.5428571,6.04285714 15.2142857,6.58571429 C15.9,7.14285714 16.2285714,7.88571429 16.2285714,8.8 C16.2285714,9.41428571 16.1,9.94285714 15.8142857,10.3857143 C15.5428571,10.8428571 15,11.3428571 14.2285714,11.9 C13.6857143,12.3 13.3571429,12.6 13.2142857,12.8 C13.0714286,13.0142857 13,13.2857143 13,13.6142857 L13,14.1142857 L13,14.1142857 Z M10.9,18.0285714 C10.6428571,17.7857143 10.5142857,17.4285714 10.5142857,16.9857143 C10.5142857,16.5142857 10.6285714,16.1571429 10.8857143,15.9142857 C11.1428571,15.6714286 11.5,15.5571429 11.9857143,15.5571429 C12.4428571,15.5571429 12.8,15.6857143 13.0571429,15.9285714 C13.3142857,16.1714286 13.4428571,16.5285714 13.4428571,16.9857143 C13.4428571,17.4142857 13.3142857,17.7714286 13.0571429,18.0142857 C12.8,18.2714286 12.4428571,18.4 11.9857143,18.4 C11.5142857,18.4 11.1571429,18.2714286 10.9,18.0285714 Z"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>
