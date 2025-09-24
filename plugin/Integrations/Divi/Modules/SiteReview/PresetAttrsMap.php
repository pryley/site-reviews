<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Modules\SiteReview;

class PresetAttrsMap
{
    public static function get_map(array $map, string $module_name): array
    {
        if ('glsr-divi/blog' !== $module_name) {
            return $map;
        }
        return [
            'post.advanced.type' => [
                'attrName' => 'post.advanced.type',
                'preset' => 'content',
            ],
            'post.advanced.number' => [
                'attrName' => 'post.advanced.number',
                'preset' => 'content',
            ],
            'post.advanced.categories' => [
                'attrName' => 'post.advanced.categories',
                'preset' => 'content',
            ],
            'post.advanced.dateFormat' => [
                'attrName' => 'post.advanced.dateFormat',
                'preset' => 'content',
            ],
            'post.advanced.excerptContent' => [
                'attrName' => 'post.advanced.excerptContent',
                'preset' => 'content',
            ],
            'post.advanced.excerptManual' => [
                'attrName' => 'post.advanced.excerptManual',
                'preset' => 'content',
            ],
            'post.advanced.excerptLength' => [
                'attrName' => 'post.advanced.excerptLength',
                'preset' => 'content',
            ],
            'post.advanced.offset' => [
                'attrName' => 'post.advanced.offset',
                'preset' => 'content',
            ],
            'image.advanced.enable' => [
                'attrName' => 'image.advanced.enable',
                'preset' => ['html'],
            ],
            'readMore.advanced.enable' => [
                'attrName' => 'readMore.advanced.enable',
                'preset' => ['html'],
            ],
            'meta.advanced.showAuthor' => [
                'attrName' => 'meta.advanced.showAuthor',
                'preset' => ['html'],
            ],
            'meta.advanced.showDate' => [
                'attrName' => 'meta.advanced.showDate',
                'preset' => ['html'],
            ],
            'meta.advanced.showCategories' => [
                'attrName' => 'meta.advanced.showCategories',
                'preset' => ['html'],
            ],
            'meta.advanced.showComments' => [
                'attrName' => 'meta.advanced.showComments',
                'preset' => ['html'],
            ],
            'post.advanced.showExcerpt' => [
                'attrName' => 'post.advanced.showExcerpt',
                'preset' => ['html'],
            ],
            'pagination.advanced.enable' => [
                'attrName' => 'pagination.advanced.enable',
                'preset' => ['html'],
            ],
            'module.advanced.link__url' => [
                'attrName' => 'module.advanced.link',
                'preset' => 'content',
                'subName' => 'url',
            ],
            'module.advanced.link__target' => [
                'attrName' => 'module.advanced.link',
                'preset' => 'content',
                'subName' => 'target',
            ],
            'masonry.decoration.background__color' => [
                'attrName' => 'masonry.decoration.background',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'module.decoration.background__color' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'module.decoration.background__gradient.stops' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'gradient.stops',
            ],
            'module.decoration.background__gradient.enabled' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'gradient.enabled',
            ],
            'module.decoration.background__gradient.type' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'gradient.type',
            ],
            'module.decoration.background__gradient.direction' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'gradient.direction',
            ],
            'module.decoration.background__gradient.directionRadial' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'gradient.directionRadial',
            ],
            'module.decoration.background__gradient.repeat' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'gradient.repeat',
            ],
            'module.decoration.background__gradient.length' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'gradient.length',
            ],
            'module.decoration.background__gradient.overlaysImage' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'gradient.overlaysImage',
            ],
            'module.decoration.background__image.url' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'image.url',
            ],
            'module.decoration.background__image.parallax.enabled' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'script', 'style'],
                'subName' => 'image.parallax.enabled',
            ],
            'module.decoration.background__image.parallax.method' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'image.parallax.method',
            ],
            'module.decoration.background__image.size' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'image.size',
            ],
            'module.decoration.background__image.width' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'image.width',
            ],
            'module.decoration.background__image.height' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'image.height',
            ],
            'module.decoration.background__image.position' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'image.position',
            ],
            'module.decoration.background__image.horizontalOffset' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'image.horizontalOffset',
            ],
            'module.decoration.background__image.verticalOffset' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'image.verticalOffset',
            ],
            'module.decoration.background__image.repeat' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'image.repeat',
            ],
            'module.decoration.background__image.blend' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'image.blend',
            ],
            'module.decoration.background__video.mp4' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html'],
                'subName' => 'video.mp4',
            ],
            'module.decoration.background__video.webm' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html'],
                'subName' => 'video.webm',
            ],
            'module.decoration.background__video.width' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html'],
                'subName' => 'video.width',
            ],
            'module.decoration.background__video.height' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html'],
                'subName' => 'video.height',
            ],
            'module.decoration.background__video.allowPlayerPause' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html'],
                'subName' => 'video.allowPlayerPause',
            ],
            'module.decoration.background__video.pauseOutsideViewport' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html'],
                'subName' => 'video.pauseOutsideViewport',
            ],
            'module.decoration.background__pattern.style' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'pattern.style',
            ],
            'module.decoration.background__pattern.enabled' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'pattern.enabled',
            ],
            'module.decoration.background__pattern.color' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'pattern.color',
            ],
            'module.decoration.background__pattern.transform' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'pattern.transform',
            ],
            'module.decoration.background__pattern.size' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'pattern.size',
            ],
            'module.decoration.background__pattern.width' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'pattern.width',
            ],
            'module.decoration.background__pattern.height' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'pattern.height',
            ],
            'module.decoration.background__pattern.repeatOrigin' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'pattern.repeatOrigin',
            ],
            'module.decoration.background__pattern.horizontalOffset' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'pattern.horizontalOffset',
            ],
            'module.decoration.background__pattern.verticalOffset' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'pattern.verticalOffset',
            ],
            'module.decoration.background__pattern.repeat' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'pattern.repeat',
            ],
            'module.decoration.background__pattern.blend' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'pattern.blend',
            ],
            'module.decoration.background__mask.style' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'mask.style',
            ],
            'module.decoration.background__mask.enabled' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['html', 'style'],
                'subName' => 'mask.enabled',
            ],
            'module.decoration.background__mask.color' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'mask.color',
            ],
            'module.decoration.background__mask.transform' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'mask.transform',
            ],
            'module.decoration.background__mask.aspectRatio' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'mask.aspectRatio',
            ],
            'module.decoration.background__mask.size' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'mask.size',
            ],
            'module.decoration.background__mask.width' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'mask.width',
            ],
            'module.decoration.background__mask.height' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'mask.height',
            ],
            'module.decoration.background__mask.position' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'mask.position',
            ],
            'module.decoration.background__mask.horizontalOffset' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'mask.horizontalOffset',
            ],
            'module.decoration.background__mask.verticalOffset' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'mask.verticalOffset',
            ],
            'module.decoration.background__mask.blend' => [
                'attrName' => 'module.decoration.background',
                'preset' => ['style'],
                'subName' => 'mask.blend',
            ],
            'module.meta.adminLabel' => [
                'attrName' => 'module.meta.adminLabel',
                'preset' => 'meta',
            ],
            'fullwidth.advanced.enable' => [
                'attrName' => 'fullwidth.advanced.enable',
                'preset' => ['style'],
            ],
            'overlay.advanced.enable' => [
                'attrName' => 'overlay.advanced.enable',
                'preset' => ['style'],
            ],
            'overlayIcon.decoration.icon__color' => [
                'attrName' => 'overlayIcon.decoration.icon',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'overlay.decoration.background__color' => [
                'attrName' => 'overlay.decoration.background',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'overlayIcon.decoration.icon' => [
                'attrName' => 'overlayIcon.decoration.icon',
                'preset' => ['style'],
            ],
            'image.decoration.border__radius' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'radius',
            ],
            'image.decoration.border__styles' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles',
            ],
            'image.decoration.border__styles.all.width' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.all.width',
            ],
            'image.decoration.border__styles.top.width' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.top.width',
            ],
            'image.decoration.border__styles.right.width' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.right.width',
            ],
            'image.decoration.border__styles.bottom.width' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.bottom.width',
            ],
            'image.decoration.border__styles.left.width' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.left.width',
            ],
            'image.decoration.border__styles.all.color' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.all.color',
            ],
            'image.decoration.border__styles.top.color' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.top.color',
            ],
            'image.decoration.border__styles.right.color' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.right.color',
            ],
            'image.decoration.border__styles.bottom.color' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.bottom.color',
            ],
            'image.decoration.border__styles.left.color' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.left.color',
            ],
            'image.decoration.border__styles.all.style' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.all.style',
            ],
            'image.decoration.border__styles.top.style' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.top.style',
            ],
            'image.decoration.border__styles.right.style' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.right.style',
            ],
            'image.decoration.border__styles.bottom.style' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.bottom.style',
            ],
            'image.decoration.border__styles.left.style' => [
                'attrName' => 'image.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.left.style',
            ],
            'image.decoration.boxShadow__style' => [
                'attrName' => 'image.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'style',
            ],
            'image.decoration.boxShadow__horizontal' => [
                'attrName' => 'image.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'horizontal',
            ],
            'image.decoration.boxShadow__vertical' => [
                'attrName' => 'image.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'vertical',
            ],
            'image.decoration.boxShadow__blur' => [
                'attrName' => 'image.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'blur',
            ],
            'image.decoration.boxShadow__spread' => [
                'attrName' => 'image.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'spread',
            ],
            'image.decoration.boxShadow__color' => [
                'attrName' => 'image.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'color',
            ],
            'image.decoration.boxShadow__position' => [
                'attrName' => 'image.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'position',
            ],
            'image.decoration.filters__hueRotate' => [
                'attrName' => 'image.decoration.filters',
                'preset' => ['style'],
                'subName' => 'hueRotate',
            ],
            'image.decoration.filters__saturate' => [
                'attrName' => 'image.decoration.filters',
                'preset' => ['style'],
                'subName' => 'saturate',
            ],
            'image.decoration.filters__brightness' => [
                'attrName' => 'image.decoration.filters',
                'preset' => ['style'],
                'subName' => 'brightness',
            ],
            'image.decoration.filters__contrast' => [
                'attrName' => 'image.decoration.filters',
                'preset' => ['style'],
                'subName' => 'contrast',
            ],
            'image.decoration.filters__invert' => [
                'attrName' => 'image.decoration.filters',
                'preset' => ['style'],
                'subName' => 'invert',
            ],
            'image.decoration.filters__sepia' => [
                'attrName' => 'image.decoration.filters',
                'preset' => ['style'],
                'subName' => 'sepia',
            ],
            'image.decoration.filters__opacity' => [
                'attrName' => 'image.decoration.filters',
                'preset' => ['style'],
                'subName' => 'opacity',
            ],
            'image.decoration.filters__blur' => [
                'attrName' => 'image.decoration.filters',
                'preset' => ['style'],
                'subName' => 'blur',
            ],
            'image.decoration.filters__blendMode' => [
                'attrName' => 'image.decoration.filters',
                'preset' => ['style'],
                'subName' => 'blendMode',
            ],
            'module.advanced.text.text__orientation' => [
                'attrName' => 'module.advanced.text.text',
                'preset' => ['html'],
                'subName' => 'orientation',
            ],
            'module.advanced.text.text__color' => [
                'attrName' => 'module.advanced.text.text',
                'preset' => ['html'],
                'subName' => 'color',
            ],
            'module.advanced.text.textShadow__style' => [
                'attrName' => 'module.advanced.text.textShadow',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'module.advanced.text.textShadow__horizontal' => [
                'attrName' => 'module.advanced.text.textShadow',
                'preset' => ['style'],
                'subName' => 'horizontal',
            ],
            'module.advanced.text.textShadow__vertical' => [
                'attrName' => 'module.advanced.text.textShadow',
                'preset' => ['style'],
                'subName' => 'vertical',
            ],
            'module.advanced.text.textShadow__blur' => [
                'attrName' => 'module.advanced.text.textShadow',
                'preset' => ['style'],
                'subName' => 'blur',
            ],
            'module.advanced.text.textShadow__color' => [
                'attrName' => 'module.advanced.text.textShadow',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'title.decoration.font.font__headingLevel' => [
                'attrName' => 'title.decoration.font.font',
                'preset' => ['html'],
                'subName' => 'headingLevel',
            ],
            'title.decoration.font.font__family' => [
                'attrName' => 'title.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'family',
            ],
            'title.decoration.font.font__weight' => [
                'attrName' => 'title.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'weight',
            ],
            'title.decoration.font.font__style' => [
                'attrName' => 'title.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'title.decoration.font.font__lineColor' => [
                'attrName' => 'title.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'lineColor',
            ],
            'title.decoration.font.font__lineStyle' => [
                'attrName' => 'title.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'lineStyle',
            ],
            'title.decoration.font.font__textAlign' => [
                'attrName' => 'title.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'textAlign',
            ],
            'title.decoration.font.font__color' => [
                'attrName' => 'title.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'title.decoration.font.font__size' => [
                'attrName' => 'title.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'size',
            ],
            'title.decoration.font.font__letterSpacing' => [
                'attrName' => 'title.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'letterSpacing',
            ],
            'title.decoration.font.font__lineHeight' => [
                'attrName' => 'title.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'lineHeight',
            ],
            'title.decoration.font.textShadow__style' => [
                'attrName' => 'title.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'title.decoration.font.textShadow__horizontal' => [
                'attrName' => 'title.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'horizontal',
            ],
            'title.decoration.font.textShadow__vertical' => [
                'attrName' => 'title.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'vertical',
            ],
            'title.decoration.font.textShadow__blur' => [
                'attrName' => 'title.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'blur',
            ],
            'title.decoration.font.textShadow__color' => [
                'attrName' => 'title.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'meta.decoration.font.font__family' => [
                'attrName' => 'meta.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'family',
            ],
            'meta.decoration.font.font__weight' => [
                'attrName' => 'meta.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'weight',
            ],
            'meta.decoration.font.font__style' => [
                'attrName' => 'meta.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'meta.decoration.font.font__lineColor' => [
                'attrName' => 'meta.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'lineColor',
            ],
            'meta.decoration.font.font__lineStyle' => [
                'attrName' => 'meta.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'lineStyle',
            ],
            'meta.decoration.font.font__textAlign' => [
                'attrName' => 'meta.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'textAlign',
            ],
            'meta.decoration.font.font__color' => [
                'attrName' => 'meta.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'meta.decoration.font.font__size' => [
                'attrName' => 'meta.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'size',
            ],
            'meta.decoration.font.font__letterSpacing' => [
                'attrName' => 'meta.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'letterSpacing',
            ],
            'meta.decoration.font.font__lineHeight' => [
                'attrName' => 'meta.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'lineHeight',
            ],
            'meta.decoration.font.textShadow__style' => [
                'attrName' => 'meta.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'meta.decoration.font.textShadow__horizontal' => [
                'attrName' => 'meta.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'horizontal',
            ],
            'meta.decoration.font.textShadow__vertical' => [
                'attrName' => 'meta.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'vertical',
            ],
            'meta.decoration.font.textShadow__blur' => [
                'attrName' => 'meta.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'blur',
            ],
            'meta.decoration.font.textShadow__color' => [
                'attrName' => 'meta.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'readMore.decoration.font.font__family' => [
                'attrName' => 'readMore.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'family',
            ],
            'readMore.decoration.font.font__weight' => [
                'attrName' => 'readMore.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'weight',
            ],
            'readMore.decoration.font.font__style' => [
                'attrName' => 'readMore.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'readMore.decoration.font.font__lineColor' => [
                'attrName' => 'readMore.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'lineColor',
            ],
            'readMore.decoration.font.font__lineStyle' => [
                'attrName' => 'readMore.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'lineStyle',
            ],
            'readMore.decoration.font.font__textAlign' => [
                'attrName' => 'readMore.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'textAlign',
            ],
            'readMore.decoration.font.font__color' => [
                'attrName' => 'readMore.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'readMore.decoration.font.font__size' => [
                'attrName' => 'readMore.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'size',
            ],
            'readMore.decoration.font.font__letterSpacing' => [
                'attrName' => 'readMore.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'letterSpacing',
            ],
            'readMore.decoration.font.font__lineHeight' => [
                'attrName' => 'readMore.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'lineHeight',
            ],
            'readMore.decoration.font.textShadow__style' => [
                'attrName' => 'readMore.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'readMore.decoration.font.textShadow__horizontal' => [
                'attrName' => 'readMore.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'horizontal',
            ],
            'readMore.decoration.font.textShadow__vertical' => [
                'attrName' => 'readMore.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'vertical',
            ],
            'readMore.decoration.font.textShadow__blur' => [
                'attrName' => 'readMore.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'blur',
            ],
            'readMore.decoration.font.textShadow__color' => [
                'attrName' => 'readMore.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'pagination.decoration.font.font__family' => [
                'attrName' => 'pagination.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'family',
            ],
            'pagination.decoration.font.font__weight' => [
                'attrName' => 'pagination.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'weight',
            ],
            'pagination.decoration.font.font__style' => [
                'attrName' => 'pagination.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'pagination.decoration.font.font__lineColor' => [
                'attrName' => 'pagination.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'lineColor',
            ],
            'pagination.decoration.font.font__lineStyle' => [
                'attrName' => 'pagination.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'lineStyle',
            ],
            'pagination.decoration.font.font__textAlign' => [
                'attrName' => 'pagination.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'textAlign',
            ],
            'pagination.decoration.font.font__color' => [
                'attrName' => 'pagination.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'pagination.decoration.font.font__size' => [
                'attrName' => 'pagination.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'size',
            ],
            'pagination.decoration.font.font__letterSpacing' => [
                'attrName' => 'pagination.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'letterSpacing',
            ],
            'pagination.decoration.font.font__lineHeight' => [
                'attrName' => 'pagination.decoration.font.font',
                'preset' => ['style'],
                'subName' => 'lineHeight',
            ],
            'pagination.decoration.font.textShadow__style' => [
                'attrName' => 'pagination.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'pagination.decoration.font.textShadow__horizontal' => [
                'attrName' => 'pagination.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'horizontal',
            ],
            'pagination.decoration.font.textShadow__vertical' => [
                'attrName' => 'pagination.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'vertical',
            ],
            'pagination.decoration.font.textShadow__blur' => [
                'attrName' => 'pagination.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'blur',
            ],
            'pagination.decoration.font.textShadow__color' => [
                'attrName' => 'pagination.decoration.font.textShadow',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'content.decoration.bodyFont.body.font__family' => [
                'attrName' => 'content.decoration.bodyFont.body.font',
                'preset' => ['style'],
                'subName' => 'family',
            ],
            'content.decoration.bodyFont.body.font__weight' => [
                'attrName' => 'content.decoration.bodyFont.body.font',
                'preset' => ['style'],
                'subName' => 'weight',
            ],
            'content.decoration.bodyFont.body.font__style' => [
                'attrName' => 'content.decoration.bodyFont.body.font',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'content.decoration.bodyFont.body.font__lineColor' => [
                'attrName' => 'content.decoration.bodyFont.body.font',
                'preset' => ['style'],
                'subName' => 'lineColor',
            ],
            'content.decoration.bodyFont.body.font__lineStyle' => [
                'attrName' => 'content.decoration.bodyFont.body.font',
                'preset' => ['style'],
                'subName' => 'lineStyle',
            ],
            'content.decoration.bodyFont.body.font__textAlign' => [
                'attrName' => 'content.decoration.bodyFont.body.font',
                'preset' => ['style'],
                'subName' => 'textAlign',
            ],
            'content.decoration.bodyFont.body.font__color' => [
                'attrName' => 'content.decoration.bodyFont.body.font',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'content.decoration.bodyFont.body.font__size' => [
                'attrName' => 'content.decoration.bodyFont.body.font',
                'preset' => ['style'],
                'subName' => 'size',
            ],
            'content.decoration.bodyFont.body.font__letterSpacing' => [
                'attrName' => 'content.decoration.bodyFont.body.font',
                'preset' => ['style'],
                'subName' => 'letterSpacing',
            ],
            'content.decoration.bodyFont.body.font__lineHeight' => [
                'attrName' => 'content.decoration.bodyFont.body.font',
                'preset' => ['style'],
                'subName' => 'lineHeight',
            ],
            'content.decoration.bodyFont.body.textShadow__style' => [
                'attrName' => 'content.decoration.bodyFont.body.textShadow',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'content.decoration.bodyFont.body.textShadow__horizontal' => [
                'attrName' => 'content.decoration.bodyFont.body.textShadow',
                'preset' => ['style'],
                'subName' => 'horizontal',
            ],
            'content.decoration.bodyFont.body.textShadow__vertical' => [
                'attrName' => 'content.decoration.bodyFont.body.textShadow',
                'preset' => ['style'],
                'subName' => 'vertical',
            ],
            'content.decoration.bodyFont.body.textShadow__blur' => [
                'attrName' => 'content.decoration.bodyFont.body.textShadow',
                'preset' => ['style'],
                'subName' => 'blur',
            ],
            'content.decoration.bodyFont.body.textShadow__color' => [
                'attrName' => 'content.decoration.bodyFont.body.textShadow',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'content.decoration.bodyFont.link.font__family' => [
                'attrName' => 'content.decoration.bodyFont.link.font',
                'preset' => ['style'],
                'subName' => 'family',
            ],
            'content.decoration.bodyFont.link.font__weight' => [
                'attrName' => 'content.decoration.bodyFont.link.font',
                'preset' => ['style'],
                'subName' => 'weight',
            ],
            'content.decoration.bodyFont.link.font__style' => [
                'attrName' => 'content.decoration.bodyFont.link.font',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'content.decoration.bodyFont.link.font__lineColor' => [
                'attrName' => 'content.decoration.bodyFont.link.font',
                'preset' => ['style'],
                'subName' => 'lineColor',
            ],
            'content.decoration.bodyFont.link.font__lineStyle' => [
                'attrName' => 'content.decoration.bodyFont.link.font',
                'preset' => ['style'],
                'subName' => 'lineStyle',
            ],
            'content.decoration.bodyFont.link.font__textAlign' => [
                'attrName' => 'content.decoration.bodyFont.link.font',
                'preset' => ['style'],
                'subName' => 'textAlign',
            ],
            'content.decoration.bodyFont.link.font__color' => [
                'attrName' => 'content.decoration.bodyFont.link.font',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'content.decoration.bodyFont.link.font__size' => [
                'attrName' => 'content.decoration.bodyFont.link.font',
                'preset' => ['style'],
                'subName' => 'size',
            ],
            'content.decoration.bodyFont.link.font__letterSpacing' => [
                'attrName' => 'content.decoration.bodyFont.link.font',
                'preset' => ['style'],
                'subName' => 'letterSpacing',
            ],
            'content.decoration.bodyFont.link.font__lineHeight' => [
                'attrName' => 'content.decoration.bodyFont.link.font',
                'preset' => ['style'],
                'subName' => 'lineHeight',
            ],
            'content.decoration.bodyFont.link.textShadow__style' => [
                'attrName' => 'content.decoration.bodyFont.link.textShadow',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'content.decoration.bodyFont.link.textShadow__horizontal' => [
                'attrName' => 'content.decoration.bodyFont.link.textShadow',
                'preset' => ['style'],
                'subName' => 'horizontal',
            ],
            'content.decoration.bodyFont.link.textShadow__vertical' => [
                'attrName' => 'content.decoration.bodyFont.link.textShadow',
                'preset' => ['style'],
                'subName' => 'vertical',
            ],
            'content.decoration.bodyFont.link.textShadow__blur' => [
                'attrName' => 'content.decoration.bodyFont.link.textShadow',
                'preset' => ['style'],
                'subName' => 'blur',
            ],
            'content.decoration.bodyFont.link.textShadow__color' => [
                'attrName' => 'content.decoration.bodyFont.link.textShadow',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'content.decoration.bodyFont.ul.font__family' => [
                'attrName' => 'content.decoration.bodyFont.ul.font',
                'preset' => ['style'],
                'subName' => 'family',
            ],
            'content.decoration.bodyFont.ul.font__weight' => [
                'attrName' => 'content.decoration.bodyFont.ul.font',
                'preset' => ['style'],
                'subName' => 'weight',
            ],
            'content.decoration.bodyFont.ul.font__style' => [
                'attrName' => 'content.decoration.bodyFont.ul.font',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'content.decoration.bodyFont.ul.font__lineColor' => [
                'attrName' => 'content.decoration.bodyFont.ul.font',
                'preset' => ['style'],
                'subName' => 'lineColor',
            ],
            'content.decoration.bodyFont.ul.font__lineStyle' => [
                'attrName' => 'content.decoration.bodyFont.ul.font',
                'preset' => ['style'],
                'subName' => 'lineStyle',
            ],
            'content.decoration.bodyFont.ul.font__textAlign' => [
                'attrName' => 'content.decoration.bodyFont.ul.font',
                'preset' => ['style'],
                'subName' => 'textAlign',
            ],
            'content.decoration.bodyFont.ul.font__color' => [
                'attrName' => 'content.decoration.bodyFont.ul.font',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'content.decoration.bodyFont.ul.font__size' => [
                'attrName' => 'content.decoration.bodyFont.ul.font',
                'preset' => ['style'],
                'subName' => 'size',
            ],
            'content.decoration.bodyFont.ul.font__letterSpacing' => [
                'attrName' => 'content.decoration.bodyFont.ul.font',
                'preset' => ['style'],
                'subName' => 'letterSpacing',
            ],
            'content.decoration.bodyFont.ul.font__lineHeight' => [
                'attrName' => 'content.decoration.bodyFont.ul.font',
                'preset' => ['style'],
                'subName' => 'lineHeight',
            ],
            'content.decoration.bodyFont.ul.textShadow__style' => [
                'attrName' => 'content.decoration.bodyFont.ul.textShadow',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'content.decoration.bodyFont.ul.textShadow__horizontal' => [
                'attrName' => 'content.decoration.bodyFont.ul.textShadow',
                'preset' => ['style'],
                'subName' => 'horizontal',
            ],
            'content.decoration.bodyFont.ul.textShadow__vertical' => [
                'attrName' => 'content.decoration.bodyFont.ul.textShadow',
                'preset' => ['style'],
                'subName' => 'vertical',
            ],
            'content.decoration.bodyFont.ul.textShadow__blur' => [
                'attrName' => 'content.decoration.bodyFont.ul.textShadow',
                'preset' => ['style'],
                'subName' => 'blur',
            ],
            'content.decoration.bodyFont.ul.textShadow__color' => [
                'attrName' => 'content.decoration.bodyFont.ul.textShadow',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'content.decoration.bodyFont.ul.list__type' => [
                'attrName' => 'content.decoration.bodyFont.ul.list',
                'preset' => ['style'],
                'subName' => 'type',
            ],
            'content.decoration.bodyFont.ul.list__position' => [
                'attrName' => 'content.decoration.bodyFont.ul.list',
                'preset' => ['style'],
                'subName' => 'position',
            ],
            'content.decoration.bodyFont.ul.list__itemIndent' => [
                'attrName' => 'content.decoration.bodyFont.ul.list',
                'preset' => ['style'],
                'subName' => 'itemIndent',
            ],
            'content.decoration.bodyFont.ol.font__family' => [
                'attrName' => 'content.decoration.bodyFont.ol.font',
                'preset' => ['style'],
                'subName' => 'family',
            ],
            'content.decoration.bodyFont.ol.font__weight' => [
                'attrName' => 'content.decoration.bodyFont.ol.font',
                'preset' => ['style'],
                'subName' => 'weight',
            ],
            'content.decoration.bodyFont.ol.font__style' => [
                'attrName' => 'content.decoration.bodyFont.ol.font',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'content.decoration.bodyFont.ol.font__lineColor' => [
                'attrName' => 'content.decoration.bodyFont.ol.font',
                'preset' => ['style'],
                'subName' => 'lineColor',
            ],
            'content.decoration.bodyFont.ol.font__lineStyle' => [
                'attrName' => 'content.decoration.bodyFont.ol.font',
                'preset' => ['style'],
                'subName' => 'lineStyle',
            ],
            'content.decoration.bodyFont.ol.font__textAlign' => [
                'attrName' => 'content.decoration.bodyFont.ol.font',
                'preset' => ['style'],
                'subName' => 'textAlign',
            ],
            'content.decoration.bodyFont.ol.font__color' => [
                'attrName' => 'content.decoration.bodyFont.ol.font',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'content.decoration.bodyFont.ol.font__size' => [
                'attrName' => 'content.decoration.bodyFont.ol.font',
                'preset' => ['style'],
                'subName' => 'size',
            ],
            'content.decoration.bodyFont.ol.font__letterSpacing' => [
                'attrName' => 'content.decoration.bodyFont.ol.font',
                'preset' => ['style'],
                'subName' => 'letterSpacing',
            ],
            'content.decoration.bodyFont.ol.font__lineHeight' => [
                'attrName' => 'content.decoration.bodyFont.ol.font',
                'preset' => ['style'],
                'subName' => 'lineHeight',
            ],
            'content.decoration.bodyFont.ol.textShadow__style' => [
                'attrName' => 'content.decoration.bodyFont.ol.textShadow',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'content.decoration.bodyFont.ol.textShadow__horizontal' => [
                'attrName' => 'content.decoration.bodyFont.ol.textShadow',
                'preset' => ['style'],
                'subName' => 'horizontal',
            ],
            'content.decoration.bodyFont.ol.textShadow__vertical' => [
                'attrName' => 'content.decoration.bodyFont.ol.textShadow',
                'preset' => ['style'],
                'subName' => 'vertical',
            ],
            'content.decoration.bodyFont.ol.textShadow__blur' => [
                'attrName' => 'content.decoration.bodyFont.ol.textShadow',
                'preset' => ['style'],
                'subName' => 'blur',
            ],
            'content.decoration.bodyFont.ol.textShadow__color' => [
                'attrName' => 'content.decoration.bodyFont.ol.textShadow',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'content.decoration.bodyFont.ol.list__type' => [
                'attrName' => 'content.decoration.bodyFont.ol.list',
                'preset' => ['style'],
                'subName' => 'type',
            ],
            'content.decoration.bodyFont.ol.list__position' => [
                'attrName' => 'content.decoration.bodyFont.ol.list',
                'preset' => ['style'],
                'subName' => 'position',
            ],
            'content.decoration.bodyFont.ol.list__itemIndent' => [
                'attrName' => 'content.decoration.bodyFont.ol.list',
                'preset' => ['style'],
                'subName' => 'itemIndent',
            ],
            'content.decoration.bodyFont.quote.font__family' => [
                'attrName' => 'content.decoration.bodyFont.quote.font',
                'preset' => ['style'],
                'subName' => 'family',
            ],
            'content.decoration.bodyFont.quote.font__weight' => [
                'attrName' => 'content.decoration.bodyFont.quote.font',
                'preset' => ['style'],
                'subName' => 'weight',
            ],
            'content.decoration.bodyFont.quote.font__style' => [
                'attrName' => 'content.decoration.bodyFont.quote.font',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'content.decoration.bodyFont.quote.font__lineColor' => [
                'attrName' => 'content.decoration.bodyFont.quote.font',
                'preset' => ['style'],
                'subName' => 'lineColor',
            ],
            'content.decoration.bodyFont.quote.font__lineStyle' => [
                'attrName' => 'content.decoration.bodyFont.quote.font',
                'preset' => ['style'],
                'subName' => 'lineStyle',
            ],
            'content.decoration.bodyFont.quote.font__textAlign' => [
                'attrName' => 'content.decoration.bodyFont.quote.font',
                'preset' => ['style'],
                'subName' => 'textAlign',
            ],
            'content.decoration.bodyFont.quote.font__color' => [
                'attrName' => 'content.decoration.bodyFont.quote.font',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'content.decoration.bodyFont.quote.font__size' => [
                'attrName' => 'content.decoration.bodyFont.quote.font',
                'preset' => ['style'],
                'subName' => 'size',
            ],
            'content.decoration.bodyFont.quote.font__letterSpacing' => [
                'attrName' => 'content.decoration.bodyFont.quote.font',
                'preset' => ['style'],
                'subName' => 'letterSpacing',
            ],
            'content.decoration.bodyFont.quote.font__lineHeight' => [
                'attrName' => 'content.decoration.bodyFont.quote.font',
                'preset' => ['style'],
                'subName' => 'lineHeight',
            ],
            'content.decoration.bodyFont.quote.textShadow__style' => [
                'attrName' => 'content.decoration.bodyFont.quote.textShadow',
                'preset' => ['style'],
                'subName' => 'style',
            ],
            'content.decoration.bodyFont.quote.textShadow__horizontal' => [
                'attrName' => 'content.decoration.bodyFont.quote.textShadow',
                'preset' => ['style'],
                'subName' => 'horizontal',
            ],
            'content.decoration.bodyFont.quote.textShadow__vertical' => [
                'attrName' => 'content.decoration.bodyFont.quote.textShadow',
                'preset' => ['style'],
                'subName' => 'vertical',
            ],
            'content.decoration.bodyFont.quote.textShadow__blur' => [
                'attrName' => 'content.decoration.bodyFont.quote.textShadow',
                'preset' => ['style'],
                'subName' => 'blur',
            ],
            'content.decoration.bodyFont.quote.textShadow__color' => [
                'attrName' => 'content.decoration.bodyFont.quote.textShadow',
                'preset' => ['style'],
                'subName' => 'color',
            ],
            'content.decoration.bodyFont.quote.border__styles.left.width' => [
                'attrName' => 'content.decoration.bodyFont.quote.border',
                'preset' => ['style'],
                'subName' => 'styles.left.width',
            ],
            'content.decoration.bodyFont.quote.border__styles.left.color' => [
                'attrName' => 'content.decoration.bodyFont.quote.border',
                'preset' => ['style'],
                'subName' => 'styles.left.color',
            ],
            'module.decoration.sizing__width' => [
                'attrName' => 'module.decoration.sizing',
                'preset' => ['style'],
                'subName' => 'width',
            ],
            'module.decoration.sizing__maxWidth' => [
                'attrName' => 'module.decoration.sizing',
                'preset' => ['style'],
                'subName' => 'maxWidth',
            ],
            'module.decoration.sizing__alignment' => [
                'attrName' => 'module.decoration.sizing',
                'preset' => ['style'],
                'subName' => 'alignment',
            ],
            'module.decoration.sizing__minHeight' => [
                'attrName' => 'module.decoration.sizing',
                'preset' => ['style'],
                'subName' => 'minHeight',
            ],
            'module.decoration.sizing__height' => [
                'attrName' => 'module.decoration.sizing',
                'preset' => ['style'],
                'subName' => 'height',
            ],
            'module.decoration.sizing__maxHeight' => [
                'attrName' => 'module.decoration.sizing',
                'preset' => ['style'],
                'subName' => 'maxHeight',
            ],
            'module.decoration.spacing__margin' => [
                'attrName' => 'module.decoration.spacing',
                'preset' => ['style'],
                'subName' => 'margin',
            ],
            'module.decoration.spacing__padding' => [
                'attrName' => 'module.decoration.spacing',
                'preset' => ['style'],
                'subName' => 'padding',
            ],
            'post.decoration.border__radius' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'radius',
            ],
            'post.decoration.border__styles' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles',
            ],
            'post.decoration.border__styles.all.width' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.all.width',
            ],
            'post.decoration.border__styles.top.width' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.top.width',
            ],
            'post.decoration.border__styles.right.width' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.right.width',
            ],
            'post.decoration.border__styles.bottom.width' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.bottom.width',
            ],
            'post.decoration.border__styles.left.width' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.left.width',
            ],
            'post.decoration.border__styles.all.color' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.all.color',
            ],
            'post.decoration.border__styles.top.color' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.top.color',
            ],
            'post.decoration.border__styles.right.color' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.right.color',
            ],
            'post.decoration.border__styles.bottom.color' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.bottom.color',
            ],
            'post.decoration.border__styles.left.color' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.left.color',
            ],
            'post.decoration.border__styles.all.style' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.all.style',
            ],
            'post.decoration.border__styles.top.style' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.top.style',
            ],
            'post.decoration.border__styles.right.style' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.right.style',
            ],
            'post.decoration.border__styles.bottom.style' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.bottom.style',
            ],
            'post.decoration.border__styles.left.style' => [
                'attrName' => 'post.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.left.style',
            ],
            'fullwidth.decoration.border__radius' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'radius',
            ],
            'fullwidth.decoration.border__styles' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles',
            ],
            'fullwidth.decoration.border__styles.all.width' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.all.width',
            ],
            'fullwidth.decoration.border__styles.top.width' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.top.width',
            ],
            'fullwidth.decoration.border__styles.right.width' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.right.width',
            ],
            'fullwidth.decoration.border__styles.bottom.width' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.bottom.width',
            ],
            'fullwidth.decoration.border__styles.left.width' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.left.width',
            ],
            'fullwidth.decoration.border__styles.all.color' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.all.color',
            ],
            'fullwidth.decoration.border__styles.top.color' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.top.color',
            ],
            'fullwidth.decoration.border__styles.right.color' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.right.color',
            ],
            'fullwidth.decoration.border__styles.bottom.color' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.bottom.color',
            ],
            'fullwidth.decoration.border__styles.left.color' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.left.color',
            ],
            'fullwidth.decoration.border__styles.all.style' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.all.style',
            ],
            'fullwidth.decoration.border__styles.top.style' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.top.style',
            ],
            'fullwidth.decoration.border__styles.right.style' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.right.style',
            ],
            'fullwidth.decoration.border__styles.bottom.style' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.bottom.style',
            ],
            'fullwidth.decoration.border__styles.left.style' => [
                'attrName' => 'fullwidth.decoration.border',
                'preset' => ['style'],
                'subName' => 'styles.left.style',
            ],
            'module.decoration.boxShadow__style' => [
                'attrName' => 'module.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'style',
            ],
            'module.decoration.boxShadow__horizontal' => [
                'attrName' => 'module.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'horizontal',
            ],
            'module.decoration.boxShadow__vertical' => [
                'attrName' => 'module.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'vertical',
            ],
            'module.decoration.boxShadow__blur' => [
                'attrName' => 'module.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'blur',
            ],
            'module.decoration.boxShadow__spread' => [
                'attrName' => 'module.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'spread',
            ],
            'module.decoration.boxShadow__color' => [
                'attrName' => 'module.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'color',
            ],
            'module.decoration.boxShadow__position' => [
                'attrName' => 'module.decoration.boxShadow',
                'preset' => ['html', 'style'],
                'subName' => 'position',
            ],
            'module.decoration.filters__hueRotate' => [
                'attrName' => 'module.decoration.filters',
                'preset' => ['style'],
                'subName' => 'hueRotate',
            ],
            'module.decoration.filters__saturate' => [
                'attrName' => 'module.decoration.filters',
                'preset' => ['style'],
                'subName' => 'saturate',
            ],
            'module.decoration.filters__brightness' => [
                'attrName' => 'module.decoration.filters',
                'preset' => ['style'],
                'subName' => 'brightness',
            ],
            'module.decoration.filters__contrast' => [
                'attrName' => 'module.decoration.filters',
                'preset' => ['style'],
                'subName' => 'contrast',
            ],
            'module.decoration.filters__invert' => [
                'attrName' => 'module.decoration.filters',
                'preset' => ['style'],
                'subName' => 'invert',
            ],
            'module.decoration.filters__sepia' => [
                'attrName' => 'module.decoration.filters',
                'preset' => ['style'],
                'subName' => 'sepia',
            ],
            'module.decoration.filters__opacity' => [
                'attrName' => 'module.decoration.filters',
                'preset' => ['style'],
                'subName' => 'opacity',
            ],
            'module.decoration.filters__blur' => [
                'attrName' => 'module.decoration.filters',
                'preset' => ['style'],
                'subName' => 'blur',
            ],
            'module.decoration.filters__blendMode' => [
                'attrName' => 'module.decoration.filters',
                'preset' => ['style'],
                'subName' => 'blendMode',
            ],
            'module.decoration.transform__scale' => [
                'attrName' => 'module.decoration.transform',
                'preset' => ['style'],
                'subName' => 'scale',
            ],
            'module.decoration.transform__translate' => [
                'attrName' => 'module.decoration.transform',
                'preset' => ['style'],
                'subName' => 'translate',
            ],
            'module.decoration.transform__rotate' => [
                'attrName' => 'module.decoration.transform',
                'preset' => ['style'],
                'subName' => 'rotate',
            ],
            'module.decoration.transform__skew' => [
                'attrName' => 'module.decoration.transform',
                'preset' => ['style'],
                'subName' => 'skew',
            ],
            'module.decoration.transform__origin' => [
                'attrName' => 'module.decoration.transform',
                'preset' => ['style'],
                'subName' => 'origin',
            ],
            'module.decoration.animation__style' => [
                'attrName' => 'module.decoration.animation',
                'preset' => ['script'],
                'subName' => 'style',
            ],
            'module.decoration.animation__direction' => [
                'attrName' => 'module.decoration.animation',
                'preset' => ['script'],
                'subName' => 'direction',
            ],
            'module.decoration.animation__duration' => [
                'attrName' => 'module.decoration.animation',
                'preset' => ['script'],
                'subName' => 'duration',
            ],
            'module.decoration.animation__delay' => [
                'attrName' => 'module.decoration.animation',
                'preset' => ['script'],
                'subName' => 'delay',
            ],
            'module.decoration.animation__intensity.slide' => [
                'attrName' => 'module.decoration.animation',
                'preset' => ['script'],
                'subName' => 'intensity.slide',
            ],
            'module.decoration.animation__intensity.zoom' => [
                'attrName' => 'module.decoration.animation',
                'preset' => ['script'],
                'subName' => 'intensity.zoom',
            ],
            'module.decoration.animation__intensity.flip' => [
                'attrName' => 'module.decoration.animation',
                'preset' => ['script'],
                'subName' => 'intensity.flip',
            ],
            'module.decoration.animation__intensity.fold' => [
                'attrName' => 'module.decoration.animation',
                'preset' => ['script'],
                'subName' => 'intensity.fold',
            ],
            'module.decoration.animation__intensity.roll' => [
                'attrName' => 'module.decoration.animation',
                'preset' => ['script'],
                'subName' => 'intensity.roll',
            ],
            'module.decoration.animation__startingOpacity' => [
                'attrName' => 'module.decoration.animation',
                'preset' => ['script'],
                'subName' => 'startingOpacity',
            ],
            'module.decoration.animation__speedCurve' => [
                'attrName' => 'module.decoration.animation',
                'preset' => ['script'],
                'subName' => 'speedCurve',
            ],
            'module.decoration.animation__repeat' => [
                'attrName' => 'module.decoration.animation',
                'preset' => ['script'],
                'subName' => 'repeat',
            ],
            'module.advanced.htmlAttributes__id' => [
                'attrName' => 'module.advanced.htmlAttributes',
                'preset' => 'content',
                'subName' => 'id',
            ],
            'module.advanced.htmlAttributes__class' => [
                'attrName' => 'module.advanced.htmlAttributes',
                'preset' => ['html'],
                'subName' => 'class',
            ],
            'css__before' => [
                'attrName' => 'css',
                'preset' => ['style'],
                'subName' => 'before',
            ],
            'css__mainElement' => [
                'attrName' => 'css',
                'preset' => ['style'],
                'subName' => 'mainElement',
            ],
            'css__after' => [
                'attrName' => 'css',
                'preset' => ['style'],
                'subName' => 'after',
            ],
            'css__freeForm' => [
                'attrName' => 'css',
                'preset' => ['style'],
                'subName' => 'freeForm',
            ],
            'css__title' => [
                'attrName' => 'css',
                'preset' => ['style'],
                'subName' => 'title',
            ],
            'css__content' => [
                'attrName' => 'css',
                'preset' => ['style'],
                'subName' => 'content',
            ],
            'css__postMeta' => [
                'attrName' => 'css',
                'preset' => ['style'],
                'subName' => 'postMeta',
            ],
            'css__pagenavi' => [
                'attrName' => 'css',
                'preset' => ['style'],
                'subName' => 'pagenavi',
            ],
            'css__featuredImage' => [
                'attrName' => 'css',
                'preset' => ['style'],
                'subName' => 'featuredImage',
            ],
            'css__readMore' => [
                'attrName' => 'css',
                'preset' => ['style'],
                'subName' => 'readMore',
            ],
            'module.decoration.conditions' => [
                'attrName' => 'module.decoration.conditions',
                'preset' => ['html'],
            ],
            'module.decoration.disabledOn' => [
                'attrName' => 'module.decoration.disabledOn',
                'preset' => ['html', 'style'],
            ],
            'module.decoration.overflow__x' => [
                'attrName' => 'module.decoration.overflow',
                'preset' => ['style'],
                'subName' => 'x',
            ],
            'module.decoration.overflow__y' => [
                'attrName' => 'module.decoration.overflow',
                'preset' => ['style'],
                'subName' => 'y',
            ],
            'module.decoration.transition__duration' => [
                'attrName' => 'module.decoration.transition',
                'preset' => ['style'],
                'subName' => 'duration',
            ],
            'module.decoration.transition__delay' => [
                'attrName' => 'module.decoration.transition',
                'preset' => ['style'],
                'subName' => 'delay',
            ],
            'module.decoration.transition__speedCurve' => [
                'attrName' => 'module.decoration.transition',
                'preset' => ['style'],
                'subName' => 'speedCurve',
            ],
            'module.decoration.position__mode' => [
                'attrName' => 'module.decoration.position',
                'preset' => ['style'],
                'subName' => 'mode',
            ],
            'module.decoration.position__origin.relative' => [
                'attrName' => 'module.decoration.position',
                'preset' => ['style'],
                'subName' => 'origin.relative',
            ],
            'module.decoration.position__origin.absolute' => [
                'attrName' => 'module.decoration.position',
                'preset' => ['style'],
                'subName' => 'origin.absolute',
            ],
            'module.decoration.position__origin.fixed' => [
                'attrName' => 'module.decoration.position',
                'preset' => ['style'],
                'subName' => 'origin.fixed',
            ],
            'module.decoration.position__offset.vertical' => [
                'attrName' => 'module.decoration.position',
                'preset' => ['style'],
                'subName' => 'offset.vertical',
            ],
            'module.decoration.position__offset.horizontal' => [
                'attrName' => 'module.decoration.position',
                'preset' => ['style'],
                'subName' => 'offset.horizontal',
            ],
            'module.decoration.scroll__gridMotion.enable' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'gridMotion.enable',
            ],
            'module.decoration.scroll__verticalMotion.enable' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'verticalMotion.enable',
            ],
            'module.decoration.scroll__horizontalMotion.enable' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'horizontalMotion.enable',
            ],
            'module.decoration.scroll__fade.enable' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'fade.enable',
            ],
            'module.decoration.scroll__scaling.enable' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'scaling.enable',
            ],
            'module.decoration.scroll__rotating.enable' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'rotating.enable',
            ],
            'module.decoration.scroll__blur.enable' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'blur.enable',
            ],
            'module.decoration.scroll__verticalMotion' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'verticalMotion',
            ],
            'module.decoration.scroll__horizontalMotion' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'horizontalMotion',
            ],
            'module.decoration.scroll__fade' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'fade',
            ],
            'module.decoration.scroll__scaling' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'scaling',
            ],
            'module.decoration.scroll__rotating' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'rotating',
            ],
            'module.decoration.scroll__blur' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'blur',
            ],
            'module.decoration.scroll__motionTriggerStart' => [
                'attrName' => 'module.decoration.scroll',
                'preset' => ['script'],
                'subName' => 'motionTriggerStart',
            ],
            'module.decoration.sticky__limit.bottom' => [
                'attrName' => 'module.decoration.sticky',
                'preset' => ['script'],
                'subName' => 'limit.bottom',
            ],
            'module.decoration.sticky__limit.top' => [
                'attrName' => 'module.decoration.sticky',
                'preset' => ['script'],
                'subName' => 'limit.top',
            ],
            'module.decoration.sticky__offset.bottom' => [
                'attrName' => 'module.decoration.sticky',
                'preset' => ['script'],
                'subName' => 'offset.bottom',
            ],
            'module.decoration.sticky__offset.surrounding' => [
                'attrName' => 'module.decoration.sticky',
                'preset' => ['script'],
                'subName' => 'offset.surrounding',
            ],
            'module.decoration.sticky__offset.top' => [
                'attrName' => 'module.decoration.sticky',
                'preset' => ['script'],
                'subName' => 'offset.top',
            ],
            'module.decoration.sticky__position' => [
                'attrName' => 'module.decoration.sticky',
                'preset' => ['script'],
                'subName' => 'position',
            ],
            'module.decoration.sticky__transition' => [
                'attrName' => 'module.decoration.sticky',
                'preset' => ['script'],
                'subName' => 'transition',
            ],
            'module.decoration.zIndex' => [
                'attrName' => 'module.decoration.zIndex',
                'preset' => ['style'],
            ],
        ];
    }
}
