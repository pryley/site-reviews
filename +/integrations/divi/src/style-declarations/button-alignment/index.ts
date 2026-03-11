import { StyleDeclarations } from '@divi/style-library';
import { type DeclarationFunctionProps } from '@divi/module';
import { type Module } from '@divi/types';

export const buttonAlignmentStyleDeclaration = ({
    attrValue,
}: DeclarationFunctionProps<Module.Element.Decoration.Button.AttributeValue>): string => {
    const { alignment } = attrValue;
    const declarations = new StyleDeclarations({
        important: false,
        returnType: 'string',
    });
    const map = {
        default: '',
        left: 'start',
        center: 'center',
        right: 'end',
    };
    const value = map[alignment ?? 'default'] ?? 'start';
    declarations.add('display', 'flex');
    declarations.add('justify-content', value);
    return declarations.value as string;
};
