import { type BlogPost } from '@divi/types';


export interface PostMetaProps {
    post: BlogPost;
    showAuthor: boolean;
    showDate: boolean;
    showCategories: boolean;
    showComments: boolean;
}
