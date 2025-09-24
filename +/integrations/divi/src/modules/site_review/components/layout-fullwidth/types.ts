import {
    type BlogPost,
    type Icon,
} from '@divi/types';

import { type PaginationProps } from '../pagination/types';


export interface LayoutFullwidthProps {
    headingLevel: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6';
    overlayIcon: Icon.Font.AttributeValue;
    pagination: PaginationProps;
    moduleId: string;
    posts: BlogPost[];
    showOverlay: boolean;
    showPagination: boolean;
    showReadMore: boolean;
    showThumbnail: boolean;
    showAuthor: boolean;
    showDate: boolean;
    showCategories: boolean;
    showComments: boolean;
}
