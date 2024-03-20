<?php defined('WPINC') || exit; ?>

<ul class="<?php echo esc_attr($style); ?>">
    <?php foreach ($filters as $filter) { ?>
        <li class="<?php echo esc_attr($filter->classes); ?>">
            <a href="<?php echo esc_url($filter->permalink); ?>">
                <?php echo $filter->stars; ?>
                <span><?php echo $filter->count; ?></span>
            </a>
        </li>
    <?php } ?>
</ul>
