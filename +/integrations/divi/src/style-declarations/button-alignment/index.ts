import { StyleDeclarations } from '@divi/style-library';
import { type DeclarationFunctionProps } from '@divi/module';
import { type Module } from '@divi/types';

export const buttonAlignmentStyleDeclaration = ({
    attrValue,
}: DeclarationFunctionProps<Module.Element.Decoration.Button.AttributeValue>): string => {
    const { alignment } = attrValue;
    const map = {
        default: '',
        left: 'start',
        center: 'center',
        right: 'right',
    };
    const value = map[alignment ?? 'default'] ?? '';
    const declarations = new StyleDeclarations({
        important: false,
        returnType: 'string',
    });
    if (value) {
        declarations.add('display', 'flex');
        declarations.add('justify-content', value);
    }
    return declarations.value as string;
};
