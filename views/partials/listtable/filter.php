<?php defined('ABSPATH') || exit;

$choices = [];
foreach (($options ?? []) as $choiceId => $title) {
    $choices[] = [
        'id' => (string) $choiceId,
        'title' => html_entity_decode((string) $title, \ENT_QUOTES, 'UTF-8'),
    ];
}
$search = $search ?? '';
?>

<div id="<?php echo esc_attr($id); ?>" class="glsr-filter <?php echo esc_attr($class); ?>" role="combobox" aria-haspopup="true" aria-expanded="false" data-options="<?php echo esc_attr(wp_json_encode($choices)); ?>" data-search="<?php echo esc_attr($search); ?>">
    <input type="hidden" class="glsr-filter__value" 
        name="<?php echo esc_attr($name); ?>" 
        value="<?php echo esc_attr($value); ?>"
    />
    <span class="glsr-filter__selected" role="textbox" aria-readonly="true" tabindex="0" title="<?php echo esc_attr($selected); ?>"><?php echo esc_html($selected); ?></span>
    <div class="glsr-filter__dropdown">
<?php if ('' !== $search) { ?>
        <input type="search" class="glsr-filter__search"
            aria-autocomplete="list" aria-controls="<?php echo esc_attr($id); ?>-listbox" aria-label="<?php echo esc_attr_x('Search', 'admin-text', 'site-reviews'); ?>"
            autocapitalize="none" autocomplete="off" autocorrect="off" spellcheck="false"
            placeholder="<?php echo esc_attr_x('Search...', 'admin-text', 'site-reviews'); ?>"
            role="searchbox"
            tabindex="0"
        />
<?php } ?>
        <div id="<?php echo esc_attr($id); ?>-listbox" class="glsr-filter__results" role="listbox" aria-expanded="false" aria-hidden="true"></div>
    </div>
</div>
