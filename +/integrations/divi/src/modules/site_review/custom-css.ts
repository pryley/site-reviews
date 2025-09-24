import { mapValues } from 'lodash';
import { __ } from '@wordpress/i18n';
import moduleMetaData from './module.json';

const { customCssFields } = moduleMetaData;

const labels: Record<string, string> = {
    content: __('Body', 'et_builder'),
    featuredImage: __('Featured Image', 'et_builder'),
    pagenavi: __('Pagenavi', 'et_builder'),
    postMeta: __('Post Meta', 'et_builder'),
    readMore: __('Read More Button', 'et_builder'),
    title: __('Title', 'et_builder'),
};

const cssFields = mapValues(customCssFields, (field, key) => ({
    ...field,
    label: labels[key],
}));

export { cssFields };
