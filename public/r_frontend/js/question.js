(function (doc, win) {
    var docEl = doc.documentElement,
            resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
            recalc = function () {
                var clientWidth = docEl.clientWidth;
                if (!clientWidth) return;
                if(clientWidth>750){
                	clientWidth = 750;
                	 docEl.style.fontSize = 50 + 'px';
                	 return;
                }
                docEl.style.fontSize = 100 * (clientWidth / 750) + 'px';
            };
    if (!doc.addEventListener) return;
    win.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);
    document.getElementsByTagName('body')[0].style.maxWidth = '750px';
    document.getElementsByTagName('body')[0].style.margin = '0 auto';
})(document, window);

var Mask = function() {
	this.btn = ["取消", "确定"],
	this.open = function(html){
		$("body").append(html);
		$('input').blur();
		$("html,body").css("overflow", "hidden");
	},
	this.close = function() {
		$(".mask").off().remove();
		$("html,body").css("overflow", "initial");
	}
};
Mask.prototype.alert = function(msg, time, callback) {
	if($('#mask').length){return;}
	var _this = this;
	var timer = null;
	var html = '<div id="mask" class="mask"><div class="mask-bg"></div><div class="mask-container">' + msg + '</div></div>'
	_this.open(html);
	$(".mask").click(function(ev) {
		clearTimeout(timer);
		_this.close();
	});
	$(".mask-container").click(function(ev) {
		ev.stopPropagation()
	});
	if(time && time > 0) {
		timer = setTimeout(function() {
			_this.close();
			callback && callback();
			clearTimeout(timer);
		}, time * 1000);
	}
};
Mask.prototype.confirm = function(options) {
	var _this = this;
	_this.defaults = {
		msg: '',
		btn: ['取消','确定'],
		leftF: null,
		rightF: null,
	};
	_this.options = $.extend({}, _this.defaults, options);

	var html = '<div class="mask mask-confirm"><div class="mask-bg"></div><div class="mask-container"><p>' + _this.options.msg + '</p><div class="mask-btn-box"><button class="mask-btn mask-btn-left">' + _this.options.btn[0] + '</button><button class="mask-btn mask-btn-right">' + _this.options.btn[1] + '</button></div></div></div>';
	_this.open(html);
	$(".mask-btn-left").click(function() {
		_this.close();
		_this.options.leftF && _this.options.leftF();
	});
	$(".mask-btn-right").click(function() {
		_this.close();
		_this.options.rightF && _this.options.rightF();
	});
};
var Mask = new Mask();



function Check(options){
	this.defaults = {
		ele : '',
		notCheckedClass: '',
		checkedClass: '',
		type: 0,
		callback : null
	};
	this.options = $.extend({}, this.defaults, options);
	this.con = $(this.options.ele);

	if(this.options.type == 0){
		this.single();
	}
}

Check.prototype = {
	single: function(){
		var _this = this;
		this.inputs = this.con.find('input');
		this.btn = this.con.find('.btn-select');
		
		var c = _this.options.checkedClass;
		var f = _this.options.callback;
		
		var result;
		this.inputs.click(function(){
			$(this).prev().addClass('active');
			$(this).parent().siblings().find('.btn-select').removeClass('active');
			var index = $(this).parent().index();
			result = {
				target: $(this),
				val: $(this).val(),
				index: index
			}
			if(f){f(result);}
		});
		return result;
	}
}

function Upload(options){
	this.defaults = {
		type: 'img',
		ele: null,
		num: 5,
		maxSize: false,
		img: '',
		success: null,
		deleted: null,
		example: false
	};
	this.options = $.extend({}, this.defaults, options);
	this.container = $(this.options.ele);
	if(this.options.type === 'img'){this.img();}
}
Upload.prototype = {
	img: function(){
		var _this = this;
		var maxSize = _this.options.maxSize;
		
		_this.photos_add = _this.container.find('.photos-add');
		_this.btn_upload = _this.container.find('.btn-upload'),
		
		_this.btn_upload.click(function(){
			$(this).parent().find('input').click();
		});
		_this.photos_add.find('input[type="file"]').off().on("change",function(e){
			var file = $(this)[0].files[0],reader = new FileReader();
			
			if(!/\/(png|jpg|jpeg|bmp|PNG|JPG|JPEG|BMP)$/.test(file.type)){
				Mask.alert('图片支持jpg, jpeg, png或bmp格式',2);
				return false;
			}
			
			if(maxSize){
				if(file.size>maxSize*1024*1024){
					Mask.alert('单个文件大小必须小于等于'+ maxSize +'MB',2)
					return false;
				}
			}
			
		    reader.readAsDataURL(file);
		    reader.onload = function(e){
                var data = e.target.result;
                $.ajax({
                    type:'post',
                    url: "/question/uploadImage",
                    data: {
                        "url": data
                    },
                    success: function(data) {
                        var value = data;
                        var html = "<div class='photos-item' style='background-image:url("+ value +")'><input hidden type='text' value='"+ value +"'/></div>";
                        $(html).insertBefore(_this.photos_add);
                        if(_this.options.successed)_this.options.successed();
                        canAddPhoto();
                    },
                    error: function() {
                        Mask.alert("网络请求错误!");
                    }
                });
			}
		});
		
		$('body').on('click','.photos-item',function(){
			var index = $(this).index();
			initSwiper(index);
		});
		
		
		function initSwiper(num){
	  		var imgArr = [];
	  		$('.photos-item').each(function(){
	  			var img = $(this).css('background-image');
	  			var reg = 
	  			imgArr.push(img);
	  		});
	  		var html = '<div class="pop-preview"><div class="bg"></div><div class="pop-content"><i class="btn-delete">删除</i><div class="swiper-container"><div class="swiper-wrapper">';
	  		for(var i=0,len=imgArr.length;i<len;i++){
	  			var value = imgArr[i].slice(4,-1);
	  			console.log(value)
	  			html += "<div class='swiper-slide' style='background-image:url("+ value +")'></div>";
	  		}
			html += '</div></div></div></div>'
			$('body').append(html);
			
			_this.preview = $('.pop-preview');
			_this.c = _this.preview.find('.swiper-container');
			_this.b = _this.preview.find('.bg');
			_this.d = _this.preview.find('.btn-delete');
			
			var mySwiper = new Swiper(_this.c,{
				observer: true,
				onSlideChangeStart: function(swiper){
			    	swiper.activeIndex === 0 ? _this.d.hide() : _this.d.show();
			    }
			});
			mySwiper.slideTo(num,0);
			
			// 关闭预览
			_this.b.on('click',function(){
				$('.pop-preview').remove();
			});
			// 删除照片
			_this.d.on('click',function(){
				var index = mySwiper.activeIndex;
				_this.container.find('.photos-item').eq(index).remove();
				_this.preview.find('.swiper-slide').eq(index).remove();
				var num = _this.container.find('.photos-item').length;
				if(_this.options.example && num == 1){
					$(this).hide();
				}else{
					$(this).show();
				}
				
				if(!num){
					$('.pop-preview').remove();
				}
				
				if(_this.options.deleted)_this.options.deleted();
				canAddPhoto(num);
			});
	  	}
		// 是否可以继续拍照上传
		function canAddPhoto(num){
		    var num = num || _this.container.find('.photos-item').length;
			if(num >= _this.options.num){
	    		_this.photos_add.hide();
	    	}else{
	    		_this.photos_add.show();
	    	}
		}
	}
}
var Util = (function(){
	var funObj = {
		initArea: function(options){
			var _this = this;
			this.defaults = {
				callback: null
			};
			this.options = $.extend({}, this.defaults, options);
			var option1 = '';
			for(obj in areaJson){
				var country = areaJson[obj].n;
				option1 += '<option data-id="'+ obj +'" value="'+ country +'">'+ country +'</option>';
			}
			$('.level1').append(option1);
			
			$('body').on('change','.level1',function(){
				var cid = $(this).find('option:selected').data('id');
				var data = areaJson[cid];
				var level2 = $(this).parent().find('.level2');
				funObj.areaChange(level2,data);
				
				var level3 = $(this).parent().find('.level3');
				var pid = level2.find('option:selected').data('id');
				var data = areaJson[cid][pid];
				funObj.areaChange(level3,data);
				if(_this.options.callback){_this.options.callback();}
			});
			$('body').on('change','.level2',function(){
				var level1 = $(this).parent().find('.level1');
				var level3 = $(this).parent().find('.level3');
				var cid = level1.find('option:selected').data('id');
				var pid = $(this).find('option:selected').data('id');
				var data = areaJson[cid][pid];
				funObj.areaChange(level3,data);
			});
			// 默认所在地区为中国
			$('.level1').trigger('change');
			
		},
		areaChange: function(ele,data){
			var html = '';
			for(obj in data){
				var name = data[obj].n;
				if(name){
					html += '<option data-id="'+ obj +'" value="'+ name +'">'+ name +'</option>';
				}else if(name == ''){
					html += '<option data-id="'+ obj +'" value="--">--</option>';
				}
			}
			ele.html(html);
		},
		isPC: function(){
			var userAgentInfo = navigator.userAgent;
		    var Agents = ["Android", "iPhone","SymbianOS", "Windows Phone","iPad", "iPod"];
		    var flag = true;
		    for (var v = 0; v < Agents.length; v++) {
		        if (userAgentInfo.indexOf(Agents[v]) > 0) {
		            flag = false;
		            break;
		        }
		    }
		    return flag;
		},
		GetQueryString: function(name){
		    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
		    var r = window.location.search.substr(1).match(reg);
		    if(r!=null)return  unescape(r[2]); return null;
		}
	}
	return {
		initArea: funObj.initArea,
		isPC: funObj.isPC,
		GetQueryString: funObj.GetQueryString
	}
})();

var verify = {
	isNum: function(ele){
		if(!ele.val()){return;}
		if(!/^[\d|.]*$/.test(ele.val())){
			Mask.alert('请输入数字且不得为负数',2);
			ele.val('').focus();
			return false;
		}
		return true;
	},
	rangeNum: function(min,ele,max){
		var min = parseFloat(min);
		var num = parseFloat(ele.val());
		var max = parseFloat(max);
		if(min>=num){
			Mask.alert('请输入大于'+ min +'的数字',2);
			ele.val('').focus();
			return false;
		}
		if(num>max){
			Mask.alert('请输入小于'+ max +'的数字',2);
			ele.val('').focus();
			return false;
		}
		return true;
	},
	isUrl: function(ele){
		if(!ele.val()){return;}
		if(!/[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/.test(ele.val())){
			Mask.alert('请输入正确格式的网址',2);
			ele.val('').focus();
			return false;
		}
		return true;
	},
	isPhone: function(ele){
		if(!ele.val()){return;}
		if(!/^1[34578]\d{9}$/.test(ele.val())){
			Mask.alert('请输入正确格式的手机号',2);
			ele.val('').focus();
			return false;
		}
		return true;
	}
}
// 返回上一步
$('.content').on('click','.btn-last',function(){
	if($(this).parents('.question').length){
		var page = $('.question .page:visible').index();
		if(!page){
			$('#step2').show();
			$('#step3').hide();
		}else{
			$('.question .page:visible').hide().prev().show();
			var section = $('.page:visible li').not('.blur');
			section.each(function(index){
				if(!$(this).find('.active').length){
					$('#step3 .btn-next').prop('disabled',true);
					return false;
				}
				if(index == section.length-1){
					$('#step3 .btn-next').prop('disabled',false);
				}
			});
		}
	}else{
		$(this).parents('.content').hide().prev().show();
	}
});

$('body').on('input propertychange','input[type="number"]', function() {
	// 限制maxlength
	var max = $(this).data('range').split(',')[1].length;
	if($(this).val().trim().length>max){
		var val = $(this).val().substring(0,max+3);
		$(this).val(val);
    }
	
	var reg = new RegExp('^\\d{'+ (max+1) +'}$')
//	if(/^\d{4}$/.test($(this).val())){
	if(reg.test($(this).val())){
		var val = $(this).val().substring(0,max);
		$(this).val(val);
	}
	// 只能输入两个小数
	var value = $(this).val();
	value = value.replace(/[^\d.]/g,"");
	value = value.replace(/^\./g,"");
	value = value.replace(/\.{2,}/g,".");
	value = value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
	value = value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); 
	$(this).val(value);
});

var step1 = {
	Ele: {
		btn_next: $('#step1 .btn-next'),
		upload_img: $('.upload-img'),
		tel: $('#tel')
	},
	init: function(callback){
		var _this = this;
		// 公司经营起始时间
		if(Util.isPC()){
			var mySchedule = new Schedule({
				ele: '#date',
				clickCb: function (val) {
					_this.isDisabled();
				}
			});
		}else{
			var calendar = new datePicker();
			calendar.init({
				'trigger': '#date',
				'type': 'date',
			});
		}
		$('.num').on('blur', function(){
			if(!verify.isNum($(this))){
				_this.Ele.btn_next.prop('disabled',true);
			}
		});
		
		$('.url').on('blur', function(){
			if(!verify.isUrl($(this))){
				_this.Ele.btn_next.prop('disabled',true);
			}
		});
		
		$('.tel').on('blur',function(){
			if(!verify.isPhone($(this))){
				_this.Ele.btn_next.prop('disabled',true);
			}
		});
		
		// 是否有股票在公开市场上发行
		var isPublish;
		new Check({
			ele: '.publish',
			checkedClass: 'active',
			callback: function(e){
				var index = e.index;
				isPublish = index;
				index == 0 ? $('.ipo2').show() : $('.ipo2').hide();
				_this.isDisabled();
			}
		});
		// 公司营业性质
		new Check({
			ele: '.quality',
			checkedClass: 'active',
			callback: function(e){
				var index = e.index;
				// 营业性质——IPO
				if(index == 2){
					$('.ipo1').show();
					isPublish == 0 && $('.ipo2').show();
				}else{
					$('.ipo1').hide();
				}
				// 营业性质——其他
				if(index == 3){
					$('.ipo1,.ipo2').hide();
					$('.other').show();
				}else{
					$('.other').hide();
				}
				_this.isDisabled();
			}
		});
		$('#country').change(function(){
			_this.changeCountry(this);
		});
		_this.Ele.btn_next.on('click',function(){
			callback();
		});
		_this.isDisabled();
	},
	changeCountry: function(ele){
		var _this = this;
		var id = $(ele).find('option:selected').data('id');
		if(id == 2){
			_this.Ele.upload_img.show();
			new Upload({
				ele: '.upload-img',
				num: 1,
				successed: function(){
					_this.isDisabled();
					$('.photos-item input').attr("name",'transact_image');
				},
				deleted: function(){
					_this.isDisabled();
				}
			});
		}else{
			_this.Ele.upload_img.hide();
		}
		_this.isDisabled();
	},
	checkInput: function(){
		var _this = this;
		var status = false;
		$('input:visible').each(function(index){
	    	if(!$(this).val().trim()){
	    		status = true;
	    		return false;
	    	}
	   });
	   return status;
	},
	checkRadio: function(){
		var _this = this;
		var status = false;
		$('.select-group:visible').each(function(index){
	    	if(!$(this).find('.active').length){
	    		status = true;
	    		return false;
	    	}
	   });
	   return status;
	},
	checkImg: function(){
		var status = false;
		if($('.upload-img:visible').length){
			var len = $('.upload-img:visible .photos-item').length;
			if(len == 0){
				status = true;
			}
		}
		return status;
	},
	isDisabled: function(){
		var _this = this;
		var status = _this.checkInput()||_this.checkRadio()|| _this.checkImg();
		_this.Ele.btn_next.prop('disabled',status)
		$('input:visible').bind('input propertychange', function() {
			var status = _this.checkInput()||_this.checkRadio()|| _this.checkImg();
			_this.Ele.btn_next.prop('disabled',status)
		});
	},
	success: function(call){
		call();
	}
}

var step2 = {
	Ele: {
		btn_next: $('#step2 .btn-next'),
		btn_add: $('#add'),
	},
	init: function(callback){
		var _this = this;
		this.addCompany();
		this.isDisabled();
		Util.initArea();
		
		$('body').on('change','select',function(){
			_this.isDisabled();
		});
		
		$('.content').on('click','.icon-delete',function(){
			$(this).parents('.section').remove();
			_this.isDisabled();
		});
		
		$('body').on('blur','.num',function(){
			if(!verify.isNum($(this))){
				_this.Ele.btn_next.prop('disabled',true);
				_this.Ele.btn_add.prop('disabled',true);
			}
		});
		
		$('body').on('blur','.range',function(){
			var data = $(this).data('range').split(',');
			var min = data[0];
			var max = data[1];
			if(!verify.rangeNum(min,$(this),max)){
				_this.Ele.btn_next.prop('disabled',true);
				_this.Ele.btn_add.prop('disabled',true);
			}
		});
		
		_this.Ele.btn_next.on('click',function(){
			callback();
		});
	},
	addCompany: function(){
		var _this = this;
		var html = $('.section').prop("outerHTML");
		
		_this.Ele.btn_add.on('click',function(){
			$(html).insertBefore($(this));
			$('.section:not(":first") .icon-delete').show();
			_this.Ele.btn_next.prop('disabled',true);
			_this.isDisabled();
			Util.initArea();
		});
	},
	checkInput: function(){
		var _this = this;
		var status = false;
		$('input:visible').each(function(index){
	    	if(!$(this).val().trim()){
	    		status = true;
	    		return false;
	    	}
	   });
	   return status;
	},
	checkSelect: function(){
		var _this = this;
		var status = false;
		$('.p_num,.level1').each(function(index){
			if(!$(this).val()){
	    		status = true;
	    		return false;
	    	}
		});
		return status;
	},
	isDisabled: function(){
		var _this = this;
		var status = _this.checkInput() || _this.checkSelect();
		_this.Ele.btn_next.prop('disabled',status);
		_this.Ele.btn_add.prop('disabled',status);
		$('input:visible').bind('input propertychange', function() {
			var status = _this.checkInput() || _this.checkSelect();
			_this.Ele.btn_next.prop('disabled',status);
			_this.Ele.btn_add.prop('disabled',status);
		});
	}
}

var step3 = {
	Ele: {
		disable: $('.disable'),
		btn_next: $('#step3 .btn-next'),
		not_have: $('#step3 .not-have'),
		page: $('.page'),
		blur: $('.blur')
	},
	init: function(successCallback){
		var _this = this;
		new Check({
			ele: '.select-group',
			checkedClass: 'active',
			callback: function(e){
				var id = e.target.parents('li').data('id'); // 当前题号
				var index = e.val; // 0:选是  1：选否
				// 当前题号为19题时
				if(id == 19){
					if(index == 0){
						_this.Ele.disable.show().parent().find('.active').removeClass('active');
						_this.Ele.btn_next.prop('disabled',false);
						_this.Ele.not_have.hide();
						_this.Ele.blur.addClass('blur');
						return;
					}else{
						_this.Ele.disable.hide();
						_this.Ele.btn_next.prop('disabled',true);
						_this.Ele.not_have.show();
						_this.Ele.blur.removeClass('blur');
					}
				}
				var groups = $('.page:visible .select-group');
				groups.each(function(index){
					if(!$(this).find('.active').length){
						return false;
					}
					if(index == groups.length-1){
						_this.Ele.btn_next.prop('disabled',false);
					}
				});
			}
		});
		_this.Ele.not_have.on('click',function(){
			var groups = $('.page:visible .select-group');
			groups.each(function(index){
				$(this).find('label').eq(1).find('input').trigger('click');
			});
		});
		
		$('#step3').on('click','.btn-next',function(){
			_this.getPage() == 3 ? successCallback() : _this.checkRadio();
		})
	},
	getPage: function(){
		var page = $('.form-wrapper .page:visible').index()+1;
		return page;
	},
	checkRadio: function(){
		var _this = this;
		var groups = $('.page:visible .select-group');
		var page = _this.getPage(); // 当前页
		page==3 ? location.href = 'question_step4.html' : _this.nextPage(page+1);
	},
	nextPage: function(page){
		var _this = this;
		$('.page:visible').hide().next().show();
		var section = $('.page:visible li').not('.blur');
		section.each(function(index){
			if(!$(this).find('.active').length){
				_this.Ele.btn_next.prop('disabled',true);
				return false;
			}
			if(index == section.length-1){
				_this.Ele.btn_next.prop('disabled',false);
			}
		});
		
		$('body,html').animate({scrollTop: 0}, 0);
		if(page == 3){
			_this.Ele.not_have.hide();
		}
	}
}


var step4 = {
	init: function(callback){
		var _this = this;
		$('#refuse').click(function(){
			Mask.confirm({
				msg: '你的问卷结果不符合投保标准，您不能购买保险',
				btn:['重新测评','取消'],
				leftF: function(){
					$('#step3').show();
					$('#step4').hide();
					$('#step3 .page').eq(0).show().siblings('.page').hide();
					$('body,html').animate({scrollTop: 0}, 0);
					$('#step3 .btn-select').removeClass('active');
					$('#step3 input').prop('checked',false);
					$('#step3 .btn-next').prop('disabled',true);
					$('#step3 .not-have').show();
					$('#step3 .disable').show().parent().addClass('blur');
				},
				rightF: function(){
					Mask.close();
				}
			});
		});
		
		$('#agree').on('click',function(){
			callback();
		});
	}
}