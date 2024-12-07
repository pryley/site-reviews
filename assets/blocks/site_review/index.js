!function(){"use strict";var e={n:function(t){var s=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(s,{a:s}),s},d:function(t,s){for(var n in s)e.o(s,n)&&!e.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:s[n]})},o:function(e,t){return Object.prototype.hasOwnProperty.call(e,t)}},t=window.wp.apiFetch,s=e.n(t),n=window.wp.data;const i="site-reviews";if(!(0,n.select)(i)){const e={options:{},selectedValues:{},suggestedValues:{}},t={setOptions:(e,t)=>({type:"SET_OPTIONS",endpoint:e,options:t}),setSelectedValues:(e,t)=>({type:"SET_SELECTED_VALUES",endpoint:e,selectedValues:t}),setSuggestedValues:(e,t)=>({type:"SET_SUGGESTED_VALUES",endpoint:e,suggestedValues:t})},s=(t=e,s)=>{switch(s.type){case"SET_OPTIONS":return{...t,options:{...t.options,[s.endpoint]:s.options}};case"SET_SELECTED_VALUES":return{...t,selectedValues:{...t.selectedValues,[s.endpoint]:s.selectedValues}};case"SET_SUGGESTED_VALUES":return{...t,suggestedValues:{...t.suggestedValues,[s.endpoint]:s.suggestedValues}};default:return t}},o={getOptions:(e,t)=>e.options[t]||[],getSelectedValues:(e,t)=>e.selectedValues[t]||[],getSuggestedValues:(e,t)=>e.suggestedValues[t]||[]},r=(0,n.createReduxStore)(i,{reducer:s,actions:t,selectors:o,controls:{}});(0,n.register)(r)}var o,r=i,a=window.wp.i18n,l=window.wp.url,c=window.wp.components,d=window.wp.compose,p=window.wp.element,u=window.ReactJSXRuntime,h=({endpoint:e,onChange:t,placeholder:i,value:o,...h})=>{const[g,w]=(0,p.useState)(!1),[x,v]=(0,p.useState)(!1),[m,f]=(0,p.useState)(""),_=(0,p.useRef)(!1),b=(0,n.useSelect)((t=>t(r).getOptions(e)),[]),{baseControlProps:S,controlProps:y}=(0,c.useBaseControlProps)(h),{options:j,...C}=y,{setOptions:E}=(0,n.useDispatch)(r),V=()=>({path:(0,l.addQueryArgs)(e,{include:o,search:m})}),O=(0,d.useDebounce)(f,250),k=e=>({label:`${e.id}: ${e.title}`,title:e.title,value:String(e.id)}),T=({item:e})=>{const{title:t,value:s}=e,n=(e=>{const t=m.toLocaleLowerCase();if(0===t.length)return null;const s=e.toLocaleLowerCase().indexOf(t);return{afterMatch:e.substring(s+t.length),beforeMatch:e.substring(0,s),match:e.substring(s,s+t.length)}})(t);return(0,u.jsxs)(c.__experimentalHStack,{children:[n?(0,u.jsxs)(c.__experimentalText,{color:"inherit",numberOfLines:1,"aria-label":t,children:[n.beforeMatch,(0,u.jsx)("strong",{className:"components-form-token-field__suggestion-match",children:n.match}),n.afterMatch]}):(0,u.jsx)(c.__experimentalText,{color:"inherit",children:t}),(0,u.jsx)(c.__experimentalText,{color:"inherit",size:"small",style:{flexShrink:0,opacity:"0.5"},children:s})]})};return(0,p.useEffect)((()=>{(async()=>{_.current||(!b.length&&o?(w(!0),s()(V()).then((t=>{_.current=!0,E(e,t.map(k))})).finally((()=>{w(!1)}))):_.current=!0)})()}),[]),(0,p.useEffect)((()=>{(async()=>{m.length<2||(v(!0),s()(V()).then((t=>{E(e,t.map(k))})).finally((()=>{v(!1)})))})()}),[m]),(0,u.jsx)(c.BaseControl,{__nextHasNoMarginBottom:!0,...S,children:(0,u.jsx)(c.Animate,{type:(g||x)&&"loading",children:({className:e})=>(0,u.jsx)(c.ComboboxControl,{__experimentalRenderItem:T,__next40pxDefaultSize:!0,__nextHasNoMarginBottom:!0,allowReset:!0,className:e,expandOnFocus:!1,options:b,onChange:t,onFilterValueChange:O,placeholder:i||(0,a._x)("Search...","admin-text","site-reviews"),value:o,...C})})})},g=({endpoint:e,onChange:t,value:i,...o})=>{const[a,l]=(0,p.useState)(!1),d=(0,n.useSelect)((t=>t(r).getOptions(e)),[]),{baseControlProps:h,controlProps:g}=(0,c.useBaseControlProps)(o),{checked:w,label:__,...x}=g,{setOptions:v}=(0,n.useDispatch)(r);return(0,p.useEffect)((()=>{d.length||(l(!0),s()({path:e}).then((t=>{const s=t.map((e=>({label:e.title,value:String(e.id)})));v(e,s)})).finally((()=>{l(!1)})))}),[]),(0,u.jsxs)(c.BaseControl,{__nextHasNoMarginBottom:!0,...h,children:[!a&&d.map((e=>(0,u.jsx)(c.ToggleControl,{__nextHasNoMarginBottom:!0,label:e.label,checked:i.includes(e.value),onChange:s=>((e,s)=>{const n=s?[...i,e]:i.filter((t=>t!==e));t(n)})(e.value,s),...x},e.value))),a&&(0,u.jsx)(c.Spinner,{})]})},w=window.wp.serverSideRender,x=e.n(w),v=window.wp.blockEditor,m=({className:e="ssr",inspectorAdvancedControls:t={},inspectorControls:s={},props:n,renderCallback:i})=>{const{attributes:o,name:r}=n,l=r.replace("/","."),d=(0,p.useRef)(null),h=(0,p.useCallback)((t=>{for(const s of t)if("childList"===s.type)for(const t of s.addedNodes)if("DIV"===t.tagName&&t.classList.contains(e)){if("function"==typeof i){const e=t.firstElementChild;e.classList.add("glsr-"+window.getComputedStyle(e,null).getPropertyValue("direction")),i(t,d.current)}return}}),[i]);(0,p.useEffect)((()=>{if(!d.current)return;const e=new MutationObserver(h);return e.observe(d.current,{childList:!0,subtree:!0}),()=>e.disconnect()}),[h]);const g={panel_settings:(0,u.jsx)(c.PanelBody,{title:(0,a._x)("Settings","admin-text","site-reviews"),children:Object.values(wp.hooks.applyFilters(`${l}.InspectorControls`,s,n))})};return(0,u.jsxs)(u.Fragment,{children:[(0,u.jsx)(v.InspectorControls,{children:Object.values(wp.hooks.applyFilters(`${l}.InspectorPanels`,g,n))}),(0,u.jsx)(v.InspectorAdvancedControls,{children:Object.values(wp.hooks.applyFilters(`${l}.InspectorAdvancedControls`,t,n))}),(0,u.jsx)("div",{...(0,v.useBlockProps)({ref:d}),children:(0,u.jsx)(c.Disabled,{isDisabled:!0,children:(0,u.jsx)(x(),{attributes:o,block:r,className:e,LoadingResponsePlaceholder:({children:e,showLoader:t})=>e?(0,u.jsxs)("div",{style:{position:"relative"},children:[t&&(0,u.jsx)("div",{style:{position:"absolute",top:"50%",left:"50%",marginTop:"-9px",marginLeft:"-9px"},children:(0,u.jsx)(c.Spinner,{})}),(0,u.jsx)("div",{style:{opacity:t?"0.3":1},children:e})]}):(0,u.jsxs)("div",{className:"block-editor-warning",children:[(0,u.jsx)(c.Spinner,{style:{marginBlockStart:0,marginInlineStart:0}}),(0,u.jsx)("p",{className:"block-editor-warning__message",children:(0,a._x)("Loading block...","admin-text","site-reviews")})]}),skipBlockSupportAttributes:!0})})})]})},f=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"site-reviews/review","version":"2.0.0","title":"Single Review","description":"Display a single review.","category":"site-reviews","example":{},"textdomain":"site-reviews","attributes":{"className":{"default":"","type":"string"},"hide":{"default":[],"items":{"type":"string"},"type":"array"},"id":{"default":"","type":"string"},"post_id":{"default":"","type":"string"}},"editorScript":"file:./index.jsx","script":"site-reviews","keywords":["review","site reviews"],"supports":{"html":false}}'),_=window.wp.blocks,b=window.React;function S(){return S=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var s=arguments[t];for(var n in s)({}).hasOwnProperty.call(s,n)&&(e[n]=s[n])}return e},S.apply(null,arguments)}const y=e=>b.createElement("svg",S({xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},e),o||(o=b.createElement("path",{d:"M18.646 1.821a3.44 3.44 0 0 1 1.772.481 3.57 3.57 0 0 1 1.281 1.285 3.46 3.46 0 0 1 .479 1.775v8.126a3.5 3.5 0 0 1-.477 1.78 3.55 3.55 0 0 1-1.283 1.298 3.44 3.44 0 0 1-1.772.481h-6.28l-.064.052-6.144 4.914c-.392.304-.976.263-1.359-.066a1.18 1.18 0 0 1-.337-1.233l1.377-3.742h-.485a3.44 3.44 0 0 1-1.567-.369l-.206-.112A3.57 3.57 0 0 1 2.3 15.206a3.46 3.46 0 0 1-.479-1.775V5.362A3.46 3.46 0 0 1 2.3 3.587a3.57 3.57 0 0 1 1.281-1.285 3.44 3.44 0 0 1 1.772-.481h13.292zm0 1.5H5.354a1.94 1.94 0 0 0-1.01.273 2.07 2.07 0 0 0-.749.752 1.96 1.96 0 0 0-.273 1.016v8.069a1.96 1.96 0 0 0 .273 1.016 2.07 2.07 0 0 0 .749.752 1.94 1.94 0 0 0 1.01.273H7.99l-.371 1.009-1.275 3.464.103-.082 1.871-1.496 1.1-.88 1.955-1.564.263-.21.205-.164h6.806a1.94 1.94 0 0 0 1.01-.273c.315-.186.561-.435.747-.757a2 2 0 0 0 .275-1.029V5.362a1.96 1.96 0 0 0-.273-1.016 2.07 2.07 0 0 0-.749-.752 1.94 1.94 0 0 0-1.01-.273zm-5.783 1.995v5.757c0 .087-.028.444-.038.592v.061l1.365-.042v1.17H9.81V11.81l.688-.013.407-.029c.084-.021.168-.042.232-.105s.105-.147.147-.274a1 1 0 0 0 .034-.136V7.578H9.81v-.754c.829-.226 1.507-.829 1.96-1.507h1.093z"})));(0,_.registerBlockType)(f,{edit:function(e){const{attributes:t,setAttributes:s}=e,n={post_id:(0,u.jsx)(h,{endpoint:"/site-reviews/v1/shortcode/site_review?option=post_id",help:(0,a._x)("Search for a review to display.","admin-text","site-reviews"),label:(0,a._x)("Review Post ID","admin-text","site-reviews"),onChange:e=>s({post_id:e}),placeholder:(0,a._x)("Search Reviews...","admin-text","site-reviews"),value:t.post_id},"post_id"),hide:(0,u.jsx)(g,{endpoint:"/site-reviews/v1/shortcode/site_review?option=hide",label:(0,a._x)("Hide Options","admin-text","site-reviews"),onChange:e=>s({hide:e}),value:t.hide},"hide")},i={id:(0,u.jsx)(c.TextControl,{__next40pxDefaultSize:!0,__nextHasNoMarginBottom:!0,help:(0,a._x)("This should be a unique value.","admin-text","site-reviews"),label:(0,a._x)("Custom ID","admin-text","site-reviews"),onChange:e=>s({id:e}),value:t.id},"id")};return(0,u.jsx)(m,{inspectorControls:n,inspectorAdvancedControls:i,props:e})},icon:(0,u.jsx)(y,{width:24,height:24})})}();