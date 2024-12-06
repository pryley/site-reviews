!function(){"use strict";var e={n:function(t){var s=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(s,{a:s}),s},d:function(t,s){for(var n in s)e.o(s,n)&&!e.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:s[n]})},o:function(e,t){return Object.prototype.hasOwnProperty.call(e,t)}},t=window.wp.apiFetch,s=e.n(t),n=window.wp.data;const i="site-reviews";if(!(0,n.select)(i)){const e={options:{},selectedValues:{},suggestedValues:{}},t={setOptions:(e,t)=>({type:"SET_OPTIONS",endpoint:e,options:t}),setSelectedValues:(e,t)=>({type:"SET_SELECTED_VALUES",endpoint:e,selectedValues:t}),setSuggestedValues:(e,t)=>({type:"SET_SUGGESTED_VALUES",endpoint:e,suggestedValues:t})},s=(t=e,s)=>{switch(s.type){case"SET_OPTIONS":return{...t,options:{...t.options,[s.endpoint]:s.options}};case"SET_SELECTED_VALUES":return{...t,selectedValues:{...t.selectedValues,[s.endpoint]:s.selectedValues}};case"SET_SUGGESTED_VALUES":return{...t,suggestedValues:{...t.suggestedValues,[s.endpoint]:s.suggestedValues}};default:return t}},a={getOptions:(e,t)=>e.options[t]||[],getSelectedValues:(e,t)=>e.selectedValues[t]||[],getSuggestedValues:(e,t)=>e.suggestedValues[t]||[]},o=(0,n.createReduxStore)(i,{reducer:s,actions:t,selectors:a,controls:{}});(0,n.register)(o)}var a,o=i,r=window.wp.i18n,l=window.wp.components,c=window.wp.element,d=window.ReactJSXRuntime,u=({endpoint:e,hideIfEmpty:t=!1,placeholder:i,...a})=>{const[u,p]=(0,c.useState)(!1),m=(0,n.useSelect)((t=>t(o).getOptions(e)),[]),{options:g,...h}=a,{setOptions:x}=(0,n.useDispatch)(o);return(0,c.useEffect)((()=>{m.length||(p(!0),s()({path:e}).then((t=>{const s=t.map((e=>({label:e.title,value:String(e.id)})));x(e,s)})).finally((()=>{p(!1)})))}),[]),(0,d.jsx)(d.Fragment,{children:(!t||m.length>1)&&(0,d.jsx)(l.Animate,{type:u&&"loading",children:({className:e})=>(0,d.jsx)(l.BaseControl,{__nextHasNoMarginBottom:!0,children:(0,d.jsx)(l.ComboboxControl,{__next40pxDefaultSize:!0,__nextHasNoMarginBottom:!0,allowReset:!0,className:e,expandOnFocus:!1,options:m,placeholder:i||(0,r._x)("Select...","admin-text","site-reviews"),...h})})})})},p=window.wp.url,m=window.wp.compose,g=({endpoint:e,help:t,label:i,onChange:a,placeholder:u,prefetch:g=!1,value:h})=>{const[x,_]=(0,c.useState)(!1),[v,w]=(0,c.useState)(!1),[f,S]=(0,c.useState)(""),[b,y]=(0,c.useState)(new Map),C=(0,c.useRef)(!1),j=(0,c.useRef)(!0),E=(0,n.useSelect)((t=>t(o).getSelectedValues(e)),[]),T=(0,n.useSelect)((t=>t(o).getSuggestedValues(e)),[]),{setSelectedValues:V,setSuggestedValues:O}=(0,n.useDispatch)(o),N=()=>({path:(0,p.addQueryArgs)(e,{include:h.join(","),search:f})}),R=(0,m.useDebounce)(S,500),L=e=>({id:e.id,title:e.title,value:isNaN(parseFloat(e.id))?e.title:`${e.title} (${e.id})`}),M=t=>{t.map(((e,s)=>{if("string"==typeof e){const n=T.find((t=>t.value===e));n&&(t[s]=n)}return e})),V(e,t),a(t.map((e=>e.id)))},B=({item:e})=>{const t=b.get(e);if(!t)return null;const{id:s,title:n}=t,i=(e=>{const t=f.toLocaleLowerCase();if(0===t.length)return null;const s=e.toLocaleLowerCase().indexOf(t);return{afterMatch:e.substring(s+t.length),beforeMatch:e.substring(0,s),match:e.substring(s,s+t.length)}})(n);return(0,d.jsxs)(l.__experimentalHStack,{children:[i?(0,d.jsxs)("span",{"aria-label":n,children:[i.beforeMatch,(0,d.jsx)("strong",{className:"components-form-token-field__suggestion-match",children:i.match}),i.afterMatch]}):(0,d.jsx)(l.__experimentalText,{color:"inherit",children:n}),(0,d.jsx)(l.__experimentalText,{color:"inherit",size:"small",style:{opacity:"0.5"},children:String(s)})]})},D=e=>T.some((t=>t.value===e));return(0,c.useEffect)((()=>{(async()=>{C.current||(T.length||!h.length&&!1===g?C.current=!0:(_(!0),s()(N()).then((t=>{C.current=!0;const s=[],n=[];t.forEach((e=>{s.push(L(e)),h.includes(e.id)&&n.push(L(e))})),V(e,n),O(e,s)})).finally((()=>{_(!1)}))))})()}),[]),(0,c.useEffect)((()=>{(async()=>{f.length<2||(j.current?j.current=!1:(w(!0),s()(N()).then((t=>{O(e,t.map(L))})).finally((()=>{w(!1)}))))})()}),[f]),(0,c.useEffect)((()=>{y(new Map(T.map((e=>[e.value,e]))))}),[T]),(0,d.jsxs)(l.BaseControl,{__nextHasNoMarginBottom:!0,children:[(0,d.jsx)(l.Animate,{type:(x||v)&&"loading",children:({className:e})=>(0,d.jsx)(l.FormTokenField,{__experimentalExpandOnFocus:!0,__experimentalRenderItem:B,__experimentalShowHowTo:!1,__experimentalValidateInput:D,__next40pxDefaultSize:!0,__nextHasNoMarginBottom:!0,className:e,disabled:x,label:i||"",onChange:M,onInputChange:R,placeholder:u||(0,r._x)("Search...","admin-text","site-reviews"),suggestions:T.map((e=>e.value)),value:E})}),t&&(0,d.jsx)(l.__experimentalText,{variant:"muted",size:"small",children:t})]})},h=({endpoint:e,onChange:t,value:i,...a})=>{const[r,u]=(0,c.useState)(!1),p=(0,n.useSelect)((t=>t(o).getOptions(e)),[]),{baseControlProps:m,controlProps:g}=(0,l.useBaseControlProps)(a),{checked:h,label:__,...x}=g,{setOptions:_}=(0,n.useDispatch)(o);return(0,c.useEffect)((()=>{p.length||(u(!0),s()({path:e}).then((t=>{const s=t.map((e=>({label:e.title,value:String(e.id)})));_(e,s)})).finally((()=>{u(!1)})))}),[]),(0,d.jsxs)(l.BaseControl,{__nextHasNoMarginBottom:!0,...m,children:[!r&&p.map((e=>(0,d.jsx)(l.ToggleControl,{__nextHasNoMarginBottom:!0,label:e.label,checked:i.includes(e.value),onChange:s=>((e,s)=>{const n=s?[...i,e]:i.filter((t=>t!==e));t(n)})(e.value,s),...x},e.value))),r&&(0,d.jsx)(l.Spinner,{})]})},x=e=>(0,d.jsxs)(l.__experimentalToggleGroupControl,{__next40pxDefaultSize:!0,__nextHasNoMarginBottom:!0,...e,children:[(0,d.jsx)(l.__experimentalToggleGroupControlOption,{value:!1,label:(0,r._x)("No","admin-text","site-reviews")}),(0,d.jsx)(l.__experimentalToggleGroupControlOption,{value:!0,label:(0,r._x)("Yes","admin-text","site-reviews")})]}),_=window.wp.serverSideRender,v=e.n(_),w=window.wp.blockEditor,f=({className:e="ssr",inspectorAdvancedControls:t={},inspectorControls:s={},name:n,namespace:i="site-reviews",props:a,renderCallback:o})=>{const{attributes:u}=a,p=(0,c.useRef)(null),m=(0,c.useCallback)((t=>{for(const s of t)if("childList"===s.type)for(const t of s.addedNodes)if("DIV"===t.tagName&&t.classList.contains(e)){if("function"==typeof o){const e=t.firstElementChild;e.classList.add("glsr-"+window.getComputedStyle(e,null).getPropertyValue("direction")),o(t,p.current)}return}}),[o]);(0,c.useEffect)((()=>{if(!p.current)return;const e=new MutationObserver(m);return e.observe(p.current,{childList:!0,subtree:!0}),()=>e.disconnect()}),[m]);const g={panel_settings:(0,d.jsx)(l.PanelBody,{title:(0,r._x)("Settings","admin-text","site-reviews"),children:Object.values(wp.hooks.applyFilters(`${i}.${n}.InspectorControls`,s,a))})};return(0,d.jsxs)(d.Fragment,{children:[(0,d.jsx)(w.InspectorControls,{children:Object.values(wp.hooks.applyFilters(`${i}.${n}.InspectorPanels`,g,a))}),(0,d.jsx)(w.InspectorAdvancedControls,{children:Object.values(wp.hooks.applyFilters(`${i}.${n}.InspectorAdvancedControls`,t,a))}),(0,d.jsx)("div",{...(0,w.useBlockProps)({ref:p}),children:(0,d.jsx)(l.Disabled,{isDisabled:!0,children:(0,d.jsx)(v(),{attributes:u,block:`${i}/${n}`,className:e,skipBlockSupportAttributes:!0})})})]})},S=JSON.parse('{"UU":"site-reviews/summary"}'),b=window.wp.blocks,y=window.React;function C(){return C=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var s=arguments[t];for(var n in s)({}).hasOwnProperty.call(s,n)&&(e[n]=s[n])}return e},C.apply(null,arguments)}const j=e=>y.createElement("svg",C({xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},e),a||(a=y.createElement("path",{d:"M20.694 18.429c.406 0 .735.336.735.75v1.5c0 .414-.329.75-.735.75H3.306a.743.743 0 0 1-.735-.75v-1.5c0-.414.329-.75.735-.75zm-.513 1.071H6.39c-.097 0-.176.064-.176.143v.571c0 .079.079.143.176.143h13.79c.097 0 .176-.064.176-.143v-.571c0-.079-.079-.143-.176-.143zm.513-4.929c.406 0 .735.336.735.75v1.5c0 .414-.329.75-.735.75H3.306a.743.743 0 0 1-.735-.75v-1.5c0-.414.329-.75.735-.75zm-.527 1.188H8.976c-.105 0-.19.064-.19.143v.571c0 .079.085.143.19.143h11.192c.105 0 .19-.064.19-.143v-.571c0-.079-.085-.143-.19-.143zm.527-5.045c.406 0 .735.336.735.75v1.5c0 .414-.329.75-.735.75H3.306a.743.743 0 0 1-.735-.75v-1.5c0-.414.329-.75.735-.75zm-.551 1.071h-2.571c-.118 0-.214.064-.214.143v.571c0 .079.096.143.214.143h2.571c.118 0 .214-.064.214-.143v-.571c0-.079-.096-.143-.214-.143M6.944 1.786l-.083 1.678-3.253.062V5.37c.083-.041.166-.083.269-.104l.332-.083.174-.028.08-.009.078-.004c.104 0 .228-.021.332-.021q.652 0 1.119.186a2 2 0 0 1 .767.518c.207.228.373.456.477.746s.145.58.145.87c0 .394-.062.746-.207 1.057s-.352.559-.622.767a2.6 2.6 0 0 1-.85.456 3.2 3.2 0 0 1-.974.145c-.352 0-.663-.041-.912-.124s-.497-.207-.684-.332-.311-.29-.414-.456-.145-.332-.145-.497a.8.8 0 0 1 .062-.311 1 1 0 0 1 .145-.249c.062-.083.145-.124.228-.186s.186-.062.29-.062q.186 0 .311.062c.125.062.145.104.207.166a.3.3 0 0 1 .07.114l.033.114c.021.083.041.145.041.228 0 .062 0 .124-.021.207s-.062.145-.104.207c.021.062.083.124.124.166s.145.083.207.124.166.062.249.083a1 1 0 0 0 .228.021 1.1 1.1 0 0 0 .539-.124c.145-.083.249-.207.352-.352s.145-.29.186-.456a2.3 2.3 0 0 0 .062-.539v-.352q0-.186-.062-.373c-.021-.145-.062-.269-.104-.394s-.145-.249-.228-.332-.207-.186-.332-.249-.311-.104-.518-.104c-.145 0-.332.041-.539.083s-.456.166-.705.311l-.435-.352-.041-3.461h2.694c.124 0 .228 0 .311-.021s.186-.041.269-.104.104-.104.145-.207a.9.9 0 0 0 .062-.332h.642zm5.203-.293a.22.22 0 0 1 .207.141l1.083 2.594 2.814.225a.22.22 0 0 1 .194.154l.035.11c.03.085.004.18-.066.238l-2.123 1.823.652 2.735a.23.23 0 0 1-.084.233l-.128.07a.22.22 0 0 1-.247 0L12.09 8.364 9.681 9.83a.22.22 0 0 1-.247 0l-.097-.066a.23.23 0 0 1-.084-.233l.634-2.753-2.123-1.823a.22.22 0 0 1-.079-.238l.035-.11a.215.215 0 0 1 .194-.154l2.814-.225 1.079-2.594a.22.22 0 0 1 .192-.141z"})));(0,b.registerBlockType)(S.UU,{edit:function(e){const{attributes:t,setAttributes:s}=e;s({post_id:jQuery("#post_ID").val()});const n={assigned_posts:(0,d.jsx)(g,{endpoint:"/site-reviews/v1/shortcode/site_reviews_summary?option=assigned_posts",label:(0,r._x)("Limit Reviews by Assigned Pages","admin-text","site-reviews"),onChange:e=>s({assigned_posts:e}),placeholder:(0,r._x)("Search Pages...","admin-text","site-reviews"),prefetch:!0,value:t.assigned_posts},"assigned_posts"),assigned_terms:(0,d.jsx)(g,{endpoint:"/site-reviews/v1/shortcode/site_reviews_summary?option=assigned_terms",label:(0,r._x)("Limit Reviews by Categories","admin-text","site-reviews"),onChange:e=>s({assigned_terms:e}),placeholder:(0,r._x)("Search Categories...","admin-text","site-reviews"),value:t.assigned_terms},"assigned_terms"),assigned_users:(0,d.jsx)(g,{endpoint:"/site-reviews/v1/shortcode/site_reviews_summary?option=assigned_users",label:(0,r._x)("Limit Reviews by Assigned Users","admin-text","site-reviews"),onChange:e=>s({assigned_users:e}),placeholder:(0,r._x)("Search Users...","admin-text","site-reviews"),prefetch:!0,value:t.assigned_users},"assigned_users"),terms:(0,d.jsx)(u,{endpoint:"/site-reviews/v1/shortcode/site_reviews_summary?option=terms",label:(0,r._x)("Limit Reviews by Accepted Terms","admin-text","site-reviews"),onChange:e=>s({terms:e}),placeholder:(0,r._x)("Select Review Terms...","admin-text","site-reviews"),value:t.terms},"terms"),type:(0,d.jsx)(u,{endpoint:"/site-reviews/v1/shortcode/site_reviews_summary?option=type",hideIfEmpty:!0,label:(0,r._x)("Limit Reviews by Type","admin-text","site-reviews"),onChange:e=>s({type:e}),placeholder:(0,r._x)("Select a Review Type...","admin-text","site-reviews"),value:t.type},"type"),rating:(0,d.jsx)(l.RangeControl,{__next40pxDefaultSize:!0,__nextHasNoMarginBottom:!0,label:(0,r._x)("Minimum Rating","admin-text","site-reviews"),min:GLSR.minrating,max:GLSR.maxrating,onChange:e=>s({rating:e}),value:t.rating},"rating"),schema:(0,d.jsx)(x,{help:(0,r._x)("The schema should only be enabled once on your page.","admin-text","site-reviews"),onChange:e=>s({schema:e}),label:(0,r._x)("Enable the Schema?","admin-text","site-reviews"),value:t.schema},"schema"),hide:(0,d.jsx)(h,{endpoint:"/site-reviews/v1/shortcode/site_reviews_summary?option=hide",label:(0,r._x)("Hide Options","admin-text","site-reviews"),onChange:e=>s({hide:e}),value:t.hide},"hide")},i={rating_field:(0,d.jsx)(l.TextControl,{__next40pxDefaultSize:!0,__nextHasNoMarginBottom:!0,help:(0,r._x)("Use the Review Forms addon to add custom rating fields.","admin-text","site-reviews"),label:(0,r._x)("Custom Rating Field Name","admin-text","site-reviews"),onChange:e=>s({rating_field:e}),value:t.rating_field},"rating_field"),id:(0,d.jsx)(l.TextControl,{__next40pxDefaultSize:!0,__nextHasNoMarginBottom:!0,help:(0,r._x)("This should be a unique value.","admin-text","site-reviews"),label:(0,r._x)("Custom ID","admin-text","site-reviews"),onChange:e=>s({id:e}),value:t.id},"id")};return(0,d.jsx)(f,{inspectorControls:n,inspectorAdvancedControls:i,name:"summary",props:e})},icon:(0,d.jsx)(j,{width:24,height:24})})}();