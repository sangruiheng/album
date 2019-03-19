// JavaScript Document
$.extend({
	//加载状态提示
	showLoading : function(title){
		var loadingHtml = '';
		loadingHtml += '<div class="loadingMask"></div>';
		loadingHtml += '<div class="loadingBox">';
		loadingHtml += '<img style="float:left;" src="'+PUBLIC+'/js/tools/icpntDialog/images/loading.gif" width="37" height="37">';
		loadingHtml += '<span style="margin-left:10px;">'+title+'</span>';
		loadingHtml += '</div>';
		$("body",window.parent.document).append(loadingHtml);
	},

	//关闭加载状态
	closeLoading : function(callback){
		$('div.loadingMask,div.loadingBox',window.parent.document).fadeOut(100,function(){
			$(this).remove();
			if(typeof callback === "function"){
				callback();
			}
		});
	},

	//登录信息提示
	loginTip : function(msg){
		alert(msg);
	},

	//询问弹窗
	/***************************************************
	title : 标题
	type : 图标类型，共有error、success、warning 3种类型
	content : 内容
	callback: 回调函数
	***************************************************/
	showAsk : function(opt){
		var _this = this;
		var askHtml = '';
		askHtml += '<div class="askMask"></div>';
        askHtml += '<div class="askMaskTop">';
        askHtml += '<div class="askMaskTitle"><i class="fa fa-info-circle"></i> '+opt.title+'<i class="fa fa-times-circle askClose askCancel"></i></div>';
        askHtml += '<div class="askBox">';
		askHtml += '<div class="askIconDiv"><img src="'+PUBLIC+'/js/tools/icpntDialog/images/'+opt.type+'.png"/></div>';
		askHtml += '<div class="askContent">'+opt.content+'</div></div>';
		askHtml += '<div class="askButton">';
		askHtml += '<button style="margin-right:15px;" type="button" class="btn btn-success askConfirm"><i class="fa fa-check"></i> 确定</button>';
		askHtml += '<button type="button" class="btn btn-default askCancel"><i class="fa fa-times"></i> 取消</button>';
		askHtml += '</div>';
		$("body",window.parent.document).append(askHtml);
		//确定按钮
		$(".askConfirm",window.parent.document).click(function(){
			$(".askMask,.askMaskTop",window.parent.document).fadeOut(function() {
				$(this).remove();
				opt.callback();
			});

		});
		//取消和关闭按钮
		$(".askCancel",window.parent.document).click(function(){
			$(".askMask,.askMaskTop",window.parent.document).fadeOut(function() {
				$(this).remove();
			});
		});
	},

	//基于bootstrap的封装弹窗
	/***************************************************
	title : 标题
	content : 内容
	isConfirm：是否有确定按钮，true or false
	complete: 窗体打开完成后回调函数
	callback：点击确认按钮回调函数
	closeCallback:点击关闭按钮回调函数
	***************************************************/
	show: function(opt) {
			var html = '';
			html += '<div class="modal fade icpntDialog_showDiv" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
			if (typeof(opt.width) == 'undefined') {
					html += '<div class="modal-dialog">';
			} else {
					html += '<div class="modal-dialog" style="width:' + opt.width + 'px;">';
			}
			html += '<div class="modal-content">';
			html += '<div class="modal-header">';
			html += '<button type="button" class="close icpntDialog_close">×</button>';
			html += '<h4 class="modal-title" id="myModalLabel">' + opt.title + '</h4>';
			html += '</div>';
			html += '<div class="modal-body">';
			html += '<div class="icpntDialog_content" style="max-height: 305px;overflow-y: auto;">' + opt.content + '</div>';
			html += '</div>';
			html += '<div class="modal-footer">';
			if (opt.isConfirm) {
					html += '<button type="button" class="btn btn-default icpntDialog_close">取消</button>';
					html += '<button type="button" class="btn btn-primary icpntDialog_confirm">确定</button>';
			} else {
					html += '<button type="button" class="btn btn-default icpntDialog_close">关闭</button>';
			}
			html += '</div>';
			html += '</div>';
			html += '</div>';
			html += '</div>';
			$("body", window.parent.document).append(html);
			$('div#myModal', window.parent.document).modal({
					keyboard: false
			});
			if (typeof(opt.complete) === "function") {
					opt.complete();
			}
			//点击确定按钮操作
			$('button.icpntDialog_confirm', window.parent.document).click(function() {
					$.closeShow(function() {
							opt.callback();
					});
			});
			//点击关闭/取消按钮操作
			$('button.icpntDialog_close', window.parent.document).click(function() {
					$.closeShow(function() {
							if (typeof(opt.closeCallback) === "function") {
									opt.closeCallback();
							}
					});
			});
	},

	//基于bootstrap的封装带iframe的弹窗
	/***************************************************
	title : 标题
	src : 相对路径地址
	isConfirm：是否有确定按钮，true or false
	complete: 窗体打开完成后回调函数
	callback：点击确认按钮回调函数
	***************************************************/
	openWin : function(opt){
		//opt.isConfirm = typeof(opt.isConfirm == 'undefined') ? opt.isConfirm = true : opt.isConfirm = opt.isConfirm;
		var html = '';
		html += '<div class="modal fade icpntDialog_showDiv" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
		html += '<div class="modal-dialog" style="width:520px;">';
		html += '<div class="modal-content">';
		html += '<div class="modal-header">';
		html += '<button type="button" class="close icpntDialog_close">×</button>';
		html += '<h4 class="modal-title" id="myModalLabel">'+opt.title+'</h4>';
		html += '</div>';
		html += '<div class="modal-body">';
		html += '<div class="icpntDialog_content" style="height: '+opt.height+';overflow-y: auto;"><iframe name="dialog_content" id="dialoa_icpnt_iframe" frameborder="0" scrolling="auto" width="100%" height="100%" src="'+opt.src+'"></iframe></div>';
		html += '</div>';
		html += '<div class="modal-footer">';
		if(opt.isConfirm){
			//html += '<button type="button" class="btn btn-default icpntDialog_close">取消</button>';
			html += '<button type="button" class="btn btn-primary icpntDialog_confirm">确定</button>';
		}else{
			html += '<button type="button" class="btn btn-default icpntDialog_close">关闭</button>';
		}
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		$("body",window.parent.document).append(html);
		$('div#myModal',window.parent.document).modal({
			keyboard: false
		});
		if(typeof(opt.complete) === "function"){
			opt.complete();
		}
		//点击确定按钮操作
		$('button.icpntDialog_confirm',window.parent.document).click(function(){
			$.closeShow(function(){
				opt.callback();
			});
		});
		//点击关闭/取消按钮操作
		$('button.icpntDialog_close',window.parent.document).click(function(){
			$.closeShow(function(){
				//opt.closeback();
			});
		});
	},

	//基于bootstrap的封装弹窗input
	/***************************************************
	title : 标题
	content : 内容
	isConfirm：是否有确定按钮，true or false
	complete: 窗体打开完成后回调函数
	***************************************************/
	showInput : function(opt){
		//opt.isConfirm = typeof(opt.isConfirm == 'undefined') ? opt.isConfirm = true : opt.isConfirm = opt.isConfirm;
		var html = '';
		html += '<div class="modal fade icpntDialog_showDiv" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
		html += '<div class="modal-dialog">';
		html += '<div class="modal-content">';
		html += '<div class="modal-header">';
		html += '<button type="button" class="close icpntDialog_close">&times;</button>';
		html += '<h4 class="modal-title" id="myModalLabel">'+opt.title+'</h4>';
		html += '</div>';
		html += '<div class="modal-body">';
		html += '<div class="icpntDialog_content" style="max-height: 305px;overflow-y: auto;">'+opt.content+'</div>';
		html += '</div>';
		html += '<div class="modal-footer">';
		html += '<button type="button" class="btn btn-default icpntDialog_close">关闭</button>';
		if(opt.isConfirm || typeof(opt.isConfirm) == 'undefined'){
			html += '<button type="button" class="btn btn-primary icpntDialog_confirm">确定</button>';
		}
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		$('#myModal',window.parent.document).remove();
		$("body",window.parent.document).append(html);
		$('div#myModal',window.parent.document).modal({
			keyboard: false
		});
		if(typeof(opt.complete) === "function"){
			opt.complete();
		}
		//点击确定按钮操作
		$('button.icpntDialog_confirm',window.parent.document).click(function(){
			var textContent = $('#reason',window.parent.document).val();
			if(textContent == ''){
				alert('请输入要拉黑理由或关闭窗口');
				$('#reason',window.parent.document).focus();
				return false;
			}
			opt.callback(textContent);
			$.closeShow();
		});
		//关闭弹窗
		$('button.icpntDialog_close',window.parent.document).click(function(){
			$.closeShow();
		});
	},
	//关闭和删除bootstrap弹窗
	closeShow : function(){
		$('div#myModal',window.parent.document).modal('hide');
		//删除弹窗
		$('div#myModal',window.parent.document).on('hidden.bs.modal', function () {
		  	$('div.icpntDialog_showDiv',window.parent.document).remove();
		});
	}


})
