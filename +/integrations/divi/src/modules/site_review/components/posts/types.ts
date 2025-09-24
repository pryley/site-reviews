import {
    type BlogPost,
    type Icon,
} from '@divi/types';


export interface PostsProps {
    headingLevel: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6';
    overlayIcon: Icon.Font.AttributeValue;
    moduleId: string;
    posts: BlogPost[];
    showOverlay: boolean;
    showReadMore: boolean;
    showThumbnail: boolean;
    showThumbnailWithWrapper: boolean;
    showAuthor: boolean;
    showDate: boolean;
    showCategories: boolean;
    showComments: boolean;
    isFullwidth?: boolean;
}
