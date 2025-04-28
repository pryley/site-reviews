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

// Singleton flag to prevent multiple GLSR event listeners
let isListenerRegistered = false;

store('site-reviews/surecart-product-rating', {
  state: {
    get formattedText() {
      const context = getContext();
      return context.textTemplate?.replace('{num}', context.reviewCount || 0) || '0';
    },
  },
  actions: {
    scroll (ev) {
      if (scrollIntoView(ev.target.href)) {
        ev.preventDefault();
      }
    },
  },
  callbacks: {
    init() {
      if (isListenerRegistered) return;
      isListenerRegistered = true;
      const context = getContext();
      const { ref } = getElement();
      GLSR.Event.on('site-reviews/form/handle', ({ summary }) => {
        if (!summary || !ref) return;
        const newStars = new DOMParser()
          .parseFromString(summary, 'text/html')
          .body.querySelector('.glsr-star-rating');
        if (!newStars) return;
        const currentStars = ref.querySelector('.glsr-star-rating');
        if (!currentStars) return;
        const newReviewCount = newStars.dataset.reviews ?? '0';
        currentStars.replaceWith(newStars);
        context.reviewCount = parseInt(newReviewCount, 10) || 0;
        console.log(context)
      });
    },
  },
})
