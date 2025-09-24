import { type BlogMetadata } from '@divi/types';


export interface PaginationProps {
    metadata: BlogMetadata;
    paged: number;
    onChangePage: (page: number) => void;
}
