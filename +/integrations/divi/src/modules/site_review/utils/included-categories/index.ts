import {
    find,
    map,
} from 'lodash';

import { select } from '@divi/data';

/**
 * Get included categories.
 *
 * @param {string} categories Categories.
 *
 * @returns {Array} - Included categories.
 */
export const includedCategories = (categories: string) => {
    const postCategories = select('divi/settings').getSetting(['taxonomy', 'postCategories']);

    // eslint-disable-next-line consistent-return
    const filterCategories = map(categories.split(',').filter(item => item !== ''), item => {
        if ('all' === item || 'current' === item) {
            return item;
        }
        const categoryExists = find(postCategories, ['term_id', Number(item)]);
        if (categoryExists) {
            return Number(item);
        }
    }).filter(Boolean).map(String);

    return filterCategories;
};
