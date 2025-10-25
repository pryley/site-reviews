import { ModuleEditProps } from '@divi/module-library';
import {
  InternalAttrs,
  type Element,
  type FormatBreakpointStateAttr,
} from '@divi/types';

interface CustomDecorationAttributes extends Element.Decoration.PickedAttributes<
  'animation' |
  'background' |
  'border' |
  'boxShadow' |
  'disabledOn' |
  'filters' |
  'font' |
  'overflow' |
  'position' |
  'scroll' |
  'sizing' |
  'spacing' |
  'sticky' |
  'transform' |
  'transition' |
  'zIndex'
> {
  styleRatingColor?: FormatBreakpointStateAttr<string>;
  styleRatingSize?: FormatBreakpointStateAttr<string>;
}

export interface ModuleAttrs extends InternalAttrs {
  module?: {
    advanced?: {
      htmlAttributes?: Element.Advanced.IdClasses.Attributes;
      link?: Element.Advanced.Link.Attributes;
      text?: Element.Advanced.Text.Attributes;
    };
    decoration?: CustomDecorationAttributes;
    meta?: Element.Meta.Attributes;
  };
  shortcode?: {
    advanced?: {
      postId?: FormatBreakpointStateAttr<string>;
      className?: FormatBreakpointStateAttr<string>;
      hide?: FormatBreakpointStateAttr<string[]>;
      id?: FormatBreakpointStateAttr<string>;
    },
  };
}

export type EditProps = ModuleEditProps<ModuleAttrs>;
