/*! For license information please see site-reviews.js.LICENSE.txt */
!function(){"use strict";var t,e={533:function(){var t,e=function(e,i,n){o(i),t.open("GET",e,!0),t.responseType="text",a(n),t.send()},i=function(e,i,n){o(i),t.open("POST",GLSR.ajaxurl,!0),t.responseType="json",t.json=!0,a(n),t.send(r(e))},n=function(t){return"json"===this.responseType||!0===this.json?t({message:this.statusText},!1):"text"===this.responseType?t(this.statusText):void console.error(this)},s=function(t){if(0===this.status||this.status>=200&&this.status<300||304===this.status){if("json"===this.responseType)return t(this.response.data,this.response.success);if("text"===this.responseType)return t(this.responseText);if(!0===this.json){var e=JSON.parse(this.response);return t(e.data,e.success)}console.info(this)}else n.bind(this,t)},r=function(t){var e=new FormData,i=Object.prototype.toString.call(t);return"[object FormData]"===i&&(e=t),"[object HTMLFormElement]"===i&&(e=new FormData(t)),"[object Object]"===i&&Object.keys(t).forEach((function(i){return e.append(i,t[i])})),e.append("action",GLSR.action),e.append("_ajax_request",!0),e},o=function(e){(t=new XMLHttpRequest).addEventListener("load",s.bind(t,e)),t.addEventListener("error",n.bind(t,e))},a=function(e){for(var i in(e=e||{})["X-Requested-With"]="XMLHttpRequest",e)e.hasOwnProperty(i)&&t.setRequestHeader(i,e[i])},l={get:e,post:i},c={},u=function(t,e){var i=c[t]||[],n=[];e&&[].forEach.call(i,(function(t){e!==t.fn&&e!==t.fn.once&&n.push(t)})),n.length?c[t]=n:delete c[t]},d=function(t,e,i){(c[t]||(c[t]=[])).push({fn:e,context:i})},h=function(t,e,i){var n=arguments,s=function s(){u(t,s),e.apply(i,n)};s.once=e,d(t,s,i)},f=function(t){var e=[].slice.call(arguments,1),i=(c[t]||[]).slice();[].forEach.call(i,(function(t){return t.fn.apply(t.context,e)}))},v={events:c,off:u,on:d,once:h,trigger:f};function p(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function g(t,e){for(var i=0;i<e.length;i++){var n=e[i];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}function m(t,e,i){return e&&g(t.prototype,e),i&&g(t,i),Object.defineProperty(t,"prototype",{writable:!1}),t}var y={hidden:"glsr-hidden",readmore:"glsr-read-more",visible:"glsr-visible"},b={hiddenText:".glsr-hidden-text"},w=function(){function t(e){var i=this;p(this,t),this.events={click:this._onClick},(e||document).querySelectorAll(b.hiddenText).forEach((function(t){return i.init(t)}))}return m(t,[{key:"init",value:function(t){var e=this._insertLink(t);e&&("expand"===t.dataset.trigger&&(e.dataset.text=t.dataset.showLess,e.removeEventListener("click",this.events.click),e.addEventListener("click",this.events.click)),"modal"===t.dataset.trigger&&(e.dataset.excerptTrigger="glsr-modal"))}},{key:"_insertLink",value:function(t){var e=t.parentNode.querySelector("."+y.readmore);if(!e){var i=document.createElement("span"),n=document.createElement("a");n.setAttribute("href","#"),n.innerHTML=t.dataset.showMore,i.setAttribute("class",y.readmore),i.appendChild(n),e=t.parentNode.insertBefore(i,t.nextSibling)}return e.querySelector("a")}},{key:"_onClick",value:function(t){t.preventDefault();var e=t.currentTarget,i=e.parentNode.previousSibling,n=e.dataset.text;i.classList.toggle(y.hidden),i.classList.toggle(y.visible),e.dataset.text=e.innerText,e.innerText=n}}]),t}(),E=w,_=function(){function t(e){var i=this;p(this,t),this.Form=e,this.counter=0,this.id=-1,this.is_submitting=!1,this.parentEl=this.Form.form.querySelector(".glsr-recaptcha-holder"),this.recaptchaEl=this._buildContainer(),this.observer=new MutationObserver((function(t){var e=t.pop();e.target&&"visible"!==e.target.style.visibility&&(i.observer.disconnect(),setTimeout((function(){i.is_submitting||i.Form.enableButton()}),250))}))}return m(t,[{key:"execute",value:function(){var t=this;if(-1!==this.id)return this.counter=0,this._observeMutations(this.id),void grecaptcha.execute(this.id);setTimeout((function(){t.counter++,t._submitForm.call(t.Form,t.counter)}),1e3)}},{key:"render",value:function(){this.Form.form.onsubmit=null,this.reset(),this._renderWait()}},{key:"reset",value:function(){-1!==this.id&&grecaptcha.reset(this.id),this.counter=0,this.is_submitting=!1}},{key:"_buildContainer",value:function(){if(!this.parentEl)return!1;Array.from(this.parentEl.getElementsByClassName("g-recaptcha")).forEach((function(t){return t.remove()}));var t=document.createElement("div");return t.classList.add("g-recaptcha"),this.parentEl.appendChild(t),t}},{key:"_observeMutations",value:function(t){var e=window.___grecaptcha_cfg.clients[t];for(var i in e)if(e.hasOwnProperty(i)&&"[object String]"===Object.prototype.toString.call(e[i])){var n=document.querySelector("iframe[name=c-"+e[i]+"]");if(n){this.observer.observe(n.parentElement.parentElement,{attributeFilter:["style"],attributes:!0});break}}}},{key:"_renderWait",value:function(){var t=this;this.recaptchaEl&&setTimeout((function(){if(-1===t.id)return"undefined"==typeof grecaptcha||void 0===grecaptcha.render?t._renderWait():void t._renderChallenge()}),250)}},{key:"_renderChallenge",value:function(){try{this.id=grecaptcha.render(this.recaptchaEl,{badge:this.parentEl.dataset.badge,callback:this._submitForm.bind(this.Form,this.counter),"expired-callback":this.reset.bind(this),isolated:!0,sitekey:this.parentEl.dataset.sitekey,size:this.parentEl.dataset.size},!0)}catch(t){console.error(t)}}},{key:"_submitForm",value:function(t){if(this.recaptcha.is_submitting=!0,!this.useAjax)return this.disableButton(),void this.form.submit();this.submitForm(t)}}]),t}(),L=_;const k={classNames:{active:"gl-active",base:"gl-star-rating",selected:"gl-selected"},clearable:!0,maxStars:10,prebuilt:!1,stars:null,tooltip:"Select a Rating"},S=(t,e,i)=>{t.classList[e?"add":"remove"](i)},x=t=>{const e=document.createElement("span");t=t||{};for(let i in t)e.setAttribute(i,t[i]);return e},O=(t,e,i)=>/^\d+$/.test(t)&&e<=t&&t<=i,T=(t,e,i)=>{const n=x(i);return t.parentNode.insertBefore(n,e?t.nextSibling:t),n},R=(...t)=>{const e={};return t.forEach((i=>{Object.keys(i||{}).forEach((n=>{if(void 0===t[0][n])return;const s=i[n];"Object"!==A(s)||"Object"!==A(e[n])?e[n]=s:e[n]=R(e[n],s)}))})),e},A=t=>({}.toString.call(t).slice(8,-1)),F=t=>{const e=[];return[].forEach.call(t.options,(t=>{const i=parseInt(t.value,10)||0;i>0&&e.push({index:t.index,text:t.text,value:i})})),e.sort(((t,e)=>t.value-e.value))};var M="undefined"!=typeof window?window:{screen:{},navigator:{}},N=(M.matchMedia||function(){return{matches:!1}}).bind(M),G=!1,j={get passive(){return G=!0}},P=function(){};M.addEventListener&&M.addEventListener("p",P,j),M.removeEventListener&&M.removeEventListener("p",P,!1);var q=G,C="ontouchstart"in M,H="TouchEvent"in M,I=C||H&&N("(any-pointer: coarse)").matches,D=(M.navigator.maxTouchPoints,M.navigator.userAgent||""),B=N("(pointer: coarse)").matches&&/iPad|Macintosh/.test(D)&&Math.min(M.screen.width||0,M.screen.height||0)>=768;(N("(pointer: coarse)").matches||!N("(pointer: fine)").matches&&C)&&/Windows.*Firefox/.test(D),N("(any-pointer: fine)").matches||N("(any-hover: hover)").matches;class V{constructor(t,e){this.direction=window.getComputedStyle(t,null).getPropertyValue("direction"),this.el=t,this.events={change:this.onChange.bind(this),keydown:this.onKeyDown.bind(this),mousedown:this.onPointerDown.bind(this),mouseleave:this.onPointerLeave.bind(this),mousemove:this.onPointerMove.bind(this),reset:this.onReset.bind(this),touchend:this.onPointerDown.bind(this),touchmove:this.onPointerMove.bind(this)},this.indexActive=null,this.indexSelected=null,this.props=e,this.tick=null,this.ticking=!1,this.values=F(t),this.widgetEl=null,this.el.widget&&this.el.widget.destroy(),O(this.values.length,1,this.props.maxStars)?this.build():this.destroy()}build(){this.destroy(),this.buildWidget(),this.selectValue(this.indexSelected=this.selected(),!1),this.handleEvents("add"),this.el.widget=this}buildWidget(){let t,e;this.props.prebuilt?(t=this.el.parentNode,e=t.querySelector("."+this.props.classNames.base+"--stars")):(t=T(this.el,!1,{class:this.props.classNames.base}),t.appendChild(this.el),e=T(this.el,!0,{class:this.props.classNames.base+"--stars"}),this.values.forEach(((t,i)=>{const n=x({"data-index":i,"data-value":t.value});"function"==typeof this.props.stars&&this.props.stars.call(this,n,t,i),[].forEach.call(n.children,(t=>t.style.pointerEvents="none")),e.innerHTML+=n.outerHTML}))),t.dataset.starRating="",t.classList.add(this.props.classNames.base+"--"+this.direction),this.props.tooltip&&e.setAttribute("role","tooltip"),this.widgetEl=e}changeIndexTo(t,e){if(this.indexActive!==t||e){if([].forEach.call(this.widgetEl.children,((e,i)=>{S(e,i<=t,this.props.classNames.active),S(e,i===this.indexSelected,this.props.classNames.selected)})),this.widgetEl.setAttribute("data-rating",t+1),"function"==typeof this.props.stars||this.props.prebuilt||(this.widgetEl.classList.remove("s"+10*(this.indexActive+1)),this.widgetEl.classList.add("s"+10*(t+1))),this.props.tooltip){const e=t<0?this.props.tooltip:this.values[t]?.text;this.widgetEl.setAttribute("aria-label",e)}this.indexActive=t}this.ticking=!1}destroy(){this.indexActive=null,this.indexSelected=this.selected();const t=this.el.parentNode;t.classList.contains(this.props.classNames.base)&&(this.props.prebuilt?(this.widgetEl=t.querySelector("."+this.props.classNames.base+"--stars"),t.classList.remove(this.props.classNames.base+"--"+this.direction),delete t.dataset.starRating):t.parentNode.replaceChild(this.el,t),this.handleEvents("remove")),delete this.el.widget}eventListener(t,e,i,n){i.forEach((i=>t[e+"EventListener"](i,this.events[i],n||!1)))}handleEvents(t){const e=this.el.closest("form");e&&"FORM"===e.tagName&&this.eventListener(e,t,["reset"]),this.eventListener(this.el,t,["change"]),"add"===t&&this.el.disabled||(this.eventListener(this.el,t,["keydown"]),this.eventListener(this.widgetEl,t,["mousedown","mouseleave","mousemove","touchend","touchmove"],!!q&&{passive:!1}))}indexFromEvent(t){const e=t.touches?.[0]||t.changedTouches?.[0]||t,i=document.elementFromPoint(e.clientX,e.clientY);return[].slice.call(i.parentNode.children).indexOf(i)}onChange(){this.changeIndexTo(this.selected(),!0)}onKeyDown(t){const e=t.key.slice(5);if(!~["Left","Right"].indexOf(e))return;let i="Left"===e?-1:1;"rtl"===this.direction&&(i*=-1);const n=this.values.length-1,s=Math.min(Math.max(this.selected()+i,-1),n);this.selectValue(s,!0)}onPointerDown(t){t.preventDefault();let e=this.indexFromEvent(t);this.props.clearable&&e===this.indexSelected&&(e=-1),this.selectValue(e,!0)}onPointerLeave(t){t.preventDefault(),cancelAnimationFrame(this.tick),requestAnimationFrame((()=>this.changeIndexTo(this.indexSelected)))}onPointerMove(t){t.preventDefault(),this.ticking||(this.tick=requestAnimationFrame((()=>this.changeIndexTo(this.indexFromEvent(t)))),this.ticking=!0)}onReset(){const t=this.valueIndex(this.el.querySelector("[selected]")?.value);this.selectValue(t||-1,!1)}selected(){return this.valueIndex(this.el.value)}selectValue(t,e){this.el.value=this.values[t]?.value||"",this.indexSelected=this.selected(),!1===e?this.changeIndexTo(this.selected(),!0):this.el.dispatchEvent(new Event("change"))}valueIndex(t){return this.values.findIndex((e=>e.value===+t))}}class W{constructor(t,e){this.destroy=this.destroy.bind(this),this.props=e,this.rebuild=this.rebuild.bind(this),this.selector=t,this.widgets=[],this.build()}build(){this.queryElements(this.selector).forEach((t=>{const e=R(k,this.props,JSON.parse(t.getAttribute("data-options")));"SELECT"!==t.tagName||t.widget||(!e.prebuilt&&t.parentNode.classList.contains(e.classNames.base)&&this.unwrap(t),this.widgets.push(new V(t,e)))}))}destroy(){this.widgets.forEach((t=>t.destroy())),this.widgets=[]}queryElements(t){return"HTMLSelectElement"===A(t)?[t]:"NodeList"===A(t)?[].slice.call(t):"String"===A(t)?[].slice.call(document.querySelectorAll(t)):[]}rebuild(){this.destroy(),this.build()}unwrap(t){const e=t.parentNode,i=e.parentNode;i.insertBefore(t,e),i.removeChild(e)}}var Y=W,$=function(t,e,i){t&&e.split(" ").forEach((function(e){t.classList[i?"add":"remove"](e)}))},K=function(t){return"."+t.trim().split(" ").join(".")},J=function(t){var e='input[name="'+t.getAttribute("name")+'"]:checked';return t.validation.form.querySelectorAll(e).length},X={email:{fn:function(t){return!t||/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(t)}},max:{fn:function(t,e){return!t||("checkbox"===this.type?J(this)<=parseInt(e):parseFloat(t)<=parseFloat(e))}},maxlength:{fn:function(t,e){return!t||t.length<=parseInt(e)}},min:{fn:function(t,e){return!t||("checkbox"===this.type?J(this)>=parseInt(e):parseFloat(t)>=parseFloat(e))}},minlength:{fn:function(t,e){return!t||t.length>=parseInt(e)}},number:{fn:function(t){return!t||!isNaN(parseFloat(t))},priority:2},pattern:{fn:function(t,e){var i=e.match(new RegExp("^/(.*?)/([gimy]*)$"));return!t||new RegExp(i[1],i[2]).test(t)}},required:{fn:function(t){return"radio"===this.type||"checkbox"===this.type?J(this):void 0!==t&&""!==t},priority:99,halt:!0},tel:{fn:function(t){var e=t.replace(/[^0-9]/g,"").length;return!t||4<=e&&15>=e&&new RegExp("^[+]?[\\d\\s()-]*$").test(t)}},url:{fn:function(t){return!t||new RegExp("^(https?)://([\\p{L}\\p{N}\\p{S}\\-_.])+(.?([\\p{L}\\p{N}]|xn--[\\p{L}\\p{N}\\-]+)+.?)(:[0-9]+)?(?:/(?:[\\p{L}\\p{N}\\-._~!$&'()*+,;=:@]|%[0-9A-Fa-f]{2})*)*(?:\\?(?:[\\p{L}\\p{N}\\-._~!$&'\\[\\]()*+,;=:@/?]|%[0-9A-Fa-f]{2})*)?(?:#(?:[\\p{L}\\p{N}\\-._~!$&'()*+,;=:@/?]|%[0-9A-Fa-f]{2})*)?$","iu").test(t)}}},z=["required","max","maxlength","min","minlength","pattern"],Q="input:not([type^=hidden]):not([type^=submit]), select, textarea, [data-glsr-validate]",U=function(){function t(e){p(this,t),this.config=GLSR.validationconfig,this.fields=[],this.form=e,this.form.setAttribute("novalidate",""),this.strings=GLSR.validationstrings,this.validateEvent=this._onChange.bind(this)}return m(t,[{key:"destroy",value:function(){for(this.reset();this.fields.length;){var t=this.fields.shift();this._removeEvent(t.input),delete t.input.validation}}},{key:"init",value:function(){var t=this;this.form.querySelectorAll(Q).forEach((function(e){if(!t.fields.find((function(t){return t.input.name===e.name&&!e.name.endsWith("[]")}))){var i=e.closest(K(t.config.field));i&&"none"!==i.style.display&&t.fields.push(t._initField(e))}}))}},{key:"reset",value:function(){for(var t in this.fields)if(this.fields.hasOwnProperty(t)){this.fields[t].errorElements=null;var e=this.fields[t].input.closest(K(this.config.field));$(this.fields[t].input,this.config.input_error,!1),$(this.fields[t].input,this.config.input_valid,!1),$(e,this.config.field_error,!1),$(e,this.config.field_valid,!1)}}},{key:"setErrors",value:function(t,e){t.hasOwnProperty("validation")&&this._initField(t),t.validation.errors=e}},{key:"toggleError",value:function(t,e){var i=t.input.closest(K(this.config.field));if($(t.input,this.config.input_error,e),$(t.input,this.config.input_valid,!e),i){$(i,this.config.field_error,e),$(i,this.config.field_valid,!e);var n=i.querySelector(K(this.config.field_message));n.innerHTML=e?t.errors.join("<br>"):"",n.style.display=e?"":"none"}}},{key:"validate",value:function(t){var e=!0,i=this.fields;for(var n in t instanceof HTMLElement&&(i=[t.validation]),i)if(i.hasOwnProperty(n)){var s=i[n];this._validateField(s)?this.toggleError(s,!1):(e=!1,this.toggleError(s,!0))}return e}},{key:"_addEvent",value:function(t){t.addEventListener(this._getEventName(t),this.validateEvent)}},{key:"_addValidators",value:function(t,e,i){var n=this;[].forEach.call(t,(function(t){var s=t.name.replace("data-","");~z.indexOf(s)?n._addValidatorToField(e,i,s,t.value):"type"===t.name&&n._addValidatorToField(e,i,t.value)}))}},{key:"_addValidatorToField",value:function(t,e,i,n){if(X[i]&&(X[i].name=i,t.push(X[i]),n)){var s="pattern"===i?[n]:n.split(",");s.unshift(null),e[i]=s}}},{key:"_onChange",value:function(t){this.validate(t.currentTarget)}},{key:"_removeEvent",value:function(t){t.removeEventListener(this._getEventName(t),this.validateEvent)}},{key:"_getEventName",value:function(t){return~["radio","checkbox"].indexOf(t.getAttribute("type"))||"SELECT"===t.nodeName?"change":"input"}},{key:"_initField",value:function(t){var e=this,i={},n=[];return null!==t.offsetParent&&(this._addValidators(t.attributes,n,i),this._sortValidators(n),this._addEvent(t)),t.validation={form:this.form,input:t,params:i,validate:function(){return e.validate(t)},validators:n}}},{key:"_sortValidators",value:function(t){t.sort((function(t,e){return(e.priority||1)-(t.priority||1)}))}},{key:"_validateField",value:function(t){var e=[],i=!0;for(var n in t.validators)if(t.validators.hasOwnProperty(n)){var s=t.validators[n],r=t.params[s.name]?t.params[s.name]:[];if(r[0]=t.input.value,!s.fn.apply(t.input,r)){i=!1;var o=this.strings[s.name];if(e.push(o.replace(/(\%s)/g,r[1])),!0===s.halt)break}}return t.errors=e,i}}]),t}(),Z=U,tt=function(){function t(e,i){p(this,t),this.button=i,this.config=GLSR.validationconfig,this.events={submit:this._onSubmit.bind(this)},this.form=e,this.isActive=!1,this.recaptcha=new L(this),this.stars=null,this.strings=GLSR.validationstrings,this.useAjax=!e.classList.contains("no-ajax"),this.validation=new Z(e)}return m(t,[{key:"destroy",value:function(){this._destroyForm(),this._destroyRecaptcha(),this._destroyStarRatings(),this.isActive=!1}},{key:"disableButton",value:function(){this.button.setAttribute("aria-busy","true"),this.button.setAttribute("disabled","")}},{key:"enableButton",value:function(){this.button.setAttribute("aria-busy","false"),this.button.removeAttribute("disabled")}},{key:"init",value:function(){this.isActive||(this._initForm(),this._initStarRatings(),this._initRecaptcha(),this.isActive=!0)}},{key:"submitForm",value:function(t){this.disableButton(),this.form[GLSR.nameprefix+"[_counter]"].value=t||0,GLSR.ajax.post(this.form,this._handleResponse.bind(this))}},{key:"_destroyForm",value:function(){this.form.removeEventListener("submit",this.events.submit),this._resetErrors(),this.validation.destroy()}},{key:"_destroyRecaptcha",value:function(){this.recaptcha.reset()}},{key:"_destroyStarRatings",value:function(){this.stars&&this.stars.destroy()}},{key:"_handleResponse",value:function(t,e){var i=!0===e;"unset"!==t.recaptcha?("reset"===t.recaptcha&&this.recaptcha.reset(),i&&(this.recaptcha.reset(),this.form.reset()),this._showFieldErrors(t.errors),this._showResults(t.message,i),this.enableButton(),GLSR.Event.trigger("site-reviews/form/handle",t,this.form),t.form=this.form,document.dispatchEvent(new CustomEvent("site-reviews/after/submission",{detail:t})),i&&""!==t.redirect&&(window.location=t.redirect)):this.recaptcha.execute()}},{key:"_initForm",value:function(){this._destroyForm(),this.form.addEventListener("submit",this.events.submit),this.validation.init()}},{key:"_initRecaptcha",value:function(){this.recaptcha.render()}},{key:"_initStarRatings",value:function(){null!==this.stars?this.stars.rebuild():this.stars=new Y(this.form.querySelectorAll(".glsr-field-rating select"),GLSR.stars)}},{key:"_onSubmit",value:function(t){if(!this.validation.validate())return t.preventDefault(),void this._showResults(this.strings.errors,!1);this._resetErrors(),(this.form["g-recaptcha-response"]&&""===this.form["g-recaptcha-response"].value||this.useAjax)&&(t.preventDefault(),this.submitForm())}},{key:"_resetErrors",value:function(){$(this.form,this.config.form_error,!1),this._showResults("",null),this.validation.reset()}},{key:"_showFieldErrors",value:function(t){if(t)for(var e in t)if(t.hasOwnProperty(e)){var i=GLSR.nameprefix?GLSR.nameprefix+"["+e+"]":e,n=this.form.querySelector('[name="'+i+'"]');n&&(this.validation.setErrors(n,t[e]),this.validation.toggleError(n.validation,"add"))}}},{key:"_showResults",value:function(t,e){var i=this.form.querySelector(K(this.config.form_message));null!==i&&($(this.form,this.config.form_error,!1===e),$(i,this.config.form_message_failed,!1===e),$(i,this.config.form_message_success,!0===e),i.innerHTML=t)}}]),t}(),et=tt;function it(t){if(Array.isArray(t)){for(var e=0,i=Array(t.length);e<t.length;e++)i[e]=t[e];return i}return Array.from(t)}var nt=!1;if("undefined"!=typeof window){var st={get passive(){nt=!0}};window.addEventListener("testPassive",null,st),window.removeEventListener("testPassive",null,st)}var rt="undefined"!=typeof window&&window.navigator&&window.navigator.platform&&(/iP(ad|hone|od)/.test(window.navigator.platform)||"MacIntel"===window.navigator.platform&&window.navigator.maxTouchPoints>1),ot=[],at=!1,lt=-1,ct=void 0,ut=void 0,dt=function(t){return ot.some((function(e){return!(!e.options.allowTouchMove||!e.options.allowTouchMove(t))}))},ht=function(t){var e=t||window.event;return!!dt(e.target)||(e.touches.length>1||(e.preventDefault&&e.preventDefault(),!1))},ft=function(t){if(void 0===ut){var e=!!t&&!0===t.reserveScrollBarGap,i=window.innerWidth-document.documentElement.clientWidth;e&&i>0&&(ut=document.body.style.paddingRight,document.body.style.paddingRight=i+"px")}void 0===ct&&(ct=document.body.style.overflow,document.body.style.overflow="hidden")},vt=function(){void 0!==ut&&(document.body.style.paddingRight=ut,ut=void 0),void 0!==ct&&(document.body.style.overflow=ct,ct=void 0)},pt=function(t){return!!t&&t.scrollHeight-t.scrollTop<=t.clientHeight},gt=function(t,e){var i=t.targetTouches[0].clientY-lt;return!dt(t.target)&&(e&&0===e.scrollTop&&i>0||pt(e)&&i<0?ht(t):(t.stopPropagation(),!0))},mt=function(t,e){if(t){if(!ot.some((function(e){return e.targetElement===t}))){var i={targetElement:t,options:e||{}};ot=[].concat(it(ot),[i]),rt?(t.ontouchstart=function(t){1===t.targetTouches.length&&(lt=t.targetTouches[0].clientY)},t.ontouchmove=function(e){1===e.targetTouches.length&&gt(e,t)},at||(document.addEventListener("touchmove",ht,nt?{passive:!1}:void 0),at=!0)):ft(e)}}else console.error("disableBodyScroll unsuccessful - targetElement must be provided when calling disableBodyScroll on IOS devices.")},yt=function(){rt?(ot.forEach((function(t){t.targetElement.ontouchstart=null,t.targetElement.ontouchmove=null})),at&&(document.removeEventListener("touchmove",ht,nt?{passive:!1}:void 0),at=!1),lt=-1):vt(),ot=[]},bt=["[contenteditable]",'[tabindex]:not([tabindex^="-"])',"a[href]","button:not([disabled]):not([aria-hidden])",'input:not([disabled]):not([type="hidden"]):not([aria-hidden])',"select:not([disabled]):not([aria-hidden])","textarea:not([disabled]):not([aria-hidden])"],wt=function(){function t(e){var i=e.closeTrigger,n=void 0===i?"data-glsr-close":i,s=e.onClose,r=void 0===s?function(){}:s,o=e.onOpen,a=void 0===o?function(){}:o,l=e.openClass,c=void 0===l?"is-open":l,u=e.openTrigger,d=void 0===u?"data-glsr-trigger":u,h=e.targetModalId,f=void 0===h?"glsr-modal":h,v=e.triggers,g=void 0===v?[]:v;p(this,t),this.modal=document.getElementById(f),this.config={openTrigger:d,closeTrigger:n,openClass:c,onOpen:a,onClose:r},this.events={mouseup:this._onClick.bind(this),keydown:this._onKeydown.bind(this),touchstart:this._onClick.bind(this)},g.length>0&&this._registerTriggers(g)}return m(t,[{key:"_closeModal",value:function(){var t=this,e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null;e&&(e.preventDefault(),e.stopPropagation()),this.modal.setAttribute("aria-hidden","true"),this._eventHandler("remove"),yt(),this.activeElement&&this.activeElement.focus&&this.activeElement.focus();var i=function i(){t.modal.classList.remove(t.config.openClass),t.modal.removeEventListener("animationend",i,!1),t.config.onClose(t.modal,t.activeElement,e)};this.modal.addEventListener("animationend",i,!1),GLSR.Event.trigger("site-reviews/modal/close",this.modal,this.activeElement,e)}},{key:"_closeModalById",value:function(t){this.modal=document.getElementById(t),this.modal&&this._closeModal()}},{key:"_eventHandler",value:function(t){this._eventListener(this.modal,t,["mouseup","touchstart"]),this._eventListener(document,t,["keydown"])}},{key:"_eventListener",value:function(t,e,i){var n=this;i.forEach((function(i){return t[e+"EventListener"](i,n.events[i])}))}},{key:"_getFocusableNodes",value:function(){var t=this.modal.querySelectorAll(bt);return Array.prototype.slice.call(t)}},{key:"_onClick",value:function(t){t.target.hasAttribute(this.config.closeTrigger)&&this._closeModal(t)}},{key:"_onKeydown",value:function(t){27===t.keyCode&&this._closeModal(t),9===t.keyCode&&this._retainFocus(t)}},{key:"_openModal",value:function(){var t=this,e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null;this.activeElement=document.activeElement,e&&(e.preventDefault(),this.activeElement=e.currentTarget),this.config.onOpen(this.modal,this.activeElement,e),this.modal.setAttribute("aria-hidden","false"),this.modal.classList.add(this.config.openClass),mt(this.modal.querySelector("[data-glsr-modal]")),this._eventHandler("add");var i=function e(){t.modal.removeEventListener("animationend",e,!1),t._setFocusToFirstNode()};this.modal.addEventListener("animationend",i,!1),GLSR.Event.trigger("site-reviews/modal/open",this.modal,this.activeElement,e)}},{key:"_registerTriggers",value:function(t){var e=this;t.filter(Boolean).forEach((function(t){t.triggerModal&&t.removeEventListener("click",t.triggerModal),t.triggerModal=e._openModal.bind(e),t.addEventListener("click",t.triggerModal)}))}},{key:"_retainFocus",value:function(t){var e=this._getFocusableNodes();if(0!==e.length)if(e=e.filter((function(t){return null!==t.offsetParent})),this.modal.contains(document.activeElement)){var i=e.indexOf(document.activeElement);t.shiftKey&&0===i&&(e[e.length-1].focus(),t.preventDefault()),!t.shiftKey&&e.length>0&&i===e.length-1&&(e[0].focus(),t.preventDefault())}else e[0].focus()}},{key:"_setFocusToFirstNode",value:function(){var t=this,e=this._getFocusableNodes();if(0!==e.length){var i=e.filter((function(e){return!e.hasAttribute(t.config.closeTrigger)}));i.length>0&&i[0].focus(),0===i.length&&e[0].focus()}}}]),t}(),Et={},_t=function(t){if(t)Et[t]._closeModalById(t);else for(var e in Et)Et[e].closeModal()},Lt=function(t){var e=Object.assign({},{openTrigger:"data-glsr-trigger"},t),i=Array.prototype.slice.call(document.querySelectorAll("[".concat(e.openTrigger,"]"))),n=St(i,e.openTrigger);return Object.keys(n).forEach((function(t){e.targetModalId=t,e.triggers=n[t],Et[t]=new wt(e)})),Et},kt=function(t,e){var i=e||{};i.targetModalId=t,Et[t]&&Et[t]._eventHandler("remove"),Et[t]=new wt(i),Et[t]._openModal()},St=function(t,e){var i={};return t.forEach((function(t){var n=t.attributes[e].value;void 0===i[n]&&(i[n]=[]),i[n].push(t)})),i},xt={init:Lt,open:kt,close:_t},Ot={hide:"glsr-hide"},Tt={scrollOffset:16,scrollTime:468},Rt={button:"button.glsr-button-loadmore",link:".glsr-pagination a.page-numbers",pagination:".glsr-pagination",reviews:".glsr-reviews, [data-reviews]"},At=function(){function t(e,i){p(this,t),this.events={button:{click:this._onLoadMore.bind(this)},link:{click:this._onPaginate.bind(this)},window:{popstate:this._onPopstate.bind(this)}},this.paginationEl=i,this.reviewsEl=e.querySelector(Rt.reviews),this.wrapperEl=e}return m(t,[{key:"destroy",value:function(){this._eventHandler("remove")}},{key:"init",value:function(){this._eventHandler("add");var t=this.paginationEl.querySelector(".current");if(t){var e=this._data(t),i=t.nextElementSibling;e&&i&&2==+i.dataset.page&&GLSR.urlparameter&&window.history.replaceState(e,"",window.location)}}},{key:"_data",value:function(t){try{for(var e=JSON.parse(JSON.stringify(this.paginationEl.dataset)),i={},n=0,s=Object.keys(e);n<s.length;n++){var r=s[n],o=void 0;try{o=JSON.parse(e[r])}catch(t){o=e[r]}i["".concat(GLSR.nameprefix,"[atts][").concat(r,"]")]=o}return i["".concat(GLSR.nameprefix,"[_action]")]="fetch-paged-reviews",i["".concat(GLSR.nameprefix,"[page]")]=t.dataset.page||1,i["".concat(GLSR.nameprefix,"[schema]")]=!1,i["".concat(GLSR.nameprefix,"[url]")]=t.href||location.href,i}catch(t){return console.error("Invalid pagination config."),!1}}},{key:"_eventHandler",value:function(t){var e=this;this._eventListener(window,t,this.events.window),this.wrapperEl.querySelectorAll(Rt.button).forEach((function(i){e._eventListener(i,t,e.events.button)})),this.wrapperEl.querySelectorAll(Rt.link).forEach((function(i){e._eventListener(i,t,e.events.link)}))}},{key:"_eventListener",value:function(t,e,i){Object.keys(i).forEach((function(n){return t[e+"EventListener"](n,i[n])}))}},{key:"_handleLoadMore",value:function(t,e,i,n){n?(t.setAttribute("aria-busy","false"),t.removeAttribute("disabled"),this.destroy(),this.paginationEl.innerHTML=i.pagination,this.reviewsEl.insertAdjacentHTML("beforeend",i.reviews),this.init(),GLSR.Event.trigger("site-reviews/pagination/handle",i,this)):window.location=location}},{key:"_handlePagination",value:function(t,e,i,n){n?(this._paginate(i),GLSR.urlparameter&&window.history.pushState(e,"",t.href)):window.location=t.href}},{key:"_handlePopstate",value:function(t,e,i){i?this._paginate(e):console.error(e)}},{key:"_onLoadMore",value:function(t){var e=t.currentTarget,i=this._data(e);i&&(e.setAttribute("aria-busy","true"),e.setAttribute("disabled",""),t.preventDefault(),GLSR.ajax.post(i,this._handleLoadMore.bind(this,e,i)))}},{key:"_onPaginate",value:function(t){var e=t.currentTarget,i=this._data(e);i&&(this.wrapperEl.classList.add(Ot.hide),t.preventDefault(),GLSR.ajax.post(i,this._handlePagination.bind(this,e,i)))}},{key:"_onPopstate",value:function(t){GLSR.Event.trigger("site-reviews/pagination/popstate",t,this),t.state&&t.state["".concat(GLSR.nameprefix,"[_action]")]&&(this.wrapperEl.classList.add(Ot.hide),GLSR.ajax.post(t.state,this._handlePopstate.bind(this,t.state)))}},{key:"_paginate",value:function(t){this.destroy(),this.paginationEl.innerHTML=t.pagination,this.reviewsEl.innerHTML=t.reviews,this.init(),this._scrollToTop(),this.wrapperEl.classList.remove(Ot.hide),GLSR.Event.trigger("site-reviews/pagination/handle",t,this)}},{key:"_scrollStep",value:function(t){var e=Math.min(1,(window.performance.now()-t.startTime)/Tt.scrollTime),i=.5*(1-Math.cos(Math.PI*e)),n=t.startY+(t.endY-t.startY)*i;window.scroll(0,t.offset+n),n!==t.endY&&window.requestAnimationFrame(this._scrollStep.bind(this,t))}},{key:"_scrollToTop",value:function(){var t=Tt.scrollOffset;[].forEach.call(GLSR.ajaxpagination,(function(e){var i=document.querySelector(e);i&&"fixed"===window.getComputedStyle(i).getPropertyValue("position")&&(t+=i.clientHeight)}));var e=this.reviewsEl.getBoundingClientRect().top-t;e>0||this._scrollStep({endY:e,offset:window.pageYOffset,startTime:window.performance.now(),startY:this.reviewsEl.scrollTop})}}]),t}(),Ft=At,Mt={excerpts:"site-reviews/excerpts/init",forms:"site-reviews/forms/init",init:"site-reviews/init",modal:"site-reviews/modal/init",pagination:"site-reviews/pagination/init"},Nt=function(t){new E(t)},Gt=function(){GLSR.forms.forEach((function(t){return t.destroy()})),GLSR.forms=[],document.querySelectorAll("form.glsr-review-form").forEach((function(t){var e=t.querySelector("[type=submit]");if(e){var i=new et(t,e);i.init(),GLSR.forms.push(i)}}))},jt=function(){var t="glsr-modal__content",e="glsr-modal__review";window.GLSR.Modal.init({onClose:function(i,n,s){i.querySelector("."+t).innerHTML="",i.classList.remove(e)},onOpen:function(i,n,s){var r=n.closest(".glsr").cloneNode(!0),o=n.closest(".glsr-review").cloneNode(!0);r.innerHTML="",r.appendChild(o),i.querySelector("."+t).appendChild(r),i.classList.add(e)},openTrigger:"data-excerpt-trigger"})},Pt=function(){GLSR.pagination.forEach((function(t){return t.destroy()})),GLSR.pagination=[],document.querySelectorAll(".glsr").forEach((function(t){var e=t.querySelector(".glsr-pagination");if(e&&(e.classList.contains("glsr-ajax-loadmore")||e.classList.contains("glsr-ajax-pagination"))){var i=new Ft(t,e);i.init(),GLSR.pagination.push(i)}}))},qt=function(){document.querySelectorAll(".glsr").forEach((function(t){var e="glsr-"+window.getComputedStyle(t,null).getPropertyValue("direction");t.classList.add(e)})),v.trigger(Mt.excerpts),v.trigger(Mt.forms),v.trigger(Mt.modal),v.trigger(Mt.pagination)};window.hasOwnProperty("GLSR")||(window.GLSR={}),window.GLSR.ajax=l,window.GLSR.forms=[],window.GLSR.pagination=[],window.GLSR.Event=v,window.GLSR.Modal=xt,v.on(Mt.excerpts,Nt),v.on(Mt.forms,Gt),v.on(Mt.modal,jt),v.on(Mt.pagination,Pt),v.on(Mt.init,qt),v.on("site-reviews/pagination/handle",(function(t,e){v.trigger(Mt.excerpts,e.wrapperEl),v.trigger(Mt.modal)})),document.addEventListener("DOMContentLoaded",(function(){v.trigger(Mt.init)}))},113:function(){},30:function(){},966:function(){},83:function(){},649:function(){},408:function(){},529:function(){},275:function(){},872:function(){},865:function(){},774:function(){},625:function(){},193:function(){},644:function(){},651:function(){},344:function(){},753:function(){},189:function(){},832:function(){},345:function(){},974:function(){},511:function(){},894:function(){},474:function(){},688:function(){},522:function(){},167:function(){},406:function(){},829:function(){},273:function(){},518:function(){},963:function(){}},i={};function n(t){var s=i[t];if(void 0!==s)return s.exports;var r=i[t]={exports:{}};return e[t](r,r.exports,n),r.exports}n.m=e,t=[],n.O=function(e,i,s,r){if(!i){var o=1/0;for(u=0;u<t.length;u++){i=t[u][0],s=t[u][1],r=t[u][2];for(var a=!0,l=0;l<i.length;l++)(!1&r||o>=r)&&Object.keys(n.O).every((function(t){return n.O[t](i[l])}))?i.splice(l--,1):(a=!1,r<o&&(o=r));if(a){t.splice(u--,1);var c=s();void 0!==c&&(e=c)}}return e}r=r||0;for(var u=t.length;u>0&&t[u-1][2]>r;u--)t[u]=t[u-1];t[u]=[i,s,r]},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},function(){var t={341:0,884:0,320:0,974:0,570:0,184:0,979:0,444:0,224:0,384:0,81:0,877:0,691:0,931:0,192:0,77:0,753:0,540:0,69:0,16:0,831:0,181:0,337:0,193:0,994:0,232:0,896:0,737:0,612:0,554:0,483:0,49:0,985:0};n.O.j=function(e){return 0===t[e]};var e=function(e,i){var s,r,o=i[0],a=i[1],l=i[2],c=0;if(o.some((function(e){return 0!==t[e]}))){for(s in a)n.o(a,s)&&(n.m[s]=a[s]);if(l)var u=l(n)}for(e&&e(i);c<o.length;c++)r=o[c],n.o(t,r)&&t[r]&&t[r][0](),t[r]=0;return n.O(u)},i=self.webpackChunk=self.webpackChunk||[];i.forEach(e.bind(null,0)),i.push=e.bind(null,i.push.bind(i))}(),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(533)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(406)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(829)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(273)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(518)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(963)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(113)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(30)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(966)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(83)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(649)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(408)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(529)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(275)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(872)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(865)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(774)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(625)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(193)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(644)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(651)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(344)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(753)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(189)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(832)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(345)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(974)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(511)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(894)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(474)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(688)})),n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(522)}));var s=n.O(void 0,[884,320,974,570,184,979,444,224,384,81,877,691,931,192,77,753,540,69,16,831,181,337,193,994,232,896,737,612,554,483,49,985],(function(){return n(167)}));s=n.O(s)}();