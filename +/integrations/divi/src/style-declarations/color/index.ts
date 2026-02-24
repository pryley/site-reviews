import { type DeclarationFunctionProps } from '@divi/module';
import { StyleDeclarations } from '@divi/style-library';
import { type Module } from '@divi/types';

export const colorStyleDeclaration = (cssVariables: string[]) => ({
    attrValue,
}: DeclarationFunctionProps<Module.Element.Decoration.Background.AttributeValue>): string => {
    const { color } = attrValue;
    const declarations = new StyleDeclarations({
        important: false,
        returnType: 'string',
    });
    if (color) {
        cssVariables.forEach(prop => declarations.add(prop, color));
    }
    return declarations.value as string;
};
