import { ModuleEditProps } from '@divi/module-library';
import {
  InternalAttrs,
  type Element,
  type FieldElementAttr,
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
      className?: FormatBreakpointStateAttr<string>;
      hide?: FormatBreakpointStateAttr<string[]>;
      id?: FormatBreakpointStateAttr<string>;
      reviews_id?: FormatBreakpointStateAttr<string>;
      summary_id?: FormatBreakpointStateAttr<string>;
    };
  };
  design?: {
    advanced?: {
      state?: {
        checkedColor?: FormatBreakpointStateAttr<string>;
        focusBackground?: FormatBreakpointStateAttr<string>;
        focusColor?: FormatBreakpointStateAttr<string>;
        invalidColor?: FormatBreakpointStateAttr<string>;
      };
    };
    decoration?: {
      ratingColor?: FormatBreakpointStateAttr<string>;
      ratingSize?: FormatBreakpointStateAttr<string>;
      columnGap?: FormatBreakpointStateAttr<string>;
      rowGap?: FormatBreakpointStateAttr<string>;
      spacing?: FormatBreakpointStateAttr<string[]>;
      fieldLabelGroup?: any;
      fieldElementGroup?: any;
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
