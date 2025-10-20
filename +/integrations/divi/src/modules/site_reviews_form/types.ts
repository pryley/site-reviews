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
      className?: FormatBreakpointStateAttr<string>;
      hide?: FormatBreakpointStateAttr<string[]>;
      id?: FormatBreakpointStateAttr<string>;
      reviews_id?: FormatBreakpointStateAttr<string>;
      summary_id?: FormatBreakpointStateAttr<string>;
    },
  };
}

export interface Item {
    id: string | number;
    title: string;
}

export interface TransformedItem {
    label: string;
    value: string;
}

export type EditProps = ModuleEditProps<ModuleAttrs>;
