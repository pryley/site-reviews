import StarRating from 'star-rating.js';

const defaults = {
    classNames: {
        base: 'glsr-star-rating',
    },
    clearable: false,
    tooltip: false,
}

export default () => {
    let instance = null;
    const destroy = () => {
        if (instance) {
            instance.destroy()
            return true;
        }
        return false;
    }
    const init = (el, options = {}) => {
        if (!rebuild()) {
            instance = new StarRating(el, Object.assign({}, defaults, options));
        }
        return instance
    }
    const rebuild = () => {
        if (instance) {
            instance.rebuild()
            return true;
        }
        return false;
    }
    return { init, destroy, rebuild }
}
