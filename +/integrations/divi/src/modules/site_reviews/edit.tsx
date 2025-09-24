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

const blockName = 'site-reviews/reviews';

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

    const parseAttrs = () => {
        const attrNames = [
            ...getAttrNamesByGroup(name, 'contentGeneral'),
            ...getAttrNamesByGroup(name, 'contentDisplay'),
            ...getAttrNamesByGroup(name, 'contentHide'),
        ];
        const results = {};
        attrNames.forEach(attName => {
            let key = attName.split('.').pop();
            // @ts-expect-error
            results[key] = getAttrByMode(get(attrs, attName));
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
