import { type DeclarationFunctionProps } from '@divi/module';
import { StyleDeclarations } from '@divi/style-library';
import { type Module } from '@divi/types';

export const orientationStyleDeclaration = ({
    attrValue,
}: DeclarationFunctionProps<Module.Element.Advanced.Text.AttributeValue>): string => {
    const { orientation } = attrValue;
    const declarations = new StyleDeclarations({
        important: true,
        returnType: 'string',
    });
    if (orientation) {
        declarations.add('display', 'flex');
        declarations.add('flex-grow', '0');
        declarations.add('justify-content', orientation);
    }
    return declarations.value as string;
};
