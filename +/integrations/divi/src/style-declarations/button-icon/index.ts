import { StyleDeclarations } from '@divi/style-library';
import { type DeclarationFunctionProps } from '@divi/module';
import { type Module } from '@divi/types';
import { escapeFontIcon, processFontIcon } from '@divi/icon-library';
import { isEmpty } from 'lodash';

export const buttonIconStyleDeclaration = ({
    attrValue,
}: DeclarationFunctionProps<Module.Element.Decoration.Button.AttributeValue>): string => {
    const { enable, icon } = attrValue;
    const declarations = new StyleDeclarations({
        important: {
            content: true,
        },
        returnType: 'string',
    });
    if ('off' === enable) {
        return declarations.value as string;
    }
    if (!isEmpty(icon?.settings)) {
        const content = escapeFontIcon(processFontIcon(icon?.settings));
        declarations.add('content', `'${content}'`);
    }
    declarations.add('padding-right', '0.3em');
    return declarations.value as string;
};
