document.addEventListener("DOMContentLoaded",(function(){var e=function(e){var n;if(((null==e||null===(n=e.settings)||void 0===n?void 0:n.base)+"").startsWith("site_review")){var t=window.vc.$frame.get(0);(t.contentWindow||t).GLSR.Event.trigger("site-reviews/init"),e.view.$el.find(".glsr :input,.glsr a").attr("tabindex",-1).css("pointerEvents","none")}};window.vc.events.on("shortcodeView:ready",e),window.vc.events.on("shortcodeView:updated",e)}));