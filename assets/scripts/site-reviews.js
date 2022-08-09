!function(){"use strict";let t;const e=function(t){return"json"===this.responseType||!0===this.json?t({message:this.statusText},!1):"text"===this.responseType?t(this.statusText):void console.error(this)},i=function(t){if(0===this.status||this.status>=200&&this.status<300||304===this.status){if("json"===this.responseType)return t(this.response.data,this.response.success);if("text"===this.responseType)return t(this.responseText);if(!0===this.json){const e=JSON.parse(this.response);return t(e.data,e.success)}console.info(this)}else e.bind(this,t)},s=t=>{let e=new FormData;const i=Object.prototype.toString.call(t);return"[object FormData]"===i&&(e=t),"[object HTMLFormElement]"===i&&(e=new FormData(t)),"[object Object]"===i&&Object.keys(t).forEach((i=>e.append(i,t[i]))),e.append("action",GLSR.action),e.append("_ajax_request",!0),e},n=s=>{t=new XMLHttpRequest,t.addEventListener("load",i.bind(t,s)),t.addEventListener("error",e.bind(t,s))},r=e=>{(e=e||{})["X-Requested-With"]="XMLHttpRequest";for(let i in e)e.hasOwnProperty(i)&&t.setRequestHeader(i,e[i])};var o={get:(e,i,s)=>{n(i),t.open("GET",e,!0),t.responseType="text",r(s),t.send()},post:(e,i,o)=>{n(i),t.open("POST",GLSR.ajaxurl,!0),t.responseType="json",t.json=!0,r(o),t.send(s(e))}};const a={},l=function(t,e){const i=a[t]||[],s=[];e&&[].forEach.call(i,(t=>{e!==t.fn&&e!==t.fn.once&&s.push(t)})),s.length?a[t]=s:delete a[t]},c=function(t,e,i){(a[t]||(a[t]=[])).push({fn:e,context:i})};var h={events:a,off:l,on:c,once:function(t,e,i){const s=function(){l(t,s),e.apply(i,arguments)};s.once=e,c(t,s,i)},trigger:function(t){const e=[].slice.call(arguments,1),i=(a[t]||[]).slice();[].forEach.call(i,(t=>t.fn.apply(t.context,e)))}};const d="function",u=(t,e)=>typeof t===e,p=(t,e)=>{null!==e&&(Array.isArray(e)?e.map((e=>p(t,e))):(f.isNode(e)||(e=document.createTextNode(e)),t.appendChild(e)))};function f(t,e){let i,s,n=arguments,r=1;if(t=f.isElement(t)?t:document.createElement(t),u(e,"object")&&!f.isNode(e)&&!Array.isArray(e))for(i in r++,e)s=e[i],i=f.attrMap[i]||i,u(i,d)?i(t,s):u(s,d)?t[i]=s:t.setAttribute(i,s);for(;r<n.length;r++)p(t,n[r]);return t}f.attrMap={},f.isElement=t=>t instanceof Element,f.isNode=t=>t instanceof Node;const v="glsr-read-more",g=".glsr-hidden-text",m=".glsr-tag-value";class y{constructor(t){this.events={click:this._onClick.bind(this)},(t||document).querySelectorAll(g).forEach((t=>this.init(t)))}init(t){const e=this._insertLink(t);e&&("expand"===t.dataset.trigger&&(e.dataset.text=t.dataset.showLess,e.removeEventListener("click",this.events.click),e.addEventListener("click",this.events.click)),"modal"===t.dataset.trigger&&(e.dataset.glsrTrigger="glsr-modal-review"))}_insertLink(t){let e=t.parentElement.querySelector("."+v);e&&e.parentElement.removeChild(e);const i=f("a",{href:"#"},t.dataset.showMore),s=f("span",{class:v},i);return t.appendChild(s).querySelector("a")}_onClick(t){t.preventDefault();const e=t.currentTarget,i=e.parentElement,s=e.closest(m),n=s.querySelector(g),r=e.dataset.text,o=e.innerText;e.dataset.text=o,e.innerText=r,e.removeEventListener("click",this.events.click),"false"===s.dataset.expanded?(s.querySelector("p:last-of-type").appendChild(i),s.dataset.expanded=!0):(n.appendChild(i),s.dataset.expanded=!1),e.addEventListener("click",this.events.click),e.focus()}}var w=t=>{const e=t.dataset.loading,i=t.innerText;return{el:t,loading:()=>{t.setAttribute("aria-busy",!0),t.setAttribute("disabled",""),t.innerHTML='<span class="glsr-loading"></span>'+e||i},loaded:()=>{t.setAttribute("aria-busy",!1),t.removeAttribute("disabled"),t.innerHTML=i}}};class b{constructor(t){this.Form=t,this.captchaEl=this._buildContainer(),this.id=-1,this.instance=null}execute(){this.captchaEl?"friendlycaptcha"===GLSR.captcha.type?setTimeout((()=>{1==+this.captchaEl.dataset.token||".ERROR"===this.Form.form["frc-captcha-solution"].value?this.Form.submitForm():this.execute()}),200):1==+this.captchaEl.dataset.error?this.Form.submitForm("sitekey_invalid"):grecaptcha.execute(this.id,{action:"submit_review"}):this.Form.submitForm()}render(){this.Form.form.onsubmit=null,this.reset(),this.captchaEl&&setTimeout((()=>{if(-1!==this.id||null!==this.instance)return;let t="undefined"==typeof grecaptcha||void 0===grecaptcha.render,e="undefined"==typeof friendlyChallenge||void 0===friendlyChallenge.WidgetInstance;if(t&&e)this.render();else try{"friendlycaptcha"===GLSR.captcha.type?this._renderFrcaptcha():this._renderRecaptcha()}catch(t){console.error(t)}}),200)}reset(){-1!==this.id&&grecaptcha.reset(this.id),this.captchaEl&&(this.captchaEl.dataset.error=0),this.instance&&(this.captchaEl.dataset.token=0,this.instance.reset()),this.is_submitting=!1}_buildContainer(){const t=this.Form.form.querySelector(".glsr-captcha-holder");if(!t)return!1;this.instance&&this.instance.destroy(),Array.from(t.getElementsByClassName(GLSR.captcha.class)).forEach((t=>t.remove()));const e=f("div",{class:GLSR.captcha.class});return t.appendChild(e),e}_renderFrcaptcha(){this.captchaEl.dataset.sitekey=GLSR.captcha.sitekey,this.instance=new friendlyChallenge.WidgetInstance(this.captchaEl,{doneCallback:t=>{this.captchaEl.dataset.token=1},errorCallback:t=>{this.captchaEl.dataset.token=1}})}_renderRecaptcha(){try{this.id=grecaptcha.render(this.captchaEl,{badge:GLSR.captcha.badge,callback:t=>this.Form.submitForm(t),"error-callback":()=>{this.captchaEl.dataset.error=1},"expired-callback":()=>this.reset(),isolated:!0,sitekey:GLSR.captcha.sitekey,size:GLSR.captcha.size,theme:GLSR.captcha.theme})}catch(t){this.captchaEl.dataset.error=1,console.error(t)}}}function E(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function _(t,e){for(var i=0;i<e.length;i++){var s=e[i];s.enumerable=s.enumerable||!1,s.configurable=!0,"value"in s&&(s.writable=!0),Object.defineProperty(t,s.key,s)}}function L(t,e,i){return e&&_(t.prototype,e),i&&_(t,i),t}var S={classNames:{active:"gl-active",base:"gl-star-rating",selected:"gl-selected"},clearable:!0,maxStars:10,prebuilt:!1,stars:null,tooltip:"Select a Rating"},x=function(t,e,i){t.classList[e?"add":"remove"](i)},k=function(t){var e=document.createElement("span");for(var i in t=t||{})e.setAttribute(i,t[i]);return e},R=function(t,e,i){var s=k(i);return t.parentNode.insertBefore(s,e?t.nextSibling:t),s},T=function t(){for(var e=arguments.length,i=new Array(e),s=0;s<e;s++)i[s]=arguments[s];var n={};return i.forEach((function(e){Object.keys(e||{}).forEach((function(s){if(void 0!==i[0][s]){var r=e[s];"Object"!==A(r)||"Object"!==A(n[s])?n[s]=r:n[s]=t(n[s],r)}}))})),n},A=function(t){return{}.toString.call(t).slice(8,-1)},F=function(){function t(e,i){var s,n,r;E(this,t),this.direction=window.getComputedStyle(e,null).getPropertyValue("direction"),this.el=e,this.events={change:this.onChange.bind(this),keydown:this.onKeyDown.bind(this),mousedown:this.onPointerDown.bind(this),mouseleave:this.onPointerLeave.bind(this),mousemove:this.onPointerMove.bind(this),reset:this.onReset.bind(this),touchend:this.onPointerDown.bind(this),touchmove:this.onPointerMove.bind(this)},this.indexActive=null,this.indexSelected=null,this.props=i,this.tick=null,this.ticking=!1,this.values=function(t){var e=[];return[].forEach.call(t.options,(function(t){var i=parseInt(t.value,10)||0;i>0&&e.push({index:t.index,text:t.text,value:i})})),e.sort((function(t,e){return t.value-e.value}))}(e),this.widgetEl=null,this.el.widget&&this.el.widget.destroy(),s=this.values.length,n=1,r=this.props.maxStars,/^\d+$/.test(s)&&n<=s&&s<=r?this.build():this.destroy()}return L(t,[{key:"build",value:function(){this.destroy(),this.buildWidget(),this.selectValue(this.indexSelected=this.selected(),!1),this.handleEvents("add"),this.el.widget=this}},{key:"buildWidget",value:function(){var t,e,i=this;this.props.prebuilt?(t=this.el.parentNode,e=t.querySelector("."+this.props.classNames.base+"--stars")):((t=R(this.el,!1,{class:this.props.classNames.base})).appendChild(this.el),e=R(this.el,!0,{class:this.props.classNames.base+"--stars"}),this.values.forEach((function(t,s){var n=k({"data-index":s,"data-value":t.value});"function"==typeof i.props.stars&&i.props.stars.call(i,n,t,s),[].forEach.call(n.children,(function(t){return t.style.pointerEvents="none"})),e.innerHTML+=n.outerHTML}))),t.dataset.starRating="",t.classList.add(this.props.classNames.base+"--"+this.direction),this.props.tooltip&&e.setAttribute("role","tooltip"),this.widgetEl=e}},{key:"changeIndexTo",value:function(t,e){var i=this;if(this.indexActive!==t||e){if([].forEach.call(this.widgetEl.children,(function(e,s){x(e,s<=t,i.props.classNames.active),x(e,s===i.indexSelected,i.props.classNames.selected)})),this.widgetEl.setAttribute("data-rating",t+1),"function"==typeof this.props.stars||this.props.prebuilt||(this.widgetEl.classList.remove("s"+10*(this.indexActive+1)),this.widgetEl.classList.add("s"+10*(t+1))),this.props.tooltip){var s,n=t<0?this.props.tooltip:null===(s=this.values[t])||void 0===s?void 0:s.text;this.widgetEl.setAttribute("aria-label",n)}this.indexActive=t}this.ticking=!1}},{key:"destroy",value:function(){this.indexActive=null,this.indexSelected=this.selected();var t=this.el.parentNode;t.classList.contains(this.props.classNames.base)&&(this.props.prebuilt?(this.widgetEl=t.querySelector("."+this.props.classNames.base+"--stars"),t.classList.remove(this.props.classNames.base+"--"+this.direction),delete t.dataset.starRating):t.parentNode.replaceChild(this.el,t),this.handleEvents("remove")),delete this.el.widget}},{key:"eventListener",value:function(t,e,i,s){var n=this;i.forEach((function(i){return t[e+"EventListener"](i,n.events[i],s||!1)}))}},{key:"handleEvents",value:function(t){var e=this.el.closest("form");e&&"FORM"===e.tagName&&this.eventListener(e,t,["reset"]),this.eventListener(this.el,t,["change"]),"add"===t&&this.el.disabled||(this.eventListener(this.el,t,["keydown"]),this.eventListener(this.widgetEl,t,["mousedown","mouseleave","mousemove","touchend","touchmove"],!1))}},{key:"indexFromEvent",value:function(t){var e,i,s=(null===(e=t.touches)||void 0===e?void 0:e[0])||(null===(i=t.changedTouches)||void 0===i?void 0:i[0])||t,n=document.elementFromPoint(s.clientX,s.clientY);return n.parentNode===this.widgetEl?[].slice.call(n.parentNode.children).indexOf(n):this.indexActive}},{key:"onChange",value:function(){this.changeIndexTo(this.selected(),!0)}},{key:"onKeyDown",value:function(t){var e=t.key.slice(5);if(~["Left","Right"].indexOf(e)){t.preventDefault();var i="Left"===e?-1:1;"rtl"===this.direction&&(i*=-1);var s=this.values.length-1,n=Math.min(Math.max(this.selected()+i,-1),s);this.selectValue(n,!0)}}},{key:"onPointerDown",value:function(t){t.preventDefault();var e=this.indexFromEvent(t);this.props.clearable&&e===this.indexSelected&&(e=-1),this.selectValue(e,!0)}},{key:"onPointerLeave",value:function(t){var e=this;t.preventDefault(),cancelAnimationFrame(this.tick),requestAnimationFrame((function(){return e.changeIndexTo(e.indexSelected)}))}},{key:"onPointerMove",value:function(t){var e=this;t.preventDefault(),this.ticking||(this.tick=requestAnimationFrame((function(){return e.changeIndexTo(e.indexFromEvent(t))})),this.ticking=!0)}},{key:"onReset",value:function(){var t,e=this.valueIndex(null===(t=this.el.querySelector("[selected]"))||void 0===t?void 0:t.value);this.selectValue(e||-1,!1)}},{key:"selected",value:function(){return this.valueIndex(this.el.value)}},{key:"selectValue",value:function(t,e){var i;this.el.value=(null===(i=this.values[t])||void 0===i?void 0:i.value)||"",this.indexSelected=this.selected(),!1===e?this.changeIndexTo(this.selected(),!0):this.el.dispatchEvent(new Event("change"))}},{key:"valueIndex",value:function(t){return this.values.findIndex((function(e){return e.value===+t}))}}]),t}(),N=function(){function t(e,i){E(this,t),this.destroy=this.destroy.bind(this),this.props=i,this.rebuild=this.rebuild.bind(this),this.selector=e,this.widgets=[],this.build()}return L(t,[{key:"build",value:function(){var t=this;this.queryElements(this.selector).forEach((function(e){var i=T(S,t.props,JSON.parse(e.getAttribute("data-options")));"SELECT"!==e.tagName||e.widget||(!i.prebuilt&&e.parentNode.classList.contains(i.classNames.base)&&t.unwrap(e),t.widgets.push(new F(e,i)))}))}},{key:"destroy",value:function(){this.widgets.forEach((function(t){return t.destroy()})),this.widgets=[]}},{key:"queryElements",value:function(t){return"HTMLSelectElement"===A(t)?[t]:"NodeList"===A(t)?[].slice.call(t):"String"===A(t)?[].slice.call(document.querySelectorAll(t)):[]}},{key:"rebuild",value:function(){this.destroy(),this.build()}},{key:"unwrap",value:function(t){var e=t.parentNode,i=e.parentNode;i.insertBefore(t,e),i.removeChild(e)}}]),t}();const G={classNames:{base:"glsr-star-rating"},clearable:!1,tooltip:!1};const M=(t,e,i)=>{t&&e.split(" ").forEach((e=>{t.classList[i?"add":"remove"](e)}))},O=t=>"."+t.trim().split(" ").join("."),C=function(t){let e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:200,i=null;return function(){clearTimeout(i);for(var s=arguments.length,n=new Array(s),r=0;r<s;r++)n[r]=arguments[r];i=setTimeout(t,e,...n)}},q=t=>{let e='input[name="'+t.getAttribute("name")+'"]:checked';return t.validation.form.querySelectorAll(e).length},j={email:{fn:function(t){return!t||/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(t)}},max:{fn:function(t,e){return!t||("checkbox"===this.type?q(this)<=parseInt(e):parseFloat(t)<=parseFloat(e))}},maxlength:{fn:function(t,e){return!t||t.length<=parseInt(e)}},min:{fn:function(t,e){return!t||("checkbox"===this.type?q(this)>=parseInt(e):parseFloat(t)>=parseFloat(e))}},minlength:{fn:function(t,e){return!t||t.length>=parseInt(e)}},number:{fn:function(t){return!t||!isNaN(parseFloat(t))},priority:2},pattern:{fn:function(t,e){let i=e.match(new RegExp("^/(.*?)/([gimy]*)$"));return!t||new RegExp(i[1],i[2]).test(t)}},required:{fn:function(t){return"radio"===this.type||"checkbox"===this.type?q(this):void 0!==t&&""!==t},priority:99,halt:!0},tel:{fn:function(t){let e=t.replace(/[^0-9]/g,"").length;return!t||4<=e&&15>=e&&new RegExp("^[+]?[\\d\\s()-]*$").test(t)}},url:{fn:function(t){return!t||new RegExp("^(https?)://([\\p{L}\\p{N}\\p{S}\\-_.])+(.?([\\p{L}\\p{N}]|xn--[\\p{L}\\p{N}\\-]+)+.?)(:[0-9]+)?(?:/(?:[\\p{L}\\p{N}\\-._~!$&'()*+,;=:@]|%[0-9A-Fa-f]{2})*)*(?:\\?(?:[\\p{L}\\p{N}\\-._~!$&'\\[\\]()*+,;=:@/?]|%[0-9A-Fa-f]{2})*)?(?:#(?:[\\p{L}\\p{N}\\-._~!$&'()*+,;=:@/?]|%[0-9A-Fa-f]{2})*)?$","iu").test(t)}}},P=["required","max","maxlength","min","minlength","pattern"];class H{constructor(t){this.config=GLSR.validationconfig,this.fields=[],this.form=t,this.form.setAttribute("novalidate",""),this.strings=GLSR.validationstrings,this.validateEvent=this._onChange.bind(this)}destroy(){for(this.reset();this.fields.length;){const t=this.fields.shift();this._removeEvent(t.input),delete t.input.validation}}init(){this.form.querySelectorAll("input:not([type^=hidden]):not([type^=submit]), select, textarea, [data-glsr-validate]").forEach((t=>{if(this.fields.find((e=>e.input.name===t.name&&!t.name.endsWith("[]"))))return;let e=t.closest(O(this.config.field));e&&"none"!==e.style.display&&this.fields.push(this._initField(t))}))}reset(){for(let t in this.fields){if(!this.fields.hasOwnProperty(t))continue;this.fields[t].errorElements=null;let e=this.fields[t].input.closest(O(this.config.field));M(this.fields[t].input,this.config.input_error,!1),M(this.fields[t].input,this.config.input_valid,!1),M(e,this.config.field_error,!1),M(e,this.config.field_valid,!1)}}setErrors(t,e){t.hasOwnProperty("validation")&&this._initField(t),t.validation.errors=e}toggleError(t,e){let i=t.input.closest(O(this.config.field));if(M(t.input,this.config.input_error,e),M(t.input,this.config.input_valid,!e),i){M(i,this.config.field_error,e),M(i,this.config.field_valid,!e);let s=i.querySelector(O(this.config.field_message));s.innerHTML=e?t.errors.join("<br>"):"",s.style.display=e?"":"none"}}validate(t){let e=!0,i=this.fields;t instanceof HTMLElement&&(i=[t.validation]);for(let t in i){if(!i.hasOwnProperty(t))continue;const s=i[t];this._validateField(s)?this.toggleError(s,!1):(e=!1,this.toggleError(s,!0))}return e}_addEvent(t){t.addEventListener(this._getEventName(t),this.validateEvent)}_addValidators(t,e,i){[].forEach.call(t,(t=>{let s=t.name.replace("data-","");~P.indexOf(s)?this._addValidatorToField(e,i,s,t.value):"type"===t.name&&this._addValidatorToField(e,i,t.value)}))}_addValidatorToField(t,e,i,s){if(j[i]&&(j[i].name=i,t.push(j[i]),s)){var n="pattern"===i?[s]:s.split(",");n.unshift(null),e[i]=n}}_onChange(t){this.validate(t.currentTarget)}_removeEvent(t){t.removeEventListener(this._getEventName(t),this.validateEvent)}_getEventName(t){return~["radio","checkbox"].indexOf(t.getAttribute("type"))||"SELECT"===t.nodeName?"change":"input"}_initField(t){let e={},i=[];return null!==t.offsetParent&&(this._addValidators(t.attributes,i,e),this._sortValidators(i),this._addEvent(t)),t.validation={form:this.form,input:t,params:e,validate:()=>this.validate(t),validators:i}}_sortValidators(t){t.sort(((t,e)=>(e.priority||1)-(t.priority||1)))}_validateField(t){let e=[],i=!0;for(let s in t.validators){if(!t.validators.hasOwnProperty(s))continue;let n=t.validators[s],r=t.params[n.name]?t.params[n.name]:[];if(r[0]=t.input.value,!n.fn.apply(t.input,r)){i=!1;let t=this.strings[n.name];if(e.push(t.replace(/(\%s)/g,r[1])),!0===n.halt)break}}return t.errors=e,i}}class D{constructor(t,e){this.button=w(e),this.config=GLSR.validationconfig,this.events={submit:this._onSubmit.bind(this)},this.form=t,this.isActive=!1,this.stars=(()=>{let t=null;const e=()=>!!t&&(t.rebuild(),!0);return{init:function(i){let s=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};return e()||(t=new N(i,Object.assign({},G,s))),t},destroy:()=>!!t&&(t.destroy(),!0),rebuild:e}})(),this.strings=GLSR.validationstrings,this.useAjax=!t.classList.contains("no-ajax"),this.captcha=new b(this),this.validation=new H(t)}destroy(){this._destroyForm(),this.stars.destroy(),this.captcha.reset(),this.isActive=!1}init(){this.isActive||(this._initForm(),this.stars.init(this.form.querySelectorAll(".glsr-field-rating select"),GLSR.starsconfig),this.captcha.render(),this.isActive=!0)}submitForm(t){this.button.loading(),this.form["g-recaptcha-response"]&&(this.form["g-recaptcha-response"].value=t),GLSR.ajax.post(this.form,this._handleResponse.bind(this))}_destroyForm(){this.form.removeEventListener("submit",this.events.submit),this._resetErrors(),this.validation.destroy()}_handleResponse(t,e){const i=!0===e;this.captcha.reset(),i&&this.form.reset(),this._showFieldErrors(t.errors),this._showResults(t.message,i),this.button.loaded(),GLSR.Event.trigger("site-reviews/form/handle",t,this.form),t.form=this.form,document.dispatchEvent(new CustomEvent("site-reviews/after/submission",{detail:t})),i&&""!==t.redirect&&(window.location=t.redirect)}_initForm(){this._destroyForm(),this.form.addEventListener("submit",this.events.submit),this.validation.init()}_onSubmit(t){if(!this.validation.validate())return t.preventDefault(),void this._showResults(this.strings.errors,!1);t.preventDefault(),this._resetErrors(),this.button.loading(),this.captcha.execute()}_resetErrors(){M(this.form,this.config.form_error,!1),this._showResults("",null),this.validation.reset()}_showFieldErrors(t){if(t)for(let e in t){if(!t.hasOwnProperty(e))continue;const i=GLSR.nameprefix?GLSR.nameprefix+"["+e+"]":e,s=this.form.querySelector('[name="'+i+'"]');s&&(this.validation.setErrors(s,t[e]),this.validation.toggleError(s.validation,"add"))}}_showResults(t,e){const i=this.form.querySelector(O(this.config.form_message));null!==i&&(M(this.form,this.config.form_error,!1===e),M(i,this.config.form_message_failed,!1===e),M(i,this.config.form_message_success,!0===e),i.innerHTML=t)}}var I=function(){return"undefined"==typeof window},V=function(t){t=t||navigator.userAgent;var e=/(iPad).*OS\s([\d_]+)/.test(t);return{ios:!e&&/(iPhone\sOS)\s([\d_]+)/.test(t)||e,android:/(Android);?[\s/]+([\d.]+)?/.test(t)}};var W=0,Y=0,$=0,B=null,K=!1,X=[],J=function(t){if(I())return!1;if(!t)throw new Error("options must be provided");var e=!1,i={get passive(){e=!0}},s=function(){},n="__TUA_BSL_TEST_PASSIVE__";window.addEventListener(n,s,i),window.removeEventListener(n,s,i);var r=t.capture;return e?t:void 0!==r&&r}({passive:!1}),z=!I()&&"scrollBehavior"in document.documentElement.style,U=function(t){t.cancelable&&t.preventDefault()},Q=function(t){t||null!==t&&"production"!==process.env.NODE_ENV&&console.warn("If scrolling is also required in the floating layer, the target element must be provided.")},Z=function(t){if(!I()){if(Q(t),V().ios){if(t)(Array.isArray(t)?t:[t]).forEach((function(t){t&&-1===X.indexOf(t)&&(t.ontouchstart=function(t){Y=t.targetTouches[0].clientY,$=t.targetTouches[0].clientX},t.ontouchmove=function(e){1===e.targetTouches.length&&function(t,e){if(e){var i=e.scrollTop,s=e.scrollLeft,n=e.scrollWidth,r=e.scrollHeight,o=e.clientWidth,a=e.clientHeight,l=t.targetTouches[0].clientX-$,c=t.targetTouches[0].clientY-Y,h=Math.abs(c)>Math.abs(l);if(h&&(c>0&&0===i||c<0&&i+a+1>=r)||!h&&(l>0&&0===s||l<0&&s+o+1>=n))return U(t)}t.stopPropagation()}(e,t)},X.push(t))}));K||(document.addEventListener("touchmove",U,J),K=!0)}else W<=0&&(B=V().android?(e=document.documentElement,i=document.body,s=e.scrollTop||i.scrollTop,n=Object.assign({},e.style),r=Object.assign({},i.style),e.style.height="100%",e.style.overflow="hidden",i.style.top="-".concat(s,"px"),i.style.width="100%",i.style.height="auto",i.style.position="fixed",i.style.overflow="hidden",function(){e.style.height=n.height||"",e.style.overflow=n.overflow||"",["top","width","height","overflow","position"].forEach((function(t){i.style[t]=r[t]||""})),z?window.scrollTo({top:s,behavior:"instant"}):window.scrollTo(0,s)}):function(){var t=document.body,e=Object.assign({},t.style),i=window.innerWidth-t.clientWidth;return t.style.overflow="hidden",t.style.boxSizing="border-box",t.style.paddingRight="".concat(i,"px"),function(){["overflow","boxSizing","paddingRight"].forEach((function(i){t.style[i]=e[i]||""}))}}());var e,i,s,n,r;W+=1}};const tt=["[contenteditable]",'[tabindex]:not([tabindex^="-"])',"a[href]","button:not([disabled]):not([aria-hidden])",'input:not([disabled]):not([type="hidden"]):not([aria-hidden])',"select:not([disabled]):not([aria-hidden])","textarea:not([disabled]):not([aria-hidden])"],et={focus:!1,onClose:()=>{},onOpen:()=>{}},it="data-glsr-close",st="glsr-modal",nt="is-open",rt="data-glsr-trigger",ot=function(t){let e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};return e.class="glsr-modal__"+t,e};class at{constructor(t){let e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};this.config=Object.assign({},et,e),this.events={_open:this._openModal.bind(this),mouseup:this._onClick.bind(this),keydown:this._onKeydown.bind(this),touchstart:this._onClick.bind(this)},this.id=t,this.triggers=[],this._reset()}_closeModal(){let t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null;var e;t&&(t.preventDefault(),t.stopPropagation()),this.root.setAttribute("aria-hidden","true"),this._eventHandler("remove"),e=this.content,I()||(Q(e),(W-=1)>0)||(V().ios||"function"!=typeof B?(e&&(Array.isArray(e)?e:[e]).forEach((function(t){var e=X.indexOf(t);-1!==e&&(t.ontouchmove=null,t.ontouchstart=null,X.splice(e,1))})),K&&(document.removeEventListener("touchmove",U,J),K=!1)):B()),this.trigger&&this.trigger.focus&&this.trigger.focus();const i=()=>{this.root.removeEventListener("animationend",i,!1),this.root.classList.remove(nt),ct.pop(),this.config.onClose(this,t),GLSR.Event.trigger("site-reviews/modal/close",this.root,this.trigger,t),C((()=>this._reset()))()};this.root.addEventListener("animationend",i,!1)}_eventHandler(t){this._eventListener(this.close,t,["keydown"]),this._eventListener(this.root,t,["mouseup","touchstart"]),this._eventListener(document,t,["keydown"])}_eventListener(t,e,i){t&&i.forEach((i=>t[e+"EventListener"](i,this.events[i])))}_focusableNodes(){return[].slice.call(this.root.querySelectorAll(tt))}_insertModal(){const t=f("button",ot("close",{"aria-label":GLSR.text.closemodal,"data-glsr-close":""})),e=f("div",ot("content",{tabindex:-1})),i=f("div",ot("header")),s=f("div",ot("footer")),n=f("div",{class:st,id:this.id,"aria-hidden":!0},f("div",ot("overlay",{tabindex:-1,"data-glsr-close":""}),f("div",ot("dialog",{"aria-modal":!0,role:"dialog"}),t,i,e,s)));this.close=t,this.content=e,this.footer=s,this.header=i,this.root=document.body.appendChild(n)}_onClick(t){t.target.hasAttribute(it)&&this._closeModal(t)}_onKeydown(t){~[13,32].indexOf(t.keyCode)&&t.target===this.close&&this._closeModal(t),27===t.keyCode&&ct.slice(-1)[0]===this.id&&this._closeModal(t),9===t.keyCode&&this._retainFocus(t)}_openModal(t){ct.push(this.id),this.trigger=document.activeElement,t&&(t.preventDefault(),this.trigger=t.currentTarget),this._insertModal(),Z(this.content),this.config.onOpen(this,t),GLSR.Event.trigger("site-reviews/modal/open",this.root,this.trigger,t),this.root.setAttribute("aria-hidden","false"),this.root.classList.add(nt),this._eventHandler("add");const e=()=>{this.root.removeEventListener("animationend",e,!1),this._setFocusToFirstNode()};this.root.addEventListener("animationend",e,!1)}_registerTrigger(t){this._removeTrigger(t),t.addEventListener("click",this.events._open),this.triggers.push(t)}_removeTrigger(t){this.triggers.filter((e=>e!==t)),t.removeEventListener("click",this.events._open)}_removeTriggers(){this.triggers.forEach((t=>this._removeTrigger(t))),this.triggers=[]}_reset(){this.root&&this.root.remove(),this.close=null,this.content=null,this.footer=null,this.header=null,this.trigger=null}_retainFocus(t){let e=this._focusableNodes();if(0!==e.length)if(e=e.filter((t=>null!==t.offsetParent)),this.root.contains(document.activeElement)){const i=e.indexOf(document.activeElement);t.shiftKey&&0===i?(e[e.length-1].focus(),t.preventDefault()):!t.shiftKey&&e.length>0&&i===e.length-1&&(e[0].focus(),t.preventDefault())}else e[0].focus()}_setFocusToFirstNode(){if(!this.config.focus)return;const t=this._focusableNodes();if(0===t.length)return;const e=t.filter((t=>!t.hasAttribute(it)));e.length>0?e[0].focus():0===e.length&&t[0].focus()}}const lt={},ct=[];var ht={close:t=>{if(t)lt[t]&&lt[t]._closeModal();else for(let t in lt)lt[t]._closeModal()},init:(t,e)=>{const i=lt[t]||new at(t,e);return i._removeTriggers(),document.querySelectorAll("["+rt+"]").forEach((e=>{t===e.attributes[rt].value&&i._registerTrigger(e)})),lt[t]=i,lt},open:(t,e)=>{const i=lt[t]||new at(t,e);i.root&&i._eventHandler("remove"),lt[t]=i,i._openModal()}};const dt="glsr-hide",ut=16,pt=468,ft="button.glsr-button-loadmore",vt=".glsr-pagination a[data-page]",gt=".glsr-reviews, [data-reviews]";class mt{constructor(t,e){this.events={button:{click:this._onLoadMore.bind(this)},link:{click:this._onPaginate.bind(this)},window:{popstate:this._onPopstate.bind(this)}},this.paginationEl=e,this.reviewsEl=t.querySelector(gt),this.wrapperEl=t}destroy(){this._eventHandler("remove")}init(){this._eventHandler("add");const t=this.paginationEl.querySelector(".current");if(t){const e=this._data(t),i=t.nextElementSibling;e&&i&&2==+i.dataset.page&&GLSR.urlparameter&&window.history.replaceState(e,"",window.location)}}_data(t){try{const i=JSON.parse(JSON.stringify(this.paginationEl.dataset)),s={};for(var e of Object.keys(i)){let t;try{t=JSON.parse(i[e])}catch(s){t=i[e]}s["".concat(GLSR.nameprefix,"[atts][").concat(e,"]")]=t}return s["".concat(GLSR.nameprefix,"[_action]")]="fetch-paged-reviews",s["".concat(GLSR.nameprefix,"[page]")]=t.dataset.page||1,s["".concat(GLSR.nameprefix,"[schema]")]=!1,s["".concat(GLSR.nameprefix,"[url]")]=t.href||location.href,s}catch(t){return console.error("Invalid pagination config."),!1}}_eventHandler(t){this._eventListener(window,t,this.events.window),this.wrapperEl.querySelectorAll(ft).forEach((e=>{this._eventListener(e,t,this.events.button)})),this.wrapperEl.querySelectorAll(vt).forEach((e=>{this._eventListener(e,t,this.events.link)}))}_eventListener(t,e,i){Object.keys(i).forEach((s=>t[e+"EventListener"](s,i[s])))}_handleLoadMore(t,e,i,s){s?(t.loaded(),this.destroy(),this.paginationEl.innerHTML=i.pagination,this.reviewsEl.insertAdjacentHTML("beforeend",i.reviews),this.init(),GLSR.Event.trigger("site-reviews/pagination/handle",i,this)):window.location=location}_handlePagination(t,e,i,s){s?(this._paginate(i),GLSR.urlparameter&&window.history.pushState(e,"",t.href)):window.location=t.href}_handlePopstate(t,e,i){i?this._paginate(e):console.error(e)}_loaded(){const t=this.paginationEl.querySelector(".glsr-spinner");t&&this.paginationEl.removeChild(t),this.wrapperEl.classList.remove(dt)}_loading(){this.wrapperEl.classList.add(dt),this.paginationEl.insertAdjacentHTML("beforeend",'<div class="glsr-spinner"></div>')}_onLoadMore(t){const e=t.currentTarget,i=this._data(e);if(i){const s=w(e);s.loading(),t.preventDefault(),GLSR.ajax.post(i,this._handleLoadMore.bind(this,s,i))}}_onPaginate(t){const e=t.currentTarget,i=this._data(e);i&&(this._loading(),t.preventDefault(),GLSR.ajax.post(i,this._handlePagination.bind(this,e,i)))}_onPopstate(t){GLSR.Event.trigger("site-reviews/pagination/popstate",t,this),t.state&&t.state["".concat(GLSR.nameprefix,"[_action]")]&&(this._loading(),GLSR.ajax.post(t.state,this._handlePopstate.bind(this,t.state)))}_paginate(t){this.destroy(),this.paginationEl.innerHTML=t.pagination,this.reviewsEl.innerHTML=t.reviews,this.init(),this._scrollToTop(),this._loaded(),GLSR.Event.trigger("site-reviews/pagination/handle",t,this)}_scrollStep(t){const e=Math.min(1,(window.performance.now()-t.startTime)/pt),i=.5*(1-Math.cos(Math.PI*e)),s=t.startY+(t.endY-t.startY)*i;window.scroll(0,t.offset+s),s!==t.endY&&window.requestAnimationFrame(this._scrollStep.bind(this,t))}_scrollToTop(){let t=ut;[].forEach.call(GLSR.ajaxpagination,(e=>{const i=document.querySelector(e);i&&"fixed"===window.getComputedStyle(i).getPropertyValue("position")&&(t+=i.clientHeight)}));const e=this.reviewsEl.getBoundingClientRect().top-t;e>0||this._scrollStep({endY:e,offset:window.pageYOffset,startTime:window.performance.now(),startY:this.reviewsEl.scrollTop})}}const yt="site-reviews/excerpts/init",wt="site-reviews/forms/init",bt="site-reviews/init",Et="site-reviews/modal/init",_t="site-reviews/pagination/init";window.hasOwnProperty("GLSR")||(window.GLSR={}),window.GLSR.ajax=o,window.GLSR.forms=[],window.GLSR.pagination=[],window.GLSR.Event=h,window.GLSR.Modal=ht,window.GLSR.Utils={debounce:C,dom:f},h.on(yt,(t=>{new y(t)})),h.on(wt,(()=>{GLSR.forms.forEach((t=>t.destroy())),GLSR.forms=[],document.querySelectorAll("form.glsr-review-form").forEach((t=>{const e=t.querySelector("[type=submit]");if(e){const i=new D(t,e);i.init(),GLSR.forms.push(i)}}))})),h.on(Et,(()=>{GLSR.Modal.init("glsr-modal-review",{onOpen:t=>{const e=t.trigger.closest(".glsr").cloneNode(!0),i=t.trigger.closest(".glsr-review").cloneNode(!0);e.innerHTML="",e.appendChild(i),t.content.appendChild(e)}})})),h.on(_t,(()=>{GLSR.pagination.forEach((t=>t.destroy())),GLSR.pagination=[],document.querySelectorAll(".glsr").forEach((t=>{const e=t.querySelector(".glsr-pagination");if(e&&(e.classList.contains("glsr-ajax-loadmore")||e.classList.contains("glsr-ajax-pagination"))){const i=new mt(t,e);i.init(),GLSR.pagination.push(i)}}))})),h.on(bt,(()=>{document.querySelectorAll(".glsr").forEach((t=>{const e="glsr-"+window.getComputedStyle(t,null).getPropertyValue("direction");t.classList.add(e)})),h.trigger(yt),h.trigger(wt),h.trigger(Et),h.trigger(_t)})),h.on("site-reviews/pagination/handle",((t,e)=>{h.trigger(yt,e.wrapperEl),h.trigger(Et)})),document.addEventListener("DOMContentLoaded",(()=>{h.trigger(bt)}))}();
