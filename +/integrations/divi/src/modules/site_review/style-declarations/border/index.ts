import { type DeclarationFunctionProps } from '@divi/module';
import { StyleDeclarations } from '@divi/style-library';
import { type Module } from '@divi/types';


/**
 * Style declaration for Blog Module If it has border radius set.
 *
 * @returns {string}
 */
export const borderStyleDeclaration = ({
    attrValue,
}: DeclarationFunctionProps<Module.Element.Decoration.Border.AttributeValue>): string => {
    const { radius } = attrValue;

    const declarations = new StyleDeclarations({
        returnType: 'string',
        important:  false,
    });

    // Check if any radius value is set.
    if (radius) {
        declarations.add('overflow', 'hidden');
    }

    return declarations.value as string;
};
