/**
 * @site-reviews/reviews: the review lists, their pagination and excerpts, the summaries, and
 * GLSR.Review and GLSR.Summary.
 */
import { attach } from '@/public/core/index.js';
import Review, { initModal } from './review.js';
import Summary from './summary.js';

window.GLSR.Review = Review;
window.GLSR.Summary = Summary;

attach('reviews', (root) => {
    Review.init(root)
    Summary.init(root)
})

export { initModal, Review, Summary }
