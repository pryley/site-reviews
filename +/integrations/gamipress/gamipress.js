jQuery($ => {
    const keyRating = 'site_reviews_gamipress_rating';
    const keyRatingCondition = 'site_reviews_gamipress_rating_condition';
    const keyUserId = 'site_reviews_gamipress_user_id';

    const manageFields = $li => {
        const trigger = $li.find('.select-trigger-type').val();
        const userSelector = $li.find('select.' + keyUserId);
        const ratingConditionSelector = $li.find('select.' + keyRatingCondition);
        const isTrigger = !!~trigger.indexOf('site_reviews_gamipress');
        const isRatingTrigger = isTrigger && !!~['minimum','exact'].indexOf(ratingConditionSelector.val());

        toggleEl(ratingConditionSelector, isTrigger)
        toggleEl($li.find('input.' + keyRating), isRatingTrigger)
        toggleEl($li.find('.' + keyRating + '-text'), isRatingTrigger)

        if (isTrigger && !!~trigger.indexOf('/user_id')) {
            userSelector.show().data('trigger-type', trigger);
            if (userSelector.hasClass('select2-hidden-accessible')) {
                userSelector.val('').trigger('change').next().show();
            } else {
                userSelector.gamipress_select2({
                    ajax: {
                        data: (params) => ({
                            q: params.term,
                            page: params.page || 1,
                            action: 'site_reviews_gamipress/users',
                            nonce: gamipress_requirements_ui.nonce,
                            trigger_type: trigger,
                        }),
                        dataType: 'json',
                        delay: 250,
                        processResults: processUserResults,
                        type: 'POST',
                        url: ajaxurl,
                    },
                    allowClear: true,
                    escapeMarkup: markup => markup,
                    multiple: false,
                    placeholder: 'Select a User',
                    templateResult: templateUserResult,
                    theme: 'default gamipress-select2',
                });
            }
        } else {
            userSelector.hide();
            if (userSelector.hasClass('select2-hidden-accessible')) {
                userSelector.next().hide();
            }
        }
    }

    const processUserResults = (response) => {
        if (null === response) {
            return { results: [] };
        }
        const results = response.data.results ?? response.data;
        results.forEach(item => {
            item.id = item.ID;
            item.text = item.display_name+' (#'+item.ID+')';
        });
        return {
            pagination: {
                more: response.data.more_results ?? false,
            },
            results,
        };
    }

    const templateUserResult = (item) => {
        if (undefined === item.ID) {
            return item.text;
        }
        const sitename = item.site_name !== undefined ? ` (${item.site_name})` : '';
        return `<strong>${item.display_name}</strong><span class="result-description">ID: ${item.ID}<span class="align-right">User</span>${sitename}</span>`;
    }

    const toggleEl = (el, bool) => el[bool ? 'show' : 'hide']()

    $('.requirements-list').on('change', '.select-trigger-type, .' + keyRatingCondition, function () {
        manageFields($(this).closest('li'))
    });

    $('.requirements-list').on('update_requirement_data', '.requirement-row', function (ev, requirements, requirementEl) {
        const trigger = requirements.trigger_type;
        if (!!~trigger.indexOf('site_reviews_gamipress')) {
            requirements[keyRating] = requirementEl.find('input.' + keyRating).val() ?? '';
            requirements[keyRatingCondition] = requirementEl.find('select.' + keyRatingCondition).val() ?? '';
            requirements[keyUserId] = requirementEl.find('select.' + keyUserId).val() ?? '';
        }
    });

    $('.requirements-list li').each((i, el) => manageFields($(el)));
})
