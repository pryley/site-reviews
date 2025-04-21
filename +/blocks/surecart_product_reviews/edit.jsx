import { _x } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const innerBlocksTemplate = [
    [ 'core/group', {align:'wide',style:{spacing:{margin:{bottom:'30px'}}},layout:{type:'flex',flexWrap:'nowrap',justifyContent:'space-between',verticalAlignment:'bottom'}}, [
        [ 'core/heading', {content:'Customer Reviews',textAlign:'center',level:3,className:'is-style-default',style:{typography:{fontSize:'34px'}}} ],
    ] ],
    [ 'core/columns', {align:'wide',style:{spacing:{blockGap:{top:"30px",left:"60px"}}}}, [
        [ 'core/column', {width:"36%",style:{spacing:{blockGap:"0.75em"}}}, [
            [ 'core/group', {}, [
                [ 'site-reviews/summary', {assigned_posts:['post_id'],text:'From {num} customer reviews',labels:'5,4,3,2,1'} ],
            ] ],
            [ 'core/group', {}, [
                [ 'core/buttons', {fontSize:'medium',layout:{type:'flex',justifyContent:'space-between',flexWrap:'nowrap'}}, [
                    [ 'core/button', {className:'is-style-fill',line_items:[],text:'Write A Review',url:'#review-form'} ],
                ] ],
            ] ],
        ] ],
        [ 'core/column', {}, [
            [ 'core/group', {}, [
                [ 'site-reviews/reviews', {assigned_posts:['post_id'],id:'reviews-id',pagination:'ajax',schema:1} ],
            ] ],
            [ 'core/group', {anchor:'review-form',style:{spacing:{margin:{top:'40px'}}},layout:{type:'default'}}, [
                [ 'core/heading', {className:'is-style-text-subtitle',content:'Submit a Review',level:2} ],
                [ 'site-reviews/form', {assigned_posts:['post_id'],hide:['name','email'],reviews_id:'reviews-id'} ],
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
        layout: { type: 'constrained' }, // Explicitly set layout for InnerBlocks
    });

    return (
        <div {...innerBlocksProps}></div>
    );
}
