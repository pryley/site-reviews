import { type ModuleEditProps } from '@divi/module-library';

export interface EditProps<TAttrs = Record<string, any>> extends ModuleEditProps<TAttrs> {
  moduleClassnames: any;
  ModuleScriptData: any;
  ModuleStyles: any;
}

export interface Item {
  id: string | number;
  title: string;
}

export interface TransformedItem {
  label: string;
  value: string;
}
