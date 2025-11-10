import React, { type ReactElement, useEffect, useRef } from 'react';
import useDeepCompareEffect from 'use-deep-compare-effect';
import { applyFilters } from '@wordpress/hooks';
import { debounce, isNil, omitBy } from 'lodash';
import { ModuleContainer } from '@divi/module';
// @ts-expect-error
import { getAttrByMode, getAttrNamesByGroup } from '@divi/module-utils';
import { Loading } from '@divi/ui-library';
import { RawHTML } from '@wordpress/element';
import { useFetch } from '@divi/rest';
import { type EditProps } from './types';

const ModuleEdit = (props: EditProps): ReactElement => {
    // @ts-expect-error
    const moduleEditProps: EditProps = applyFilters('site-reviews.divi.module_edit', props);
    const {
        attrs,
        elements,
        id,
        isFirst,
        isLast,
        name,
        moduleClassnames,
        ModuleScriptData,
        ModuleStyles,
    } = moduleEditProps;
    const {
        abort,
        fetch,
        isLoading,
        response: { rendered },
    } = useFetch<{ rendered: string }>({ rendered: '' });

    const blockName = name.replace('glsr-divi', 'site-reviews');
    const moduleRef = useRef(null);

    const parseAttrs = (): Record<string, any> => {
        const settings = attrs?.shortcode?.advanced || {};
        const allowedAttrNames = Object.keys(elements?.moduleMetadata?.attributes?.shortcode?.settings?.advanced || settings);
        const attrNames = Object.keys(settings);
        const results: Record<string, any> = {
            context: 'edit',
        };
        attrNames.forEach((attName: string) => {
            let value = getAttrByMode(settings[attName]);
            if (Array.isArray(value) && value.every(obj => obj?.value !== undefined)) {
                value = value.map(obj => obj.value);
            }
            // @ts-expect-error
            if (['on', 'off'].includes(value)) {
                value = 'on' === value ? 1 : 0;
            }
            if (!allowedAttrNames.includes(attName)) {
                value = null;
            }
            results[`attributes[${attName}]`] = value;
        });
        return results;
    };

    const attributes = omitBy(parseAttrs(), isNil);

    const debouncedFetch = debounce(() => {
        fetch({
            data: attributes,
            method: 'GET',
            restRoute: `/wp/v2/block-renderer/${blockName}`,
        }).catch(error => {
            if ('AbortError' !== error.name) {
                console.error(error);
            }
        });
    }, 300);

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
        return <Loading />;
    };

    const renderModule = () => {
        if (isLoading) {
            return null;
        }
        return <RawHTML>{rendered}</RawHTML>;
    };

    useEffect(() => {
        // @ts-expect-error
        window?.GLSR_init && window.GLSR_init(`block:${blockName}`);
    }, [rendered]);

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
};

export { ModuleEdit };
