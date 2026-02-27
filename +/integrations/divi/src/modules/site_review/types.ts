import { ModuleEditProps } from '@divi/module-library';
import {
  InternalAttrs,
  type Element,
  type FormatBreakpointStateAttr,
  type Module,
} from '@divi/types';

export interface ModuleAttrs extends InternalAttrs {
  css?: FormatBreakpointStateAttr<Module.Css.AttributeValue>;
  module?: {
    advanced?: {
      htmlAttributes?: Element.Advanced.IdClasses.Attributes;
      link?: Element.Advanced.Link.Attributes;
      text?: Element.Advanced.Text.Attributes;
    };
    decoration?: Element.Decoration.PickedAttributes<
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
    >;
    meta?: Element.Meta.Attributes;
  };
  shortcode?: {
    advanced?: {
      postId?: FormatBreakpointStateAttr<string>;
      className?: FormatBreakpointStateAttr<string>;
      hide?: FormatBreakpointStateAttr<string[]>;
      id?: FormatBreakpointStateAttr<string>;
    };
  };
  design?: {
    decoration?: {
      ratingColor?: FormatBreakpointStateAttr<string>;
    };
  };
}

export type EditProps = ModuleEditProps<ModuleAttrs>;
