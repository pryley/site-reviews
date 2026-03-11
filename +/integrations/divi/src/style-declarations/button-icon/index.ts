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
            'content': true,
            'line-height': true,
            'margin-left': true,
            'margin-right': true,
            'padding': true,
        },
        returnType: 'string',
    });
    declarations.add('position', 'relative');
    // custom icon disabled
    if ('off' === enable) {
        declarations.add('margin-right', '-1em');
        return declarations.value as string;
    }

    declarations.add('line-height', '1');
    declarations.add('top', 'auto');
    declarations.add('transform', 'none');

    if (isEmpty(icon?.settings)) {
        // has default icon
        if ('left' === icon?.placement) {
            declarations.add('margin-left', '-1.3em');
            declarations.add('padding', '0 0 0 0.3em');
        } else {
            declarations.add('margin-right', '-1.3em');
            declarations.add('padding', '0 0.3em 0 0');
        }
    } else {
        // has custom icon
        const content = escapeFontIcon(processFontIcon(icon?.settings));
        declarations.add('content', `'${content}'`);
        if ('left' === icon?.placement) {
            declarations.add('margin-left', '-1.5em');
            declarations.add('padding', '0 0.5em 0 0');
        } else {
            declarations.add('margin-right', '-1.5em');
            declarations.add('padding', '0 0 0 0.2em');
        }
    }


    return declarations.value as string;
};
