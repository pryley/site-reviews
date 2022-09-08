function _defineProperty(e,t,a){return t in e?Object.defineProperty(e,t,{value:a,enumerable:!0,configurable:!0,writable:!0}):e[t]=a,e}jQuery((function(e){var t=_glsr_deactivate,a=Backbone.Model.extend({defaults:{details:"",name:"",reason:"",slug:"",version:""}}),i=Backbone.Collection.extend({model:a}),n=Backbone.View.extend({className:"glsr-dp-overlay",isBusy:!1,model:null,target:null,template:null,events:{click:"closeOverlay","click .expand-info":"expandDetails","click .glsr-dp-reason":"selectReason","click .submit":"submit",'input input[name="reason"]':"updateModel",'input textarea[name="details"]':"updateModel"},initialize:function(e){_.extend(this,_.pick(e,"target")),this.template=wp.template("glsr-deativate")},render:function(){var e=_.extend({},this.model.toJSON(),t,{action:this.target.attr("href")});return this.$el.html(this.template(e)),this.containFocus(),this},closeOverlay:function(t){var a=this;(27===event.keyCode||e(t.target).is(".close")||e(t.target).is(".deactivate")||e(t.target).is(".glsr-dp-backdrop"))&&(e("body").addClass("closing-overlay"),this.$el.fadeOut(130,(function(){e("body").removeClass("closing-overlay"),e("body").removeClass("modal-open"),a.remove(),a.unbind(),a.target&&a.target.trigger("focus")})))},containFocus:function(){var t=this;_.delay((function(){return e(".glsr-dp-overlay").trigger("focus")}),100),this.$el.on("keydown.glsr",(function(e){var a=t.$el.find(".glsr-dp-header button").first(),i=t.$el.find(".glsr-dp-footer a").last();9===e.which&&(a[0]===e.target&&e.shiftKey?(i.trigger("focus"),e.preventDefault()):i[0]!==e.target||e.shiftKey||(a.trigger("focus"),e.preventDefault()))}))},expandDetails:function(){this.$("#glsr-dp-info").slideToggle("fast")},onChange:function(e){this.isBusy||this.updateModel(e.currentTarget)},selectReason:function(t){var a=e(t.currentTarget);this.$(".glsr-dp-reason").removeClass("is-selected"),this.$(".glsr-dp-details textarea").attr("placeholder",a.toggleClass("is-selected").data("placeholder")),this.$(".glsr-dp-details").slideDown("fast")},submit:function(a){var i=this;a.preventDefault();var n=_defineProperty({},t.ajax.prefix,_.extend({},this.model.toJSON(),{_action:"deactivate",_nonce:t.ajax.nonce}));e(a.currentTarget).addClass("is-busy").prop("disabled",!0).text(t.l10n.processing),wp.ajax.post(t.ajax.action,n).always((function(){window.location.href=i.target.attr("href")}))},updateModel:function(e){this.isBusy||(this.isBusy=!0,this.model.set(e.target.name,e.target.value,{validate:!1}),this.isBusy=!1)}});new(Backbone.View.extend({el:"#the-list",collection:null,overlay:e("#glsr-dp-overlay"),events:{"click a[data-deactivate]":"openOverlay"},initialize:function(){this.collection=new i(t.plugins)},openOverlay:function(t){var a=e(t.target),i=a.data("deactivate"),l=this.collection.findWhere({slug:i});if(l){var s=new n({model:l,target:a});t.preventDefault(),e("body").addClass("modal-open"),s.render(),this.overlay.html(s.el)}}}))}));