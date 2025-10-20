import { omit } from 'lodash';
import { addAction, addFilter } from '@wordpress/hooks';
// @ts-expect-error: '@divi/module-library' lacks 'registerFolder' export in types
import { registerFolder, registerModule } from '@divi/module-library';
import { SiteReviews } from './modules/site_reviews';
import { SiteReviewsForm } from './modules/site_reviews_form';
import {
  iconForm,
  iconLogo,
  iconReview,
  iconReviews,
  iconSummary,
} from './icons';

// Add module icons to the icon library.
addFilter('divi.iconLibrary.icon.map', 'site-reviews/divi', (icons) => {
  return {
    ...icons,
    [iconForm.name]: iconForm,
    [iconLogo.name]: iconLogo,
    [iconReview.name]: iconReview,
    [iconReviews.name]: iconReviews,
    [iconSummary.name]: iconSummary,
  };
});

// Register modules.
addAction('divi.moduleLibrary.registerModuleLibraryStore.after', 'site-reviews/divi', () => {
  registerFolder({
    category: 'module',
    icon: iconLogo.name,
    name: 'site-reviews',
    path: '',
    title: 'Site Reviews',
  })
  registerModule(SiteReviews.metadata, omit(SiteReviews, 'metadata'));
  registerModule(SiteReviewsForm.metadata, omit(SiteReviewsForm, 'metadata'));
});
