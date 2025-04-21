import { store, getContext, getElement } from '@wordpress/interactivity';

const scrollIntoView = (href) => {
  const location = window.location;
  const url = new URL(href, location);
  if (url.origin === location.origin && url.pathname === location.pathname && url.hash) {
    const el = document.getElementById(url.hash.slice(1));
    if (el) {
      el.scrollIntoView({ behavior: 'smooth' });
      return true;
    }
  }
  return false;
};

store('site-reviews/surecart-product-rating', {
  actions: {
    *scroll (ev) {
      if (scrollIntoView(getElement().ref.href)) {
        ev.preventDefault()
      }
    },
  },
})
