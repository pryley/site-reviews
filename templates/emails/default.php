<?php defined('ABSPATH') || exit; ?>
<!doctype html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo wp_specialchars_decode((string) get_option('blogname', ''), ENT_QUOTES); ?></title>
    </head>
    <body>

        {{ message }}

    </body>
</html>
