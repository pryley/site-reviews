import { ModuleEditProps } from '@divi/module-library';
import {
  InternalAttrs,
  type Element,
  type FormatBreakpointStateAttr,
} from '@divi/types';

export interface ModuleAttrs extends InternalAttrs {
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
      assigned_posts?: FormatBreakpointStateAttr<string[]>;
      assigned_terms?: FormatBreakpointStateAttr<string[]>;
      assigned_users?: FormatBreakpointStateAttr<string[]>;
      author?: FormatBreakpointStateAttr<string>;
      className?: FormatBreakpointStateAttr<string>;
      display?: FormatBreakpointStateAttr<number>;
      hide?: FormatBreakpointStateAttr<string[]>;
      id?: FormatBreakpointStateAttr<string>;
      pagination?: FormatBreakpointStateAttr<string>;
      rating?: FormatBreakpointStateAttr<number>;
      schema?: FormatBreakpointStateAttr<string>;
      terms?: FormatBreakpointStateAttr<string>;
      type?: FormatBreakpointStateAttr<string>;
      verified?: FormatBreakpointStateAttr<string>;
    };
  };
  button?: {
    decoration?: Element.Decoration.PickedAttributes<
      'background' |
      'border' |
      'boxShadow' |
      'button' |
      'font' |
      'spacing'
    >;
  };
}

export type EditProps = ModuleEditProps<ModuleAttrs>;
