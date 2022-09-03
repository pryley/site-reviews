/*! For license information please see site-reviews-blocks.js.LICENSE.txt */
!function(){"use strict";var e={408:function(e,t,n){var r=n(424),s=60103,a=60106;var i=60109,o=60110,l=60112;var c=60115,u=60116;if("function"==typeof Symbol&&Symbol.for){var p=Symbol.for;s=p("react.element"),a=p("react.portal"),p("react.fragment"),p("react.strict_mode"),p("react.profiler"),i=p("react.provider"),o=p("react.context"),l=p("react.forward_ref"),p("react.suspense"),c=p("react.memo"),u=p("react.lazy")}var d="function"==typeof Symbol&&Symbol.iterator;function m(e){for(var t="https://reactjs.org/docs/error-decoder.html?invariant="+e,n=1;n<arguments.length;n++)t+="&args[]="+encodeURIComponent(arguments[n]);return"Minified React error #"+e+"; visit "+t+" for the full message or use the non-minified dev environment for full errors and additional helpful warnings."}var f={isMounted:function(){return!1},enqueueForceUpdate:function(){},enqueueReplaceState:function(){},enqueueSetState:function(){}},g={};function v(e,t,n){this.props=e,this.context=t,this.refs=g,this.updater=n||f}function y(){}function h(e,t,n){this.props=e,this.context=t,this.refs=g,this.updater=n||f}v.prototype.isReactComponent={},v.prototype.setState=function(e,t){if("object"!=typeof e&&"function"!=typeof e&&null!=e)throw Error(m(85));this.updater.enqueueSetState(this,e,t,"setState")},v.prototype.forceUpdate=function(e){this.updater.enqueueForceUpdate(this,e,"forceUpdate")},y.prototype=v.prototype;var b=h.prototype=new y;b.constructor=h,r(b,v.prototype),b.isPureReactComponent=!0;var w={current:null},_=Object.prototype.hasOwnProperty,x={key:!0,ref:!0,__self:!0,__source:!0};function E(e,t,n){var r,a={},i=null,o=null;if(null!=t)for(r in void 0!==t.ref&&(o=t.ref),void 0!==t.key&&(i=""+t.key),t)_.call(t,r)&&!x.hasOwnProperty(r)&&(a[r]=t[r]);var l=arguments.length-2;if(1===l)a.children=n;else if(1<l){for(var c=Array(l),u=0;u<l;u++)c[u]=arguments[u+2];a.children=c}if(e&&e.defaultProps)for(r in l=e.defaultProps)void 0===a[r]&&(a[r]=l[r]);return{$$typeof:s,type:e,key:i,ref:o,props:a,_owner:w.current}}function k(e){return"object"==typeof e&&null!==e&&e.$$typeof===s}var O=/\/+/g;function C(e,t){return"object"==typeof e&&null!==e&&null!=e.key?function(e){var t={"=":"=0",":":"=2"};return"$"+e.replace(/[=:]/g,(function(e){return t[e]}))}(""+e.key):t.toString(36)}function S(e,t,n,r,i){var o=typeof e;"undefined"!==o&&"boolean"!==o||(e=null);var l=!1;if(null===e)l=!0;else switch(o){case"string":case"number":l=!0;break;case"object":switch(e.$$typeof){case s:case a:l=!0}}if(l)return i=i(l=e),e=""===r?"."+C(l,0):r,Array.isArray(i)?(n="",null!=e&&(n=e.replace(O,"$&/")+"/"),S(i,t,n,"",(function(e){return e}))):null!=i&&(k(i)&&(i=function(e,t){return{$$typeof:s,type:e.type,key:t,ref:e.ref,props:e.props,_owner:e._owner}}(i,n+(!i.key||l&&l.key===i.key?"":(""+i.key).replace(O,"$&/")+"/")+e)),t.push(i)),1;if(l=0,r=""===r?".":r+":",Array.isArray(e))for(var c=0;c<e.length;c++){var u=r+C(o=e[c],c);l+=S(o,t,n,u,i)}else if(u=function(e){return null===e||"object"!=typeof e?null:"function"==typeof(e=d&&e[d]||e["@@iterator"])?e:null}(e),"function"==typeof u)for(e=u.call(e),c=0;!(o=e.next()).done;)l+=S(o=o.value,t,n,u=r+C(o,c++),i);else if("object"===o)throw t=""+e,Error(m(31,"[object Object]"===t?"object with keys {"+Object.keys(e).join(", ")+"}":t));return l}function j(e,t,n){if(null==e)return e;var r=[],s=0;return S(e,r,"","",(function(e){return t.call(n,e,s++)})),r}function R(e){if(-1===e._status){var t=e._result;t=t(),e._status=0,e._result=t,t.then((function(t){0===e._status&&(t=t.default,e._status=1,e._result=t)}),(function(t){0===e._status&&(e._status=2,e._result=t)}))}if(1===e._status)return e._result;throw e._result}var P={current:null};function A(){var e=P.current;if(null===e)throw Error(m(321));return e}t.createElement=E},294:function(e,t,n){e.exports=n(408)},424:function(e){var t=Object.getOwnPropertySymbols,n=Object.prototype.hasOwnProperty,r=Object.prototype.propertyIsEnumerable;function s(e){if(null==e)throw new TypeError("Object.assign cannot be called with null or undefined");return Object(e)}e.exports=function(){try{if(!Object.assign)return!1;var e=new String("abc");if(e[5]="de","5"===Object.getOwnPropertyNames(e)[0])return!1;for(var t={},n=0;n<10;n++)t["_"+String.fromCharCode(n)]=n;if("0123456789"!==Object.getOwnPropertyNames(t).map((function(e){return t[e]})).join(""))return!1;var r={};return"abcdefghijklmnopqrst".split("").forEach((function(e){r[e]=e})),"abcdefghijklmnopqrst"===Object.keys(Object.assign({},r)).join("")}catch(e){return!1}}()?Object.assign:function(e,a){for(var i,o,l=s(e),c=1;c<arguments.length;c++){for(var u in i=Object(arguments[c]))n.call(i,u)&&(l[u]=i[u]);if(t){o=t(i);for(var p=0;p<o.length;p++)r.call(i,o[p])&&(l[o[p]]=i[o[p]])}}return l}}},t={};function n(r){var s=t[r];if(void 0!==s)return s.exports;var a=t[r]={exports:{}};return e[r](a,a.exports,n),a.exports}!function(){var e=wp.i18n._x,t=[{label:"- "+e("Select","admin-text","site-reviews")+" -",value:""},{label:"- "+e("Specific Post ID","admin-text","site-reviews")+" -",value:"custom"},{label:e("The Current Page","admin-text","site-reviews"),value:"post_id"},{label:e("The Parent Page","admin-text","site-reviews"),value:"parent_id"}],r=wp.i18n._x,s=[],a={label:"- "+r("Select","admin-text","site-reviews")+" -",value:""},i={label:"- "+r("Multiple Categories","admin-text","site-reviews")+" -",value:"custom"};wp.apiFetch({path:"/site-reviews/v1/categories?per_page=50"}).then((function(e){s.push(a),s.push(i),jQuery.each(e,(function(e,t){s.push({label:"".concat(t.name," (").concat(t.slug,")"),value:t.id})}))}));var o=s,l=wp.i18n._x,c=[{label:"- "+l("Select","admin-text","site-reviews")+" -",value:""},{label:"- "+l("Specific User ID","admin-text","site-reviews")+" -",value:"custom"},{label:l("The Logged-in user","admin-text","site-reviews"),value:"user_id"},{label:l("The Page author","admin-text","site-reviews"),value:"author_id"},{label:l("The Profile user (BuddyPress/Ultimate Member)","admin-text","site-reviews"),value:"profile_id"}];function u(){return u=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e},u.apply(this,arguments)}function p(e,t){if(null==e)return{};var n,r,s=function(e,t){if(null==e)return{};var n,r,s={},a=Object.keys(e);for(r=0;r<a.length;r++)n=a[r],t.indexOf(n)>=0||(s[n]=e[n]);return s}(e,t);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);for(r=0;r<a.length;r++)n=a[r],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(s[n]=e[n])}return s}var d=n(294),m=["children","custom_value","help","label","onChange","options","className","hideLabelFromVision","selectedValue"],f=(wp.i18n._x,wp.components),g=f.BaseControl,v=(f.TextControl,lodash.isEmpty),y=wp.compose.useInstanceId;var h={},b=function(e,t){var n=h[e]||[],r=[];t&&[].forEach.call(n,(function(e){t!==e.fn&&t!==e.fn.once&&r.push(e)})),r.length?h[e]=r:delete h[e]},w=function(e,t,n){(h[e]||(h[e]=[])).push({fn:t,context:n})},x={events:h,off:b,on:w,once:function(e,t,n){var r=function r(){b(e,r),t.apply(n,arguments)};r.once=t,w(e,r,n)},trigger:function(e){var t=[].slice.call(arguments,1),n=(h[e]||[]).slice();[].forEach.call(n,(function(e){return e.fn.apply(e.context,t)}))}},E=function(e,t,n){GLSR.Event.trigger(t,e,n)};function k(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=new Array(t);n<t;n++)r[n]=e[n];return r}function O(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){var n=null==e?null:"undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null!=n){var r,s,a=[],i=!0,o=!1;try{for(n=n.call(e);!(i=(r=n.next()).done)&&(a.push(r.value),!t||a.length!==t);i=!0);}catch(e){o=!0,s=e}finally{try{i||null==n.return||n.return()}finally{if(o)throw s}}return a}}(e,t)||function(e,t){if(e){if("string"==typeof e)return k(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);return"Object"===n&&e.constructor&&(n=e.constructor.name),"Map"===n||"Set"===n?Array.from(e):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?k(e,t):void 0}}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function C(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var S=wp.element,j=S.useRef,R=S.useState,P=S.useEffect;function A(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function L(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?A(Object(n),!0).forEach((function(t){C(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):A(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}var I=lodash,T=I.debounce,N=I.isEqual,D=I.reduce,G=wp.compose.usePrevious,F=wp.element,M=F.RawHTML,B=F.useEffect,U=F.useRef,$=F.useState,z=wp.i18n,V=z.__,q=z.sprintf,Q=wp.apiFetch,W=wp.url.addQueryArgs,Y=wp.components,H=Y.Placeholder,J=Y.Spinner,X=wp.blocks.getBlockType;function K(e,t,n){var r,s,a,i,o,l,c=(r=function(){return T(e,t,n)},s=[e,t,n],a=R((function(){return{inputs:s,result:r()}}))[0],i=j(!0),o=j(a),l=i.current||Boolean(s&&o.current.inputs&&function(e,t){if(e.length!==t.length)return!1;for(var n=0;n<e.length;n++)if(e[n]!==t[n])return!1;return!0}(s,o.current.inputs))?o.current:{inputs:s,result:r()},P((function(){i.current=!1,o.current=l}),[l]),l.result);return B((function(){return function(){return c.cancel()}}),[c]),c}function Z(e){var t=e.className;return(0,d.createElement)(H,{className:t},V("Block rendered as empty."))}function ee(e){var t=e.response,n=e.className,r=q(V("Error loading block: %s"),t.errorMsg);return(0,d.createElement)(H,{className:n},r)}function te(e){var t=e.className;return(0,d.createElement)(H,{className:t},(0,d.createElement)(J,null))}var ne=wp.i18n._x,re=[{label:"- "+ne("Select","admin-text","site-reviews")+" -",value:""},{label:ne("Terms were accepted","admin-text","site-reviews"),value:"true"},{label:ne("Terms were not accepted","admin-text","site-reviews"),value:"false"}],se={label:"- "+(0,wp.i18n._x)("Select","admin-text","site-reviews")+" -",value:""},ae=[];wp.apiFetch({path:"/site-reviews/v1/types?per_page=50"}).then((function(e){e.length<2||(ae.push(se),jQuery.each(e,(function(e,t){ae.push({label:t.name,value:t.id})})))}));var ie=ae;function oe(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}var le=function(e,t){var n=function(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?oe(Object(n),!0).forEach((function(t){C(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):oe(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}({},e.raw);return n.hide&&(n.hide=n.hide.join()),n.rating&&(n.rating=Number(n.rating)),~["","post_id","parent_id"].indexOf(n.assigned_posts)?t.assign_to?n.assign_to=n.assigned_posts:n.assigned_to=n.assigned_posts:t.assign_to?n.assign_to="custom":n.assigned_to="custom",n.user=n.assigned_users,~_.findIndex(c,(function(e){return e.value==n.assigned_users}))||(n.user="custom"),n.category=n.assigned_terms,~_.findIndex(o,(function(e){return e.value==n.assigned_terms}))||(n.category="custom"),n},ce=wp.components.CheckboxControl,ue=wp.element.useState;window.hasOwnProperty("GLSR")||(window.GLSR={Event:x}),GLSR.blocks={AssignedPostsOptions:t,AssignedTermsOptions:o,AssignedUsersOptions:c,CheckboxControlList:function(e,t,n){var r=[];return jQuery.each(e,(function(e,s){var a=O(ue(!1),2),i=a[0],o=a[1],l=t.split(",").indexOf(e)>-1;r.push((0,d.createElement)(ce,{key:"hide-".concat(e),className:"glsr-checkbox-control",checked:l||i,label:s,onChange:function(r){o(r),t=_.without(_.without(t.split(","),""),e),r&&t.push(e),n({hide:t.toString()})}}))})),r},ConditionalSelectControl:function e(t){var n=t.children,r=t.custom_value,s=void 0===r?"custom":r,a=t.help,i=t.label,o=t.onChange,l=t.options,c=void 0===l?[]:l,f=t.className,h=t.hideLabelFromVision,b=(t.selectedValue,p(t,m)),w=y(e),_="inspector-select-control-".concat(w),x=b.value;return!v(c)&&(0,d.createElement)(g,{label:i,hideLabelFromVision:h,id:_,help:a,className:f},(0,d.createElement)("select",u({id:_,className:"components-select-control__input",onChange:function(e){o(e.target.value)},"aria-describedby":a?"".concat(_,"__help"):void 0},b),c.map((function(e,t){return(0,d.createElement)("option",{key:"".concat(e.label,"-").concat(e.value,"-").concat(t),value:e.value,disabled:e.disabled},e.label)}))),s===x&&n)},ServerSideRender:function(e){var t=e.attributes,n=e.block,r=e.className,s=e.httpMethod,a=void 0===s?"GET":s,i=e.urlQueryArgs,o=e.EmptyResponsePlaceholder,l=void 0===o?Z:o,c=e.ErrorResponsePlaceholder,p=void 0===c?ee:c,m=e.LoadingResponsePlaceholder,f=void 0===m?te:m,g=U(!0),v=U(),y=O($(null),2),h=y[0],b=y[1],w=G(e);function _(){var e;if(g.current){null!==h&&b(null);var r=null!==(e=t&&function(e,t){var n=X(e);if(void 0===n)throw new Error("Block type '".concat(e,"' is not registered."));return D(n.attributes,(function(e,n,r){var s=t[r];return void 0!==s?e[r]=s:n.hasOwnProperty("default")&&(e[r]=n.default),-1!==["node","children"].indexOf(n.source)&&("string"==typeof e[r]?e[r]=[e[r]]:Array.isArray(e[r])||(e[r]=[])),e}),{})}(n,t))&&void 0!==e?e:null,s="POST"===a,o=function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null,n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{};return W("/wp/v2/block-renderer/".concat(e),L(L({context:"edit"},null!==t?{attributes:t}:{}),n))}(n,s?null:r,i),l=s?{attributes:r}:null,c=v.current=Q({path:o,data:l,method:s?"POST":"GET"}).then((function(e){g.current&&c===v.current&&e&&b(e.rendered)})).catch((function(e){g.current&&c===v.current&&b({error:!0,errorMsg:e.message})}));return c}}var x=K(_,500);return B((function(){return function(){g.current=!1}}),[]),B((function(){void 0===w?_():N(w,e)||x()})),B((function(){e.onRender&&e.onRender(h,n,t)}),[h]),""===h?(0,d.createElement)(l,e):h?h.error?(0,d.createElement)(p,u({response:h},e)):(0,d.createElement)(M,{className:r},h):(0,d.createElement)(f,e)},onRender:E,TermOptions:re,TypeOptions:ie,transformWidgetAttributes:le};var pe=wp.i18n._x,de=wp.blocks,me=de.createBlock,fe=de.registerBlockType,ge=wp.blockEditor,ve=ge.InspectorAdvancedControls,ye=ge.InspectorControls,he=wp.components,be=he.Icon,we=he.PanelBody,_e=(he.SelectControl,he.TextControl),xe=GLSR.blocks,Ee=xe.AssignedPostsOptions,ke=xe.AssignedTermsOptions,Oe=xe.AssignedUsersOptions,Ce=xe.CheckboxControlList,Se=xe.ConditionalSelectControl,je=xe.ServerSideRender,Re=xe.onRender,Pe=xe.transformWidgetAttributes,Ae=GLSR.nameprefix+"/form",Le={assign_to:{default:"",type:"string"},assigned_posts:{default:"",type:"string"},assigned_terms:{default:"",type:"string"},assigned_users:{default:"",type:"string"},category:{default:"",type:"string"},className:{default:"",type:"string"},hide:{default:"",type:"string"},id:{default:"",type:"string"},reviews_id:{default:"",type:"string"},user:{default:"",type:"string"}},Ie=(fe(Ae,{attributes:Le,category:GLSR.nameprefix,description:pe("Display a review form.","admin-text","site-reviews"),edit:function(e){var t=e.attributes,n=t.assign_to,r=t.assigned_posts,s=t.assigned_terms,a=t.assigned_users,i=t.category,o=t.hide,l=t.id,c=t.reviews_id,u=t.user,p=(e.className,e.setAttributes),m={assign_to:(0,d.createElement)(Se,{key:"assigned_posts",label:pe("Assign Reviews to a Page","admin-text","site-reviews"),onChange:function(e){return p({assign_to:e,assigned_posts:"custom"===e?r:""})},options:Ee,value:n},(0,d.createElement)(_e,{key:"custom_assigned_posts",className:"glsr-base-conditional-control",help:pe("Separate values with a comma.","admin-text","site-reviews"),onChange:function(e){return p({assigned_posts:e})},placeholder:pe("Enter the Post IDs","admin-text","site-reviews"),type:"text",value:r})),category:(0,d.createElement)(Se,{key:"assigned_terms",label:pe("Assign Reviews to a Category","admin-text","site-reviews"),onChange:function(e){return p({category:e,assigned_terms:"custom"===e?s:""})},options:ke,value:i},(0,d.createElement)(_e,{key:"custom_assigned_terms",className:"glsr-base-conditional-control",help:pe("Separate values with a comma.","admin-text","site-reviews"),onChange:function(e){return p({assigned_terms:e})},placeholder:pe("Enter the Category IDs or slugs","admin-text","site-reviews"),type:"text",value:s})),user:(0,d.createElement)(Se,{key:"assigned_users",label:pe("Assign Reviews to a User","admin-text","site-reviews"),onChange:function(e){return p({user:e,assigned_users:"custom"===e?a:""})},options:Oe,value:u},(0,d.createElement)(_e,{key:"custom_assigned_users",className:"glsr-base-conditional-control",help:pe("Separate values with a comma.","admin-text","site-reviews"),onChange:function(e){return p({assigned_users:e})},placeholder:pe("Enter the User IDs or usernames","admin-text","site-reviews"),type:"text",value:a})),hide:Ce(GLSR.hideoptions.site_reviews_form,o,p)},f={panel_settings:(0,d.createElement)(we,{title:pe("Settings","admin-text","site-reviews")},Object.values(wp.hooks.applyFilters(GLSR.nameprefix+".form.InspectorControls",m,e)))},g={id:(0,d.createElement)(_e,{label:pe("Custom ID","admin-text","site-reviews"),onChange:function(e){return p({id:e})},value:l}),reviews_id:(0,d.createElement)(_e,{help:pe("Enter the Custom ID of a reviews block or shortcode to display the review after submission.","admin-text","site-reviews"),label:pe("Custom Reviews ID","admin-text","site-reviews"),onChange:function(e){return p({reviews_id:e})},value:c})};return[(0,d.createElement)(ye,null,Object.values(wp.hooks.applyFilters(GLSR.nameprefix+".form.InspectorPanels",f,e))),(0,d.createElement)(ve,null,Object.values(wp.hooks.applyFilters(GLSR.nameprefix+".form.InspectorAdvancedControls",g,e))),(0,d.createElement)(je,{block:Ae,attributes:e.attributes,onRender:Re})]},example:{},icon:function(){return(0,d.createElement)(be,{icon:(0,d.createElement)("svg",null,(0,d.createElement)("path",{d:"M12 2a.36.36 0 0 1 .321.199l2.968 6.01a.36.36 0 0 0 .268.196l6.634.963a.36.36 0 0 1 .199.612l-4.8 4.676a.36.36 0 0 0-.103.318l1.133 6.605a.36.36 0 0 1-.521.378l-5.933-3.12a.36.36 0 0 0-.334 0l-5.934 3.118a.36.36 0 0 1-.519-.377l1.133-6.605a.36.36 0 0 0-.103-.318L1.609 9.981a.36.36 0 0 1 .201-.612l6.632-.963a.36.36 0 0 0 .27-.196l2.967-6.01A.36.36 0 0 1 12 2zm0 2.95v12.505c.492 0 .982.117 1.43.35l3.328 1.745-.636-3.694c-.171-.995.16-2.009.885-2.713l2.693-2.617-3.724-.539c-1.001-.145-1.866-.772-2.313-1.675L12 4.95zM21 1v.963h-3.479v1.683h3.272v.963h-3.272V7.3h-1.017V1H21z"}))})},keywords:["reviews","form"],save:function(){return null},title:pe("Review Form","admin-text","site-reviews"),transforms:{from:[{type:"block",blocks:["core/legacy-widget"],isMatch:function(e){var t=e.idBase,n=e.instance;return"glsr_site-reviews-form"===t&&!(null==n||!n.raw)},transform:function(e){var t=e.instance;return me(Ae,Pe(t,Le))}}]}}),wp.i18n._x),Te=wp.blocks,Ne=Te.createBlock,De=Te.registerBlockType,Ge=wp.blockEditor,Fe=Ge.InspectorAdvancedControls,Me=Ge.InspectorControls,Be=wp.components,Ue=Be.Icon,$e=Be.PanelBody,ze=Be.RangeControl,Ve=Be.SelectControl,qe=Be.TextControl,Qe=Be.ToggleControl,We=GLSR.blocks,Ye=We.AssignedPostsOptions,He=We.AssignedTermsOptions,Je=We.AssignedUsersOptions,Xe=We.CheckboxControlList,Ke=We.ConditionalSelectControl,Ze=We.ServerSideRender,et=We.onRender,tt=We.TermOptions,nt=We.TypeOptions,rt=We.transformWidgetAttributes,st=GLSR.nameprefix+"/reviews",at={assigned_to:{default:"",type:"string"},assigned_posts:{default:"",type:"string"},assigned_terms:{default:"",type:"string"},assigned_users:{default:"",type:"string"},category:{default:"",type:"string"},className:{default:"",type:"string"},display:{default:5,type:"number"},hide:{default:"",type:"string"},id:{default:"",type:"string"},pagination:{default:"",type:"string"},post_id:{default:"",type:"string"},rating:{default:0,type:"number"},schema:{default:!1,type:"boolean"},terms:{default:"",type:"string"},type:{default:"local",type:"string"},user:{default:"",type:"string"}};wp.hooks.addFilter("blocks.getBlockAttributes",st,(function(e,t,n,r){return r&&r.count&&(e.display=r.count),e}));De(st,{attributes:at,category:GLSR.nameprefix,description:Ie("Display your most recent reviews.","admin-text","site-reviews"),edit:function(e){e.attributes.post_id=jQuery("#post_ID").val();var t=e.attributes,n=t.assigned_to,r=t.assigned_posts,s=t.assigned_terms,a=t.assigned_users,i=t.category,o=t.display,l=t.hide,c=t.id,u=t.pagination,p=t.rating,m=t.schema,f=t.terms,g=t.type,v=t.user,y=(e.className,e.setAttributes),h={assigned_to:(0,d.createElement)(Ke,{key:"assigned_posts",label:Ie("Limit Reviews to an Assigned Page","admin-text","site-reviews"),onChange:function(e){return y({assigned_to:e,assigned_posts:"custom"===e?r:""})},options:Ye,value:n},(0,d.createElement)(qe,{key:"custom_assigned_posts",className:"glsr-base-conditional-control",help:Ie("Separate values with a comma.","admin-text","site-reviews"),onChange:function(e){return y({assigned_posts:e})},placeholder:Ie("Enter the Post IDs","admin-text","site-reviews"),type:"text",value:r})),category:(0,d.createElement)(Ke,{key:"assigned_terms",label:Ie("Limit Reviews to an Assigned Category","admin-text","site-reviews"),onChange:function(e){return y({category:e,assigned_terms:"custom"===e?s:""})},options:He,value:i},(0,d.createElement)(qe,{key:"custom_assigned_terms",className:"glsr-base-conditional-control",help:Ie("Separate values with a comma.","admin-text","site-reviews"),onChange:function(e){return y({assigned_terms:e})},placeholder:Ie("Enter the Category IDs or slugs","admin-text","site-reviews"),type:"text",value:s})),user:(0,d.createElement)(Ke,{key:"assigned_users",label:Ie("Limit Reviews to an Assigned User","admin-text","site-reviews"),onChange:function(e){return y({user:e,assigned_users:"custom"===e?a:""})},options:Je,value:v},(0,d.createElement)(qe,{key:"custom_assigned_users",className:"glsr-base-conditional-control",help:Ie("Separate values with a comma.","admin-text","site-reviews"),onChange:function(e){return y({assigned_users:e})},placeholder:Ie("Enter the User IDs or usernames","admin-text","site-reviews"),type:"text",value:a})),terms:(0,d.createElement)(Ve,{key:"terms",label:Ie("Limit Reviews to terms","admin-text","site-reviews"),onChange:function(e){return y({terms:e})},options:tt,value:f}),pagination:(0,d.createElement)(Ve,{key:"pagination",label:Ie("Enable Pagination","admin-text","site-reviews"),onChange:function(e){return y({pagination:e})},options:[{label:"- "+Ie("Select","admin-text","site-reviews")+" -",value:""},{label:Ie("Yes (AJAX load more)","admin-text","site-reviews"),value:"loadmore"},{label:Ie("Yes (AJAX pagination)","admin-text","site-reviews"),value:"ajax"},{label:Ie("Yes (page reload)","admin-text","site-reviews"),value:"true"}],value:u}),type:(0,d.createElement)(Ve,{key:"type",label:Ie("Limit the Type of Reviews","admin-text","site-reviews"),onChange:function(e){return y({type:e})},options:nt,value:g}),display:(0,d.createElement)(ze,{key:"display",label:Ie("Reviews Per Page","admin-text","site-reviews"),min:1,max:50,onChange:function(e){return y({display:e})},value:o}),rating:(0,d.createElement)(ze,{key:"rating",label:Ie("Minimum Rating","admin-text","site-reviews"),min:0,max:GLSR.maxrating,onChange:function(e){return y({rating:e})},value:p}),schema:(0,d.createElement)(Qe,{key:"schema",checked:m,help:Ie("The schema should only be enabled once per page.","admin-text","site-reviews"),label:Ie("Enable the schema?","admin-text","site-reviews"),onChange:function(e){return y({schema:e})}}),hide:Xe(GLSR.hideoptions.site_reviews,l,y)},b={panel_settings:(0,d.createElement)($e,{title:Ie("Settings","admin-text","site-reviews")},Object.values(wp.hooks.applyFilters(GLSR.nameprefix+".reviews.InspectorControls",h,e)))},w={id:(0,d.createElement)(qe,{label:Ie("Custom ID","admin-text","site-reviews"),onChange:function(e){return y({id:e})},value:c})};return[(0,d.createElement)(Me,null,Object.values(wp.hooks.applyFilters(GLSR.nameprefix+".reviews.InspectorPanels",b,e))),(0,d.createElement)(Fe,null,Object.values(wp.hooks.applyFilters(GLSR.nameprefix+".reviews.InspectorAdvancedControls",w,e))),(0,d.createElement)(Ze,{block:st,attributes:e.attributes,onRender:et})]},example:{attributes:{display:2,pagination:"ajax",rating:0}},icon:function(){return(0,d.createElement)(Ue,{icon:(0,d.createElement)("svg",null,(0,d.createElement)("path",{d:"M12 2a.36.36 0 0 1 .321.199l2.968 6.01a.36.36 0 0 0 .268.196l6.634.963a.36.36 0 0 1 .199.612l-4.8 4.676a.36.36 0 0 0-.103.318l1.133 6.605a.36.36 0 0 1-.521.378l-5.933-3.12a.36.36 0 0 0-.334 0l-5.934 3.118a.36.36 0 0 1-.519-.377l1.133-6.605a.36.36 0 0 0-.103-.318L1.609 9.981a.36.36 0 0 1 .201-.612l6.632-.963a.36.36 0 0 0 .27-.196l2.967-6.01A.36.36 0 0 1 12 2zm0 2.95v12.505c.492 0 .982.117 1.43.35l3.328 1.745-.636-3.694c-.171-.995.16-2.009.885-2.713l2.693-2.617-3.724-.539c-1.001-.145-1.866-.772-2.313-1.675L12 4.95zM18.768 1C20.217 1 21 1.648 21 2.823c0 1.071-.819 1.782-2.102 1.827L20.973 7.3h-1.26L17.706 4.65h-.513V7.3h-1.017V1h2.592zm-.027.954h-1.548v1.773h1.548c.819 0 1.202-.297 1.202-.905 0-.599-.405-.869-1.202-.869z"}))})},keywords:["reviews"],save:function(){return null},title:Ie("Latest Reviews","admin-text","site-reviews"),transforms:{from:[{type:"block",blocks:["core/legacy-widget"],isMatch:function(e){var t=e.idBase,n=e.instance;return"glsr_site-reviews"===t&&!(null==n||!n.raw)},transform:function(e){var t=e.instance;return Ne(st,rt(t,at))}}]}});var it=wp.i18n._x,ot=wp.blocks,lt=ot.createBlock,ct=ot.registerBlockType,ut=wp.blockEditor,pt=ut.InspectorAdvancedControls,dt=ut.InspectorControls,mt=wp.components,ft=mt.Icon,gt=mt.PanelBody,vt=mt.RangeControl,yt=mt.SelectControl,ht=mt.TextControl,bt=mt.ToggleControl,wt=GLSR.blocks,_t=wt.AssignedPostsOptions,xt=wt.AssignedTermsOptions,Et=wt.AssignedUsersOptions,kt=wt.CheckboxControlList,Ot=wt.ConditionalSelectControl,Ct=wt.ServerSideRender,St=wt.onRender,jt=wt.TermOptions,Rt=wt.TypeOptions,Pt=wt.transformWidgetAttributes,At=GLSR.nameprefix+"/summary",Lt={assigned_to:{default:"",type:"string"},assigned_posts:{default:"",type:"string"},assigned_terms:{default:"",type:"string"},assigned_users:{default:"",type:"string"},category:{default:"",type:"string"},className:{default:"",type:"string"},hide:{default:"",type:"string"},post_id:{default:"",type:"string"},rating:{default:0,type:"number"},rating_field:{default:"",type:"string"},schema:{default:!1,type:"boolean"},terms:{default:"",type:"string"},type:{default:"local",type:"string"},user:{default:"",type:"string"}};ct(At,{attributes:Lt,category:GLSR.nameprefix,description:it("Display a summary of your reviews.","admin-text","site-reviews"),edit:function(e){e.attributes.post_id=jQuery("#post_ID").val();var t=e.attributes,n=t.assigned_to,r=t.assigned_posts,s=t.assigned_terms,a=t.assigned_users,i=t.category,o=(t.display,t.hide),l=t.id,c=(t.pagination,t.rating),u=t.rating_field,p=t.schema,m=t.terms,f=t.type,g=t.user,v=(e.className,e.setAttributes),y={assigned_to:(0,d.createElement)(Ot,{key:"assigned_posts",label:it("Limit Reviews to an Assigned Page","admin-text","site-reviews"),onChange:function(e){return v({assigned_to:e,assigned_posts:"custom"===e?r:""})},options:_t,value:n},(0,d.createElement)(ht,{key:"custom_assigned_posts",className:"glsr-base-conditional-control",help:it("Separate values with a comma.","admin-text","site-reviews"),onChange:function(e){return v({assigned_posts:e})},placeholder:it("Enter the Post IDs","admin-text","site-reviews"),type:"text",value:r})),category:(0,d.createElement)(Ot,{key:"assigned_terms",label:it("Limit Reviews to an Assigned Category","admin-text","site-reviews"),onChange:function(e){return v({category:e,assigned_terms:"custom"===e?s:""})},options:xt,value:i},(0,d.createElement)(ht,{key:"custom_assigned_terms",className:"glsr-base-conditional-control",help:it("Separate values with a comma.","admin-text","site-reviews"),onChange:function(e){return v({assigned_terms:e})},placeholder:it("Enter the Category IDs or slugs","admin-text","site-reviews"),type:"text",value:s})),user:(0,d.createElement)(Ot,{key:"assigned_users",label:it("Limit Reviews to an Assigned User","admin-text","site-reviews"),onChange:function(e){return v({user:e,assigned_users:"custom"===e?a:""})},options:Et,value:g},(0,d.createElement)(ht,{key:"custom_assigned_users",className:"glsr-base-conditional-control",help:it("Separate values with a comma.","admin-text","site-reviews"),onChange:function(e){return v({assigned_users:e})},placeholder:it("Enter the User IDs or usernames","admin-text","site-reviews"),type:"text",value:a})),terms:(0,d.createElement)(yt,{key:"terms",label:it("Limit Reviews to terms","admin-text","site-reviews"),onChange:function(e){return v({terms:e})},options:jt,value:m}),type:(0,d.createElement)(yt,{key:"type",label:it("Limit the Type of Reviews","admin-text","site-reviews"),onChange:function(e){return v({type:e})},options:Rt,value:f}),rating:(0,d.createElement)(vt,{key:"rating",label:it("Minimum Rating","admin-text","site-reviews"),min:0,max:GLSR.maxrating,onChange:function(e){return v({rating:e})},value:c}),schema:(0,d.createElement)(bt,{key:"schema",checked:p,help:it("The schema should only be enabled once per page.","admin-text","site-reviews"),label:it("Enable the schema?","admin-text","site-reviews"),onChange:function(e){return v({schema:e})}}),hide:kt(GLSR.hideoptions.site_reviews_summary,o,v)},h={panel_settings:(0,d.createElement)(gt,{title:it("Settings","admin-text","site-reviews")},Object.values(wp.hooks.applyFilters(GLSR.nameprefix+".summary.InspectorControls",y,e)))},b={id:(0,d.createElement)(ht,{label:it("Custom ID","admin-text","site-reviews"),onChange:function(e){return v({id:e})},value:l}),rating_field:(0,d.createElement)(ht,{help:it("Use the Review Forms add-on to add custom rating fields.","admin-text","site-reviews"),label:it("Custom Rating Field Name","admin-text","site-reviews"),onChange:function(e){return v({rating_field:e})},value:u})};return[(0,d.createElement)(dt,null,Object.values(wp.hooks.applyFilters(GLSR.nameprefix+".summary.InspectorPanels",h,e))),(0,d.createElement)(pt,null,Object.values(wp.hooks.applyFilters(GLSR.nameprefix+".summary.InspectorAdvancedControls",b,e))),(0,d.createElement)(Ct,{block:At,attributes:e.attributes,onRender:St})]},example:{},icon:function(){return(0,d.createElement)(ft,{icon:(0,d.createElement)("svg",null,(0,d.createElement)("path",{d:"M12 2a.36.36 0 0 1 .321.199l2.968 6.01a.36.36 0 0 0 .268.196l6.634.963a.36.36 0 0 1 .199.612l-4.8 4.676a.36.36 0 0 0-.103.318l1.133 6.605a.36.36 0 0 1-.521.378l-5.933-3.12a.36.36 0 0 0-.334 0l-5.934 3.118a.36.36 0 0 1-.519-.377l1.133-6.605a.36.36 0 0 0-.103-.318L1.609 9.981a.36.36 0 0 1 .201-.612l6.632-.963a.36.36 0 0 0 .27-.196l2.967-6.01A.36.36 0 0 1 12 2zm0 2.95v12.505c.492 0 .982.117 1.43.35l3.328 1.745-.636-3.694c-.171-.995.16-2.009.885-2.713l2.693-2.617-3.724-.539c-1.001-.145-1.866-.772-2.313-1.675L12 4.95zM18.651 1a3.95 3.95 0 0 1 2.277.68l-.518.824c-.536-.342-1.13-.54-1.769-.54-.842 0-1.418.365-1.418.941 0 .522.491.725 1.31.842l.437.059c1.022.14 2.03.563 2.03 1.733 0 1.283-1.161 1.985-2.525 1.985-.855 0-1.881-.284-2.534-.846l.554-.81c.432.396 1.247.693 1.976.693.824 0 1.472-.351 1.472-.932 0-.495-.495-.725-1.418-.851l-.491-.068c-.936-.131-1.868-.572-1.868-1.742C16.167 1.702 17.287 1 18.651 1z"}))})},keywords:["reviews","summary"],save:function(){return null},title:it("Rating Summary","admin-text","site-reviews"),transforms:{from:[{type:"block",blocks:["core/legacy-widget"],isMatch:function(e){var t=e.idBase,n=e.instance;return"glsr_site-reviews-summary"===t&&!(null==n||!n.raw)},transform:function(e){var t=e.instance;return lt(At,Pt(t,Lt))}}]}})}()}();