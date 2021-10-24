<?php defined('ABSPATH') || die;

$table = glsr('Overrides\ScheduledActionsTable');
$table->process_actions();
$table->display_page();
