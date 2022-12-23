<?php defined('WPINC') || die; ?>

<ul class="<?= $style; ?>">
    <?php foreach ($filters as $filter): ?>
        <li class="<?= $filter->classes; ?>">
            <a href="<?= $filter->permalink; ?>">
                <?= $filter->stars; ?>
                <span><?= $filter->count; ?></span>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
