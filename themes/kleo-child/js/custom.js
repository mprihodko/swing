function gformInitDatepicker() {
	jQuery(".datepicker").each(function() {
		var a = jQuery(this),
			b = this.id
		if(a.attr('id')=='input_3_26'){
		
		var	c = {
				yearRange: "-100:-18",
				showOn: "focus",
				dateFormat: "mm/dd/yy",
				changeMonth: !0,
				changeYear: !0,
				suppressDatePicker: !1,
				onClose: function() {
					a.focus();
					var b = this;
					this.suppressDatePicker = !0, setTimeout(function() {
						b.suppressDatePicker = !1
					}, 200)
				},
				beforeShow: function(a, b) {
					return !this.suppressDatePicker
				}
			};
		}
		else if (a.attr('id')=='input_9_6') {
			var	c = {
			yearRange: "-0:+2",
			showOn: "focus",
			dateFormat: "mm/dd/yy",
			minDate: 0,
			changeMonth: !0,
			changeYear: !0,
			suppressDatePicker: !1,
			beforeShowDay: function(date) {
        var day = date.getDay();
        return [(day == 5)];
    	},
			onClose: function() {
				a.focus();
				var b = this;
				this.suppressDatePicker = !0, setTimeout(function() {
					b.suppressDatePicker = !1
				}, 200)
			},
			beforeShow: function(a, b) {
				return !this.suppressDatePicker
			}
		};
		}
		else if (a.attr('id')=='input_9_5') {
			var	c = {
			yearRange: "-0:+2",
			showOn: "focus",
			dateFormat: "mm/dd/yy",
			minDate: 0,
			changeMonth: !0,
			changeYear: !0,
			suppressDatePicker: !1,
			beforeShowDay: function(date) {
        var day = date.getDay();
        return [(day == 6)];
    	},
			onClose: function() {
				a.focus();
				var b = this;
				this.suppressDatePicker = !0, setTimeout(function() {
					b.suppressDatePicker = !1
				}, 200)
			},
			beforeShow: function(a, b) {
				return !this.suppressDatePicker
			}
		};
		}
		else if (a.attr('id')=='input_8_4' || a.attr('id')=='input_5_5') {
			var	c = {
			yearRange: "-0:+2",
			showOn: "focus",
			dateFormat: "mm/dd/yy",
			minDate: 0,
			changeMonth: !0,
			changeYear: !0,
			suppressDatePicker: !1,
			onClose: function() {
				a.focus();
				var b = this;
				this.suppressDatePicker = !0, setTimeout(function() {
					b.suppressDatePicker = !1
				}, 200)
			},
			beforeShow: function(a, b) {
				return !this.suppressDatePicker
			}
		};
		}
		else{
			var	c = {
				yearRange: "-100:+100",
				showOn: "focus",
				dateFormat: "mm/dd/yy",
				changeMonth: !0,
				changeYear: !0,
				suppressDatePicker: !1,
				onClose: function() {
					a.focus();
					var b = this;
					this.suppressDatePicker = !0, setTimeout(function() {
						b.suppressDatePicker = !1
					}, 200)
				},
				beforeShow: function(a, b) {
					return !this.suppressDatePicker
				}
			};
		}
		a.hasClass("dmy") ? c.dateFormat = "dd/mm/yy" : a.hasClass("dmy_dash") ? c.dateFormat = "dd-mm-yy" : a.hasClass("dmy_dot") ? c.dateFormat = "dd.mm.yy" : a.hasClass("ymd_slash") ? c.dateFormat = "yy/mm/dd" : a.hasClass("ymd_dash") ? c.dateFormat = "yy-mm-dd" : a.hasClass("ymd_dot") && (c.dateFormat = "yy.mm.dd"), a.hasClass("datepicker_with_icon") && (c.showOn = "both", c.buttonImage = jQuery("#gforms_calendar_icon_" + b).val(), c.buttonImageOnly = !0), b = b.split("_"), c = gform.applyFilters("gform_datepicker_options_pre_init", c, b[1], b[2]), a.datepicker(c)
	})
}
jQuery(document).ready(gformInitDatepicker);
(function($){

	var _rating={
		stars:$("#actStars"),
		star:$("#actStars .fa"),		 

		init:function(){			
			this.star.on("mouseover",function(){
				var currentStar = $(this);
				var prevStars = $(this).prevAll();
				var nextStars = $(this).nextAll();				
				$.each(prevStars, function(){
					if(!$(this).hasClass('lock'))
						$(this).removeClass('fa-star-o').addClass('fa-star');
				})
				currentStar.removeClass('fa-star-o').addClass('fa-star');
				$.each(nextStars, function(){
					if(!$(this).hasClass('lock'))
						$(this).removeClass('fa-star').addClass('fa-star-o');
				})
			})
			this.star.on("mouseout", function(){
				$.each($("#actStars .fa"), function(){
					if(!$(this).hasClass('lock'))
						$(this).removeClass('fa-star').addClass('fa-star-o');					 
				})
			});		
		},

		setRating:function(){
			$("#actStars .fa").on("click", function(){
				var nextStars = $(this).nextAll();	
				$("#input_10_3").val(parseInt($(this).data('rait')))
				$(".rating-message span b").html(parseInt($(this).data('rait')));
				$.each($("#actStars .fa-star"), function(){
					$(this).addClass('lock');					 
				})
				$.each(nextStars, function(){					
						$(this).removeClass('fa-star').removeClass('lock').addClass('fa-star-o');
				})
			})
		}

	}
	_rating.init();
	_rating.setRating();

})(jQuery);

(function($){
	$.unserialize = function(serializedString){
		var str = decodeURI(serializedString);
		var pairs = str.split('&');
		var obj = {}, p, idx, val;
		for (var i=0, n=pairs.length; i < n; i++) {
			p = pairs[i].split('=');
			idx = p[0];

			if (idx.indexOf("[]") == (idx.length - 2)) {
				// Eh um vetor
				var ind = idx.substring(0, idx.length-2)
				if (obj[ind] === undefined) {
					obj[ind] = [];
				}
				obj[ind].push(p[1]);
			}
			else {
				obj[idx] = p[1];
			}
		}
		return obj;
	};
})(jQuery);
(function($){
	jQuery(".vc_grid-container").ajaxComplete(function(event,xhr,settings){
		if($.unserialize(settings.data).action=='vc_get_vc_grid_data'){
			jQuery(".vc_grid-container").find(".vc_gitem-link.prettyphoto").click(function(){
				$(this).data("slug")
				console.log('vc_gitem-link prettyphoto ' , $(document).find(".pp_details .pp_nav"));
				$(document).find(".pp_details .pp_nav").after('<a href="'+$(this).data("slug")+'" style="float:left;" class="send-report">Report Content</a>')
			});
		}
	});
})(jQuery)
 