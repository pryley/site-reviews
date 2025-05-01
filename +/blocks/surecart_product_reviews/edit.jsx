import { _x } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const innerBlocksTemplate = [
    [ 'core/group', {align:'wide',style:{spacing:{margin:{bottom:'30px'}}},layout:{type:'flex',flexWrap:'nowrap',justifyContent:'space-between',verticalAlignment:'bottom'}}, [
        [ 'core/heading', {content:'Customer Reviews',textAlign:'center',level:3,className:'is-style-default',style:{typography:{fontSize:'34px'}}} ],
    ] ],
    [ 'core/columns', {align:'wide',style:{spacing:{blockGap:{top:"30px",left:"60px"}}}}, [
        [ 'core/column', {style:{spacing:{blockGap:"var:preset|spacing|40"}}}, [
            [ 'core/group', {fontSize:"medium"}, [
                [ 'site-reviews/summary', {assigned_posts:['post_id'],id:'rating-summary-id',className:'is-style-3',labels:'5,4,3,2,1',styleMaxWidth:"50ch",styleBarSize:'50px',styleStarSize:'24px',text:'From {num} customer reviews'} ],
            ] ],
            [ 'core/group', {fontSize:"medium"}, [
                [ 'site-reviews/reviews', {assigned_posts:['post_id'],id:'reviews-id',pagination:'ajax',schema:1} ],
            ] ],
        ] ],
        [ 'core/column', {width:"36%",style:{spacing:{blockGap:"var:preset|spacing|40"}}}, [
            [ 'core/group', {anchor:'review-form',fontSize:"medium",style:{spacing:{padding:{top:"var:preset|spacing|40",bottom:"var:preset|spacing|40",left:"var:preset|spacing|30",right:"var:preset|spacing|30"}},border:{radius:"10px",color:"#9da4b030",width:"1px"}}}, [
                [ 'core/heading', {className:'is-style-text-subtitle',content:'Submit a Review',level:4} ],
                [ 'site-reviews/form', {assigned_posts:['post_id'],hide:['email'],reviews_id:'reviews-id',summary_id:'rating-summary-id'} ],
            ] ],
        ] ],
    ] ],
];

export default function edit (props) {
    const { context } = props;
    const { postId } = context;
    const blockProps = useBlockProps();
    const innerBlocksProps = useInnerBlocksProps(blockProps, {
        template: innerBlocksTemplate,
        templateLock: 'all',
    });

    return (
        <div {...innerBlocksProps}></div>
    );
}
