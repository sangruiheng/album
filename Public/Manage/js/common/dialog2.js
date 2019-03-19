;(function($, window, document, undefined){
	
	// 定义Dialog的构造函数
	var Dialog = function(ele, opt){
		this.$element = ele,
		this.defaults = {
			'color' : '#000',
			'fontSize' : '12px',
		},
		this.options = $.extend({}, this.defaults, opt)
	}
	
	//定义Dialog的方法
	Dialog.prototype = {
		//显示加载层
		showLoading : function(){
			$("body").html("123");
		}
	}
	
	//在插件中使用Dialog对象
	$.fn.myDialog = function(options){
		//创建Dialog对象实体
		var dialog = new Dialog(this, options);
		//调用某个方法
		return dialog.showLoading();
	}
})(jQuery, window, document);