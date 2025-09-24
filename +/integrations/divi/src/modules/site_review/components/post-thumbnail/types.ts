import {
    type BlogPost,
    type Icon,
} from '@divi/types';

export interface PostThumbnailProps {
    post: BlogPost;
    overlayIcon: Icon.Font.AttributeValue;
    showOverlay: boolean;
    hasWrapper: boolean;
}
