define(function(require, exports, module) {
	var e = require("jquery");
	require("util");
	require("unveil");
	var t = {
		down: "index.php?c=cindexcontroller&m=downLoadApp"
	};
	var n = {
		init: function() {
			this.setHomeModuleTab();
			this.setHomeSlide();
			this.showApp();
			this.initFooter();
			this.lazyLoad()
		},
		initGAT: function() {
			this.gallery({
				gallery: e(".gallery"),
				nav: e(".gallery-nav")
			});
			var t = e(".tour-h2");
			this.initTabEvent({
				nav: t,
				navActive: "selected",
				content: t.next()
			});
			var n = e(".gt-tabtit");
			this.initTabEvent({
				nav: n,
				navActive: "selected",
				content: n.next()
			})
		},
		initZBY: function() {
			this.gallery({
				gallery: e(".gallery"),
				nav: e(".gallery-nav")
			});
			this.setThemeGallery()
		},
		setHomeSlide: function() {
			this.gallery({
				gallery: e(".gallery"),
				nav: e(".gallery-nav")
			})
		},
		gallery: function(t) {
			var n = {
				gallery: null,
				galleryItem: ">li",
				galleryItemActive: "active",
				nav: null,
				navItem: ">a",
				navItemActive: "selected",
				timer: 5e3
			},
				a, i, l, o, r, s, c, u;
			t = e.extend({}, n, t);
			r = t.gallery;
			l = t.galleryItemActive;
			s = t.nav;
			o = t.navItemActive;
			if (!r || !r.length) {
				return
			}
			c = r.find(t.galleryItem);
			if (!c.length) {
				return
			}
			u = s.find(t.navItem);
			i = c.length;

			function f(t) {
				var n = c.filter("." + l),
					a = u.filter("." + o),
					r = n.index(),
					s = !isNaN(t) ? +t : r + 1 === i ? 0 : r + 1;
				n.stop(true).animate({
					opacity: 0
				}, {
					duration: 1e3,
					queue: false,
					complete: function() {
						e(this).removeClass(l)
					}
				});
				a.removeClass(o);
				c.eq(s).stop(true).animate({
					opacity: 1
				}, {
					duration: 800,
					queue: false,
					complete: function() {
						e(this).addClass(l)
					}
				});
				u.eq(s).addClass(o)
			}
			a = setInterval(f, t.timer);
			r.parent().hover(function(e) {
				clearInterval(a);
				return false
			}, function(e) {
				a = setInterval(f, t.timer);
				return false
			});
			s.on("mouseenter", "a", function(t) {
				clearInterval(a);
				f(e(t.target).data("slide"));
				return false
			})
		},
		setThemeGallery: function() {
			var t = e(".m-gallery"),
				n = t.find(".inner"),
				a = t.data("pages"),
				i = t.data("width");
			e(t).delegate(".btn-gallery", "click", function(t) {
				t.preventDefault();
				var l = e(this),
					o = e(".m-gallery"),
					r = parseInt(o.attr("data-curr"));
				if (l.hasClass("l-btn")) {
					if (r > 1) {
						o.attr("data-curr", --r)
					}
				} else {
					if (r < a) {
						o.attr("data-curr", ++r)
					}
				}
				n.stop().animate({
					"margin-left": (1 - r) * i
				}, 750)
			})
		},
		initFooter: function() {
			var t = e("#J_footer_soarea_btn"),
				n = e("#J_footer_soarea");
			t.click(function() {
				e(this).toggleClass("on");
				n.children().show().end().slideToggle()
			})
		},
		setHomeModuleTab: function() {
			var t = e(".home-nav");
			this.initTabEvent({
				nav: t,
				content: t.next()
			})
		},
		initTabEvent: function(t) {
			var n = {
				nav: null,
				content: null,
				navActive: "on",
				contentActive: "selected"
			},
				a, i;
			t = e.extend({}, n, t);
			a = t.navActive;
			i = t.contentActive;
			t.nav.on("mouseenter", "li", function(n) {
				var l = e(this),
					o = l.data("tab");
				if (!l.hasClass(a)) {
					l.addClass(a).siblings().removeClass(a);
					t.content.find("." + o).stop(true).fadeTo(450, 1, function() {
						e(this).addClass(i).find("img").trigger("lookup")
					}).siblings().stop(true).fadeTo(750, 0, function() {
						e(this).removeClass(i)
					})
				}
				return true
			})
		},
		showApp: function() {
			var n = e(".app-close"),
				a = e(".app-inner"),
				i = e(".app-hander"),
				l = e("#J_index_getAppUrl"),
				o = e("#J_index_phone"),
				r = e(".app-cont"),
				s = e("#J_index_tipmsg"),
				c = e("#J_index_downtip"),
				u, f = {
					succ: "短信已发送到您的手机！",
					empty: "请输入手机号",
					format: "请输入正确的手机号",
					limit: "",
					fail: "下载链接短信发送失败"
				};
			u = !! Util.getCookie("mg_cm_t");
			n.on("click", function(e) {
				e.preventDefault();
				e.stopImmediatePropagation();
				a.stop(true).animate({
					left: "-100%"
				}, {
					duration: 800,
					done: function() {
						r.hide();
						i.animate({
							left: 0
						}, 500)
					}
				});
				Util.setCookie("mg_cm_t", 1)
			});
			i.on("click", function(e) {
				e.preventDefault();
				e.stopImmediatePropagation();
				i.stop(true).animate({
					left: "-220px"
				}, {
					duration: 500,
					done: function() {
						r.show();
						a.animate({
							left: 0,
							width: "100%"
						}, 800)
					}
				})
			});

			function d(e, t) {
				var n = ["failed_tips", "succes_tips"],
					a = e ? 0 : 1;
				s.html(t);
				c.removeClass(n[e]).addClass(n[a]).show()
			}
			o.keyup(function(e) {
				if ([13, 108].indexOf(e.which) > -1) {
					l.trigger("click")
				}
			});
			l.on("click", function(n) {
				n.stopImmediatePropagation();
				var a = e(this),
					i = e.trim(o.val());
				if (a.data("run")) {
					return
				}
				if (!i.length) {
					d(1, f.empty);
					return
				} else if (!Util.getRegex(2).test(i)) {
					d(1, f.format);
					return
				}
				a.data("run", true);
				e.ajax({
					type: "POST",
					url: t.down,
					dataType: "json",
					data: "phone=" + i
				}).done(function(e) {
					if (e && e.code == 0) {
						d(0, f.succ);
						a.hide().off("click")
					} else {
						d(1, f.fail)
					}
				}).fail(function() {
					d(1, f.fail)
				}).always(function() {
					a.data("run", false)
				})
			});
			u ? i.css("left", 0) : (a.css("left", 0), r.show())
		},
		lazyLoad: function() {
			var t = e(".home-cont-item img");
			t.unveil(200, function(t) {
				var n = this;
				e.Deferred(function(e) {
					n.onload = function() {
						n.onload = null;
						e.resolve()
					};
					n.setAttribute("src", t);
					return e.promise()
				}).done(function() {
					e(n).removeClass("unveil-img").filter(function() {
						return e(this).data("noimg")
					}).addClass("unfind-img")
				})
			})
		}
	};
	module.exports = n
});