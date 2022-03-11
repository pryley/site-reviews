import AssignedTermsOptions from './assigned_terms';
import AssignedUsersOptions from './assigned_users';

const transformWidgetAttributes = (instance, attributes) => {
    const attr = { ...instance.raw }
    if (attr.hide) {
        attr.hide = attr.hide.join()
    }
    if (attr.rating) {
        attr.rating = Number(attr.rating)
    }
    if (!~['','post_id','parent_id'].indexOf(attr.assigned_posts)) {
        if (attributes.assign_to) {
            attr.assign_to = 'custom';
        } else {
            attr.assigned_to = 'custom';
        }
    } else {
        if (attributes.assign_to) {
            attr.assign_to = attr.assigned_posts;
        } else {
            attr.assigned_to = attr.assigned_posts;
        }
    }
    attr.user = attr.assigned_users;
    if (!~_.findIndex(AssignedUsersOptions, user => user.value == attr.assigned_users)) {
        attr.user = 'custom';
    }
    attr.category = attr.assigned_terms;
    if (!~_.findIndex(AssignedTermsOptions, term => term.value == attr.assigned_terms)) {
        attr.category = 'custom';
    }
    return attr
};

export default transformWidgetAttributes;
