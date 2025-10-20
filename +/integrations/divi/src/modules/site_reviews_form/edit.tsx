import React, {
    type ReactElement,
    useEffect,
    useRef,
} from 'react';
import useDeepCompareEffect from 'use-deep-compare-effect';
import { debounce, get, isNil, omitBy } from 'lodash';
import { ModuleContainer } from '@divi/module';
// @ts-expect-error
import { getAttrByMode, getAttrNamesByGroup } from '@divi/module-utils';
import { Loading } from '@divi/ui-library';
import { RawHTML } from '@wordpress/element';
import { useFetch } from '@divi/rest';

import { moduleClassnames } from './module-classnames';
import { ModuleScriptData } from './module-script-data';
import { ModuleStyles } from './module-styles';

import { type EditProps } from './types';

const blockName = 'site-reviews/form';

const ModuleEdit = (props: EditProps): ReactElement => {
    const {
        attrs,
        elements,
        id,
        isFirst,
        isLast,
        name,
    } = props;

    const {
        abort,
        fetch,
        isLoading,
        response: { rendered },
    } = useFetch<{ rendered: string }>({ rendered: '' });

    const moduleRef = useRef(null);

    const parseAttrs = (): Record<string, any> => {
        const attrNames = [
            ...getAttrNamesByGroup(name, 'contentGeneral'),
            ...getAttrNamesByGroup(name, 'contentDisplay'),
            ...getAttrNamesByGroup(name, 'contentHide'),
        ];
        const results: Record<string, any> = {};
        attrNames.forEach((attName: string) => {
            const key = attName.split('.').pop();
            let value = getAttrByMode(get(attrs, attName));
            if (Array.isArray(value) && value.every(obj => obj?.value !== undefined)) {
                value = value.map(obj => obj.value);
            }
            // @ts-expect-error
            if (['on','off'].includes(value)) {
                value = 'on' === value ? 1 : 0;
            }
            results[key] = value;
        });
        return results;
    }

    const attributes = omitBy(parseAttrs(), isNil);

    const debouncedFetch = debounce(() => {
        fetch({
            method: 'POST',
            restRoute: `/wp/v2/block-renderer/${blockName}`,
            data: {
                attributes,
                context: 'edit',
            },
        }).catch(error => {
            if ('AbortError' !== error.name) {
                console.error(error);
            }
        });
    }, 300)

    useDeepCompareEffect(() => {
        debouncedFetch();
        return () => {
            abort(); // abort active HTTP requests
            debouncedFetch.cancel(); // cancel queued requests
        };
    }, [attributes]);

    const renderLoading = () => {
        if (!isLoading) {
            return null;
        }
        return (
            <Loading />
        );
    };

    const renderModule = () => {
        if (isLoading) {
            return null;
        }
        return (
            <RawHTML>{ rendered }</RawHTML>
        );
    }

    useEffect(() => {
        // @ts-expect-error
        window?.GLSR_init && (window.GLSR_init(`block:${blockName}`))
    }, [props]);

    return (
        <ModuleContainer
            attrs={attrs}
            classnamesFunction={moduleClassnames}
            domRef={moduleRef}
            elements={elements}
            id={id}
            isFirst={isFirst}
            isLast={isLast}
            name={name}
            scriptDataComponent={ModuleScriptData}
            stylesComponent={ModuleStyles}
        >
            {elements.styleComponents({ attrName: 'module' })}
            {renderLoading()}
            {renderModule()}
        </ModuleContainer>
    );
}

export {
    ModuleEdit,
};
