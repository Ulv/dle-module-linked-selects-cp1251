<?php
/**
* выбор специальность/регион/ВУЗ (связанные списки, ajax)
*
* модуль для dle 9.6
*
* author: Vladimir Chmil <ulv8888@gmail.com>
* link:   https://github.com/Ulv/dle-module-linked-selects.git 
*/
?>

<?php /* js */ ?>
<script type="text/javascript">
/*!
 * jQuery Selectbox plugin 0.2
 *
 * originally developed by Dimitar Ivanov (http://www.bulgaria-web-developers.com/projects/javascript/selectbox/)
 *
 * modified by Vladimir Chmil <ulv8888@gmail.com>
 */
(function ($, undefined) {
	var PROP_NAME = 'selectbox',
		FALSE = false,
		TRUE = true;

	/**
	 * Selectbox manager.
	 * Use the singleton instance of this class, $.selectbox, to interact with the select box.
	 * Settings for (groups of) select boxes are maintained in an instance object,
	 * allowing multiple different settings on the same page
	 */
	function Selectbox() {
		this._state = [];
		this._defaults = { // Global defaults for all the select box instances
			classHolder: "sbHolder",
			classHolderDisabled: "sbHolderDisabled",
			classSelector: "sbSelector",
			classOptions: "sbOptions",
			classGroup: "sbGroup",
			classSub: "sbSub",
			classDisabled: "sbDisabled",
			classToggleOpen: "sbToggleOpen",
			classToggle: "sbToggle",
			classFocus: "sbFocus",
			speed: 200,
			effect: "slide", // "slide" or "fade"
			onChange: function() {
            }, //Define a callback function when the selectbox is changed
			onOpen: null, //Define a callback function when the selectbox is open
            onClose: null, //Define a callback function when the selectbox is closed
            vuzlinks: false,
            links: null
		};
	}
	
	$.extend(Selectbox.prototype, {
		/**
		 * Is the first field in a jQuery collection open as a selectbox
		 * 
		 * @param {Object} target
		 * @return {Boolean}
		 */
		_isOpenSelectbox: function (target) {
			if (!target) {
				return FALSE;
			}
			var inst = this._getInst(target);
			return inst.isOpen;
		},
		/**
		 * Is the first field in a jQuery collection disabled as a selectbox
		 * 
		 * @param {HTMLElement} target
		 * @return {Boolean}
		 */
		_isDisabledSelectbox: function (target) {
			if (!target) {
				return FALSE;
			}
			var inst = this._getInst(target);
			return inst.isDisabled;
		},
		/**
		 * Attach the select box to a jQuery selection.
		 * 
		 * @param {HTMLElement} target
		 * @param {Object} settings
		 */
		_attachSelectbox: function (target, settings) {
			if (this._getInst(target)) {
				return FALSE;
			}
			var $target = $(target),
				self = this,
				inst = self._newInst($target),
				sbHolder, sbSelector, sbToggle, sbOptions,
				s = FALSE, optGroup = $target.find("optgroup"), opts = $target.find("option"), olen = opts.length,
                placeholderText = $(target).attr("data-placeholder");

               // console.log(placeholderText);
				
			$target.attr("sb", inst.uid);
				
			$.extend(inst.settings, self._defaults, settings);
			self._state[inst.uid] = FALSE;
			$target.hide();
			
			function closeOthers() {
				var key, sel,
					uid = this.attr("id").split("_")[1];
				for (key in self._state) {
					if (key !== uid) {
						if (self._state.hasOwnProperty(key)) {
							sel = $("select[sb='" + key + "']")[0];

							if (sel) {
								self._closeSelectbox(sel);
							}
						}
					}
				}
			}
			
			sbHolder = $("<div>", {
				"id": "sbHolder_" + inst.uid,
				"class": inst.settings.classHolder,
				"tabindex": $target.attr("tabindex")
			});
			
			sbSelector = $("<a>", {
				"id": "sbSelector_" + inst.uid,
				"href": "#",
				"class": inst.settings.classSelector,
				"click": function (e) {
					e.preventDefault();
					closeOthers.apply($(this), []);
					var uid = $(this).attr("id").split("_")[1];
					if (self._state[uid]) {
						self._closeSelectbox(target);
					} else {
						self._openSelectbox(target);
					}
                    $(".sbHolder").removeClass("sel_active");
                    $(this).parent().addClass("sel_active");
                    //console.log($(this).parent().siblings());
				}
			});
			
			sbToggle = $("<a>", {
				"id": "sbToggle_" + inst.uid,
				"href": "#",
				"class": inst.settings.classToggle,
				"click": function (e) {
					e.preventDefault();
					closeOthers.apply($(this), []);
					var uid = $(this).attr("id").split("_")[1];
					if (self._state[uid]) {
						self._closeSelectbox(target);
					} else {
						self._openSelectbox(target);
					}
				}
			});
			sbToggle.appendTo(sbHolder);

			sbOptions = $("<ul>", {
				"id": "sbOptions_" + inst.uid,
				"class": inst.settings.classOptions,
				"css": {
					"display": "none"
				}
			});
			
			$target.children().each(function(i) {
				var that = $(this), li, config = {};
				if (that.is("option")) {
					getOptions(that);
				} else if (that.is("optgroup")) {
					li = $("<li>");
					$("<span>", {
						"text": that.attr("label")
					}).addClass(inst.settings.classGroup).appendTo(li);
					li.appendTo(sbOptions);
					if (that.is(":disabled")) {
						config.disabled = true;
					}
					config.sub = true;
					getOptions(that.find("option"), config);
				}
			});
			
			function getOptions () {
				var sub = arguments[1] && arguments[1].sub ? true : false,
					disabled = arguments[1] && arguments[1].disabled ? true : false;
				arguments[0].each(function (i) {
					var that = $(this),
						li = $("<li>"),
						child;
					if (that.is(":selected")) {
						sbSelector.text(that.text());
						s = TRUE;
					}
					if (i === olen - 1) {
						li.addClass("last");
                    }

                    // ссылка на вуз
                    var lnk;
                    if (inst.settings.vuzlinks == false) {
                        lnk = '#'+that.val();
                    } else {
                        if (inst.settings.links !== null) {
                            lnk = inst.settings.links[that.val()-1]; 
                        }
                    }

                    if (!that.is(":disabled") && !disabled) {
						child = $("<a>", {
							"href": lnk,
							"rel": that.val()
						}).text(that.text()).bind("click.sb", function (e) {
							if (e && e.preventDefault) {
								e.preventDefault();
							}
							var t = sbToggle,
							 	$this = $(this),
								uid = t.attr("id").split("_")[1];
							self._changeSelectbox(target, $this.attr("rel"), $this.text());
							self._closeSelectbox(target);
						}).bind("mouseover.sb", function () {
							var $this = $(this);
							$this.parent().siblings().find("a").removeClass(inst.settings.classFocus);
							$this.addClass(inst.settings.classFocus);
						}).bind("mouseout.sb", function () {
							$(this).removeClass(inst.settings.classFocus);
						});
						if (sub) {
							child.addClass(inst.settings.classSub);
						}
						if (that.is(":selected")) {
							child.addClass(inst.settings.classFocus);
						}
						child.appendTo(li);
					} else {

						child = $("<span>", {
							"text": that.text()
						}).addClass(inst.settings.classDisabled);
						if (sub) {
							child.addClass(inst.settings.classSub);
						}
						child.appendTo(li);
					}
					li.appendTo(sbOptions);
				});

			}
			sbSelector.text(placeholderText);
			/*if (!s) {
				sbSelector.text(placeholderText);
			}*/

			$.data(target, PROP_NAME, inst);
			
			sbHolder.data("uid", inst.uid).bind("keydown.sb", function (e) {
				var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0,
					$this = $(this),
					uid = $this.data("uid"),
					inst = $this.siblings("select[sb='"+uid+"']").data(PROP_NAME),
					trgt = $this.siblings(["select[sb='", uid, "']"].join("")).get(0),
					$f = $this.find("ul").find("a." + inst.settings.classFocus);
				switch (key) {
					case 37: //Arrow Left
					case 38: //Arrow Up
						if ($f.length > 0) {
							var $next;
							$("a", $this).removeClass(inst.settings.classFocus);
							$next = $f.parent().prevAll("li:has(a)").eq(0).find("a");
							if ($next.length > 0) {
								$next.addClass(inst.settings.classFocus).focus();
								$("#sbSelector_" + uid).text($next.text());
							}
						}
						break;
					case 39: //Arrow Right
					case 40: //Arrow Down
						var $next;
						$("a", $this).removeClass(inst.settings.classFocus);
						if ($f.length > 0) {
							$next = $f.parent().nextAll("li:has(a)").eq(0).find("a");
						} else {
							$next = $this.find("ul").find("a").eq(0);
						}
						if ($next.length > 0) {
							$next.addClass(inst.settings.classFocus).focus();
							$("#sbSelector_" + uid).text($next.text());
						}
						break;				
					case 13: //Enter
						if ($f.length > 0) {
							self._changeSelectbox(trgt, $f.attr("rel"), $f.text());
						}
						self._closeSelectbox(trgt);
						break;
					case 9: //Tab
						if (trgt) {
							var inst = self._getInst(trgt);
							if (inst/* && inst.isOpen*/) {
								if ($f.length > 0) {
									self._changeSelectbox(trgt, $f.attr("rel"), $f.text());
								}
								self._closeSelectbox(trgt);
							}
						}
						var i = parseInt($this.attr("tabindex"), 10);
						if (!e.shiftKey) {
							i++;
						} else {
							i--;
						}
						$("*[tabindex='" + i + "']").focus();
						break;
					case 27: //Escape
						self._closeSelectbox(trgt);
						break;
				}
				e.stopPropagation();
				return false;
			}).delegate("a", "mouseover", function (e) {
				$(this).addClass(inst.settings.classFocus);
			}).delegate("a", "mouseout", function (e) {
				$(this).removeClass(inst.settings.classFocus);	
			});
			
			sbSelector.appendTo(sbHolder);
			sbOptions.appendTo(sbHolder);			
			sbHolder.insertAfter($target);
			
			$("html").live('mousedown', function(e) {
				e.stopPropagation();          
				$("select").selectbox('close'); 
                $(".sbHolder").removeClass("sel_active");
			});
			$([".", inst.settings.classHolder, ", .", inst.settings.classSelector].join("")).mousedown(function(e) {    
				e.stopPropagation();
			});
		},
		/**
		 * Remove the selectbox functionality completely. This will return the element back to its pre-init state.
		 * 
		 * @param {HTMLElement} target
		 */
		_detachSelectbox: function (target) {
			var inst = this._getInst(target);
			if (!inst) {
				return FALSE;
			}
			$("#sbHolder_" + inst.uid).remove();
			$.data(target, PROP_NAME, null);
			$(target).show();			
		},
		/**
		 * Change selected attribute of the selectbox.
		 * 
		 * @param {HTMLElement} target
		 * @param {String} value
		 * @param {String} text
		 */
		_changeSelectbox: function (target, value, text) {
			var onChange,
				inst = this._getInst(target);
			if (inst) {
				onChange = this._get(inst, 'onChange');
				$("#sbSelector_" + inst.uid).text(text);
			}
			value = value.replace(/\'/g, "\\'");
			$(target).find("option[value='" + value + "']").attr("selected", TRUE);
			if (inst && onChange) {
				onChange.apply((inst.input ? inst.input[0] : null), [value, inst]);
			} else if (inst && inst.input) {
				inst.input.trigger('change');
			}
		},
		/**
		 * Enable the selectbox.
		 * 
		 * @param {HTMLElement} target
		 */
		_enableSelectbox: function (target) {
			var inst = this._getInst(target);
			if (!inst || !inst.isDisabled) {
				return FALSE;
			}
			$("#sbHolder_" + inst.uid).removeClass(inst.settings.classHolderDisabled);
			inst.isDisabled = FALSE;
			$.data(target, PROP_NAME, inst);
		},
		/**
		 * Disable the selectbox.
		 * 
		 * @param {HTMLElement} target
		 */
		_disableSelectbox: function (target) {
			var inst = this._getInst(target);
			if (!inst || inst.isDisabled) {
				return FALSE;
			}
			$("#sbHolder_" + inst.uid).addClass(inst.settings.classHolderDisabled);
			inst.isDisabled = TRUE;
			$.data(target, PROP_NAME, inst);
		},
		/**
		 * Get or set any selectbox option. If no value is specified, will act as a getter.
		 * 
		 * @param {HTMLElement} target
		 * @param {String} name
		 * @param {Object} value
		 */
		_optionSelectbox: function (target, name, value) {
			var inst = this._getInst(target);
			if (!inst) {
				return FALSE;
			}
			//TODO check name
			inst[name] = value;
			$.data(target, PROP_NAME, inst);
		},
		/**
		 * Call up attached selectbox
		 * 
		 * @param {HTMLElement} target
		 */
		_openSelectbox: function (target) {
			var inst = this._getInst(target);
			//if (!inst || this._state[inst.uid] || inst.isDisabled) {
			if (!inst || inst.isOpen || inst.isDisabled) {
				return;
			}
			var	el = $("#sbOptions_" + inst.uid),
				viewportHeight = parseInt($(window).height(), 10),
				offset = $("#sbHolder_" + inst.uid).offset(),
				scrollTop = $(window).scrollTop(),
				height = el.prev().height(),
				diff = viewportHeight - (offset.top - scrollTop) - height / 2,
				onOpen = this._get(inst, 'onOpen');
			el.css({
				"top": height + "px",
				"maxHeight": (diff - height) + "px"
			});
			inst.settings.effect === "fade" ? el.fadeIn(inst.settings.speed) : el.slideDown(inst.settings.speed);
			$("#sbToggle_" + inst.uid).addClass(inst.settings.classToggleOpen);
			this._state[inst.uid] = TRUE;
			inst.isOpen = TRUE;
			if (onOpen) {
				onOpen.apply((inst.input ? inst.input[0] : null), [inst]);
			}
			$.data(target, PROP_NAME, inst);
		},
		/**
		 * Close opened selectbox
		 * 
		 * @param {HTMLElement} target
		 */
		_closeSelectbox: function (target) {
			var inst = this._getInst(target);
			//if (!inst || !this._state[inst.uid]) {
			if (!inst || !inst.isOpen) {
				return;
			}
			var onClose = this._get(inst, 'onClose');
			inst.settings.effect === "fade" ? $("#sbOptions_" + inst.uid).fadeOut(inst.settings.speed) : $("#sbOptions_" + inst.uid).slideUp(inst.settings.speed);
			$("#sbToggle_" + inst.uid).removeClass(inst.settings.classToggleOpen);
			this._state[inst.uid] = FALSE;
			inst.isOpen = FALSE;
			if (onClose) {
				onClose.apply((inst.input ? inst.input[0] : null), [inst]);
			}
			$.data(target, PROP_NAME, inst);
		},
		/**
		 * Create a new instance object
		 * 
		 * @param {HTMLElement} target
		 * @return {Object}
		 */
		_newInst: function(target) {
			var id = target[0].id.replace(/([^A-Za-z0-9_-])/g, '\\\\$1');
			return {
				id: id, 
				input: target, 
				uid: Math.floor(Math.random() * 99999999),
				isOpen: FALSE,
				isDisabled: FALSE,
				settings: {}
			}; 
		},
		/**
		 * Retrieve the instance data for the target control.
		 * 
		 * @param {HTMLElement} target
		 * @return {Object} - the associated instance data
		 * @throws error if a jQuery problem getting data
		 */
		_getInst: function(target) {
			try {
				return $.data(target, PROP_NAME);
			}
			catch (err) {
				throw 'Missing instance data for this selectbox';
			}
		},
		/**
		 * Get a setting value, defaulting if necessary
		 * 
		 * @param {Object} inst
		 * @param {String} name
		 * @return {Mixed}
		 */
		_get: function(inst, name) {
			return inst.settings[name] !== undefined ? inst.settings[name] : this._defaults[name];
		}
	});

	/**
	 * Invoke the selectbox functionality.
	 * 
	 * @param {Object|String} options
	 * @return {Object}
	 */
	$.fn.selectbox = function (options) {
		
		var otherArgs = Array.prototype.slice.call(arguments, 1);
		if (typeof options == 'string' && options == 'isDisabled') {
			return $.selectbox['_' + options + 'Selectbox'].apply($.selectbox, [this[0]].concat(otherArgs));
		}
		
		if (options == 'option' && arguments.length == 2 && typeof arguments[1] == 'string') {
			return $.selectbox['_' + options + 'Selectbox'].apply($.selectbox, [this[0]].concat(otherArgs));
		}
		
		return this.each(function() {
			typeof options == 'string' ?
				$.selectbox['_' + options + 'Selectbox'].apply($.selectbox, [this].concat(otherArgs)) :
				$.selectbox._attachSelectbox(this, options);
		});
	};
	
	$.selectbox = new Selectbox(); // singleton instance
	$.selectbox.version = "0.2";
})(jQuery);


// education
(function($) {
    // ajax обработчик
     var Education = (function ($) {

        var list_spec,
            list_region,
            list_vuz,
            specid, 
            $links;

		var trans = [];
		var snart = [];
		for (var i = 0x410; i <= 0x44F; i++) {
		    trans[i] = i - 0x350;
		    snart[i - 0x350] = i;
		}
		trans[0x401] = 0xA8;
		trans[0x451] = 0xB8;
		snart[0xA8] = 0x401;
		snart[0xB8] = 0x451;
		window.urlencode = function (str) {
		    var ret = [];
		    for (var i = 0; i < str.length; i++) {
		        var n = str.charCodeAt(i);
		        if (typeof trans[n] != 'undefined') n = trans[n];
		        if (n <= 0xFF) ret.push(n);
		    }

		    return window.escape(String.fromCharCode.apply(null, ret));
		}
		window.urldecode = function (str) {
		    var ret = [];
		    str = unescape(str);
		    for (var i = 0; i < str.length; i++) {
		        var n = str.charCodeAt(i);
		        if (typeof snart[n] != 'undefined') n = snart[n];
		        ret.push(n);
		    }

		    return String.fromCharCode.apply(null, ret);
		}

        function chSelect(lst, url) {
            $.getJSON('engine/ajax/education.php?' + url, function(data) {
                if (data.status == 'ok') {
                    lst.empty();
                    $.each(data.data, function(index, value) {
                        lst.append('<option value="'+value.id+'">'+window.urldecode(value.title.replace(/\+/g, ' '))+'</option>');
                        //lst.append('<option value="'+value.id+'">'+decodeURIComponent(value.title)+'</option>');
                    });


                } else {
                    alert('ошибка работы с бд!');
                }
            }).complete(function(){
                list_region.selectbox("detach");
                list_region.selectbox({ 
                    onChange: function() {
                        if (undefined !== specid) {
                            chSelect(list_vuz, 'specid=' + specid + '&regid=' + $(this).val(), true);
                            
                        }
                   }
                });


                list_vuz.selectbox("detach");
                list_vuz.selectbox({
                    onChange: function(v, i) {
                        document.location = $links[v-1];
                    },
                    vuzlinks: true,
                    links: $links
                });
            });
        }

        // ссылки на вузы
        function getLinks() {
            $links = [];
            $.ajax({
                    async: true,
                    type: "GET",
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    url: "engine/ajax/education.php",
                    success: function(jsonData) {
                        if (jsonData.status == 'ok') {
                            $.each(jsonData.data, function(index, value) {
                                $links.push(value.link);
                            });
                        }
                    }
            });

        }

        function init () {

            list_spec   = $("#education_spec"),
            list_region = $("#education_region"),
            list_vuz    = $("#education_vuz"),
            specid=0;

            getLinks();

            list_spec.selectbox({
                onChange: function() {
                    list_vuz.empty();
                    list_vuz.append('<option value="0">--- сначала выберите специальность и регион ---</option>');
                    specid = $(this).val();
                    chSelect(list_region, 'specid=' + specid);

                }
            });

            list_region.selectbox();
            list_vuz.selectbox();

        }

        return {
            init: init
        }
    })($);

    $(document).ready(function (){
        window.education = Education.init();
    });
})($);
</script>

<?php /* some styling */ ?>

<style type="text/css">
    #education_wrapper { 
        width: 100%;
        padding: 10px;
    }
    #education_wrapper label {
        min-width: 120px; 
        display: block;
        float: left;
        font-weight: 600;
    }
    #education_wrapper select {}

    .sbHolder{

        -webkit-appearance: none;
        -moz-appearance: none;
        -o-appearance: none;

        position: relative;
        height: 20px; 
        width: 400px;
        margin-right: 23px;
        margin-left: 122px;
        
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px; 

        background-image: linear-gradient(top, rgb(245,241,245) 15%, rgb(214,214,214) 97%, rgb(189,190,189) 79%);
        background-image: -o-linear-gradient(top, rgb(245,241,245) 15%, rgb(214,214,214) 97%, rgb(189,190,189) 79%);
        background-image: -moz-linear-gradient(top, rgb(245,241,245) 15%, rgb(214,214,214) 97%, rgb(189,190,189) 79%);
        background-image: -webkit-linear-gradient(top, rgb(245,241,245) 15%, rgb(214,214,214) 97%, rgb(189,190,189) 79%);
        background-image: -ms-linear-gradient(top, rgb(245,241,245) 15%, rgb(214,214,214) 97%, rgb(189,190,189) 79%);

        background-image: -webkit-gradient(
            linear,
            left top,
            left bottom,
            color-stop(0.15, rgb(245,241,245)),
            color-stop(0.97, rgb(214,214,214)),
            color-stop(0.79, rgb(189,190,189))
        );
        border-top: 1px solid #7e807e;
        border-right: 1px solid #404140;
        border-bottom: 1px solid #111111;
        border-left: 1px solid #202220;

    }
    .sbSelector{
        text-decoration: none !important;

        color: #2f2f2f !important; 
        /*width: 300px;*/
    }

    .sel_active {
        background-color: #fff;
        background-image: none;
    }
    .sbSelector{
        display: block;
        height: 20px;
        left: 0;
        line-height: 20px;
        outline: none;
        overflow: hidden;
        position: absolute;
        text-indent: 10px;
        top: 0;
        width: 370px;

        /*padding: 8px 0 0 3px;*/
        padding: 0;

    }
    .sbSelector:link, .sbSelector:visited, .sbSelector:hover{
        color: #2c2c2c;
        outline: none;
        text-decoration: none;

        /*font: 600 14px Arial;*/
        text-shadow: 0px 1px 1px #fff;

    }
    .sbToggle{
        /*background: url(../img/select-icons.png) 0 -116px no-repeat;*/
        background: transparent url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABsAAAAhCAYAAAAoNdCeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAUlJREFUeNrslU9qg0AUxp+Ohfhn1BrPUOgdcopqaqC5yBCq6MqDpJAu0l2h0GUPUehBUqGO9o2B7IzaiHQxn3wMM/MePwa/UQUAbuu6/oQJpKI5TCQVJpSESZiESdjfpXUVbB43HzgsRmC9dsJ8338jKlloRAMFn6HCPwqUVQm84u+i+wYXvs41bJ+2KS95zH9409xXiqIAuSJANMLWD+u8F0xo97xLi6KID98HqKqqOwyqCoZuwGw2Y6tolQ8KCDYkuq5n6OZ0nPNWC4k69Ak0OI3RfZRYppWhW4FCpmEC1jCszy+K/jJcJpTSzKb28eWX5clC1KKA+wzr8lHuWRiEiW3bmeu4zVycSITBsR3AdYb7+aiXWgBd183m3hwIIeBde4DzVlCTzr5pbNP+ZZ/iEKNZcBfkZ6/CpTD5IZYwCft/sMmAvwIMAAfek8QXvDSmAAAAAElFTkSuQmCC) 0 -7px no-repeat;

        display: block;
        height: 20px;
        outline: none;
        position: absolute;
        right: 2px;
        top: 0;
        width: 30px;
    }
    .sbToggle:hover{
    }
    .sbToggleOpen{
    }
    .sbToggleOpen:hover{
    }
    /*.sbHolder a { text-decoration: none; }*/
    .sbHolderDisabled{
    /*	background-color: #3C3C3C;
        border: solid 1px #515151;*/
    }
    .sbHolderDisabled .sbHolder{

    }
    .sbHolderDisabled .sbToggle{

    }
    .sbOptions{
        background-color: #fff;
        border: solid 1px #999999;
        list-style: none;
        left: -1px;
        max-height: 200px;
        margin: 5px 0 0 0 !important;
        padding: 0 !important;
        position: absolute;
        top: 16px !important;
        width: 400px;
        z-index: 1;
        overflow-y: auto;

        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px; 

    }
    .sbOptions li{
        padding: 0 7px;
        background-image: none !important;
    }
    .sbOptions a{
        /*font: 14px Arial;*/
        display: block;
        outline: none;
        padding: 3px 0 3px 3px;

        color: #2f2f2f !important; 
    }
    .sbOptions a:link, .sbOptions a:visited{
        text-decoration: none;
    }
    .sbOptions a:hover { text-decoration: underline}
    .sbOptions a,
    .sbOptions a.sbFocus{
        /*color: #EBB52D;*/
        color: #2f2f2f; 
    }
    .sbOptions li.last a{
        border-bottom: none;
    }
    .sbOptions .sbDisabled{
        /*border-bottom: dotted 1px #515151;
        color: #999;*/
        display: block;
        padding: 7px 0 7px 3px;
    }
    .sbOptions .sbGroup{
        /*border-bottom: dotted 1px #515151;
        color: #EBB52D;*/
        display: block;
        font-weight: bold;
        padding: 7px 0 7px 3px;
    }
    .sbOptions .sbSub{
        padding-left: 17px;
    }


    .dwrap { float: left; }
    .dwrap:nth-child(1) { margin-left:30px; }

</style>

<?php
$lang = array(
    "education_spec"   => "Специальность",
    "education_region" => "Регион",
    "education_vuz"    => "ВУЗ"
);

if(!defined('DATALIFEENGINE'))
{
  	die("Hacking attempt!");
}

include ('engine/api/api.class.php');

?>
<div id="education_wrapper">
    <label for="education_spec"><?=$lang["education_spec"]; ?></label>
    <select class="education" id="education_spec" name="education_spec">
        <?php $sql = $db->query("select * from spec"); while ($row = $db->get_row($sql)): ?>
        <option value="<?=$row['id'];?>"><?=$row['title'];?></option>
        <?php endwhile; ?>
    </select>

    <br />
    <label for="education_region"><?=$lang["education_region"];?></label>
    <select class="education" id="education_region" name="education_region">
        <option value="0">--- выберите регион ---</option>
    </select>

    <br />
    <label for="education_vuz"><?=$lang["education_vuz"];?></label>
    <select class="education" id="education_vuz" name="education_vuz">
        <option value="0">--- сначала выберите специальность и регион ---</option>
    </select>
</div>
<?php $db->free(); ?>
