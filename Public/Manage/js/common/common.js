$(document).ready(function(e) {
	//ajax fadein submit请求 渐隐渐现
    $('.ajax-fadein').submit(function(){
    	var self = $(this);
    	// console.log(self);
    	// return false;
    	$.post(self.attr("action"), self.serialize(), success);
    	return false;
    	function success(ret){
    		if (ret.status == 1) {
    			updateAlert(ret.info + ' 页面即将自动跳转~','alert-success');
                setTimeout(function(){
                	location.href= ret.url;
                },1500);
            }else{
            	updateAlert(ret.info);
                setTimeout(function(){
                    $('#top-alert').find('button').click();
                },1500);
            }
    	}
    });

    //ajax alert submit请求 弹出框提示
    $('.ajax-alert').submit(function(){
    	var self = $(this);
    	$.post(self.attr("action"), self.serialize(), success);
    	return false;
    	function success(ret){
    		if (ret.status == 1) {
    			$.showLoading('正在提交……');
    			setTimeout(function(){
    				$.closeLoading(function(){
    					location.href= ret.url;
    				});
                },1500);
            }else{
            	$.show({
        			title : '提示',
        			isConfirm: false,
        			content : ret.info
        		});
            }
    	}
    });
	
	//给排序单元格添加功能按钮
	$('td.sortTD').each(function(index, element) {
        var sortHtml = $(this).html();
		$(this).html('<i class="fa fa-arrow-circle-up faSort" data-sort="'+sortHtml+'" aria-hidden="true" title="升序"></i> '+sortHtml+' <i class="fa fa-arrow-circle-down faSort" data-sort="'+sortHtml+'" aria-hidden="true" title="降序"></i>');
    });
	//升降序操作
	$('i.faSort').click(function(){
		var _this = $(this);
		if($(this).hasClass('fa-arrow-circle-up')){
			var action = 'dataAsc';
		}else{
			var action = 'dataDesc';
		}
		var table  = $(_this).parent('td.sortTD').attr('name');
		var dataID = $(_this).parents('tr').find('input#del_listID').val();
		var dataID  = dataID;
		if (typeof(table) != "undefined") {
			$.post(APP+'/Common/dataSort',{table : table, dataID : dataID, action : action},function(){
				reload();
			});
		}
	});
	//添加数据页面取消按钮
	$("button#cancelButton").click(function(){
		window.history.back(-1);
	});
	
	
});

//打开添加数据页面
function openAddData(src){
	if (typeof(src) != "undefined") {
		window.location.href = APP+'/'+CONTROLLER_NAME+'/'+src;
	}else{
		$.show({
			title : '提示',
			isConfirm: false,
			content : '未检测到指定页面'
		});
	}
}


//列表页面点击修改按钮
function editData(src){
	var checkBox = $("input[name=del_listID]:checked");
	var checkBoxVal = checkBox.val();
	if (checkBox.length == 1) {
		window.location.href = APP+'/'+CONTROLLER_NAME+'/'+src+'/id/'+checkBoxVal;
	}else if(checkBox.length > 1){
		$.show({
			title : '提示',
			isConfirm: false,
			content : '只能同时编辑一条数据'
		});
	}else{
		$.show({
			title : '提示',
			isConfirm: false,
			content : '请至少选中一条数据'
		});
	}
	
}
//编辑数据页面加载数据
function getEditData(callback){
	var table = $('form.addForm').attr('action');
	table = table.split('/');
	table = table[table.length-1];
	var editID = $("input#id").val();
	if(editID == '')return false;
	$.showLoading('正在加载……');
	$("#changeTitle").html('编辑');
	$("#saveButton").html('<i class="fa fa-floppy-o"></i> 保存');
	var where = 'id = '+editID;
	$.post(APP+'/Common/getEditData',{table:table,where:where},function(ret){
		if(parseInt(ret.code) == 200){
			var jdata = ret.data[0];
			$("form.addForm input[type!=checkbox],textarea,select").each(function(index, element) {
				var thisIdName = $(this).attr("id");
				$("#"+thisIdName).val(eval("jdata."+thisIdName));
			});
			$.closeLoading();
			if(typeof callback === "function"){
				callback(jdata);
				$.closeLoading();
			}
		}else{
			$.show({
				title : '提示',
				isConfirm: false,
				content : ret.msg
			});
		}
	});
}





//编辑群页面加载数据
function getEditProductData(callback){
    var table = $('form.addForm').attr('action');
    table = table.split('/');
    table = table[table.length-1];
    var editID = $("input#id").val();
    if(editID == '')return false;
    $.showLoading('正在加载……');
    $("#changeTitle").html('编辑');
    $("#saveButton").html('<i class="fa fa-floppy-o"></i> 保存');
    var where = 'id = '+editID;
    $.post(APP+'/Product/getEditProductData',{table:table,where:where},function(ret){
        if(parseInt(ret.code) == 200){
            var jdata = ret.data;
            // console.log(jdata);
            $("form.addForm input[type!=checkbox],textarea,select").each(function(index, element) {
                var thisIdName = $(this).attr("id");
                $("#"+thisIdName).val(eval("jdata."+thisIdName));
            });
            $.closeLoading();
            if(typeof callback === "function"){
                callback(jdata);
                $.closeLoading();
            }
        }else{
            $.show({
                title : '提示',
                isConfirm: false,
                content : ret.msg
            });
        }
    });
}




//编辑数据页面加载数据
function getEditAttrData(callback){
    var table = $('form.addForm').attr('action');
    table = table.split('/');
    table = table[table.length-1];
    var editID = $("input#id").val();
    if(editID == '')return false;
    $.showLoading('正在加载……');
    $("#changeTitle").html('编辑');
    $("#saveButton").html('<i class="fa fa-floppy-o"></i> 保存');
    var where = 'id = '+editID;
    $.post(APP+'/Common/getEditAttrData',{table:table,where:where},function(ret){
        if(parseInt(ret.code) == 200){
            var jdata = ret.data;
            $("form.addForm input[type!=checkbox],textarea,select").each(function(index, element) {
                var thisIdName = $(this).attr("id");
                $("#"+thisIdName).val(eval("jdata."+thisIdName));
            });
            $.closeLoading();
            if(typeof callback === "function"){
                callback(jdata);
                $.closeLoading();
            }
        }else{
            $.show({
                title : '提示',
                isConfirm: false,
                content : ret.msg
            });
        }
    });
}




//编辑轮播图加载数据
function getEditBannerData(callback){
    var table = $('form.addForm').attr('action');
    table = table.split('/');
    table = table[table.length-1];
    var editID = $("input#id").val();
    if(editID == '')return false;
    $.showLoading('正在加载……');
    $("#changeTitle").html('编辑');
    $("#saveButton").html('<i class="fa fa-floppy-o"></i> 保存');
    var where = 'id = '+editID;
    $.post(APP+'/Common/getEditBannerData',{table:table,where:where},function(ret){
        if(parseInt(ret.code) == 200){
            var jdata = ret.data;
            // console.log(jdata);
            $("form.addForm input[type!=checkbox],textarea,select").each(function(index, element) {
                var thisIdName = $(this).attr("id");
                $("#"+thisIdName).val(eval("jdata."+thisIdName));
            });
            $.closeLoading();
            if(typeof callback === "function"){
                callback(jdata);
                $.closeLoading();
            }
        }else{
            $.show({
                title : '提示',
                isConfirm: false,
                content : ret.msg
            });
        }
    });
}

//列表页面点击删除按钮
function deleteData(table){
	var delID = '';
	$("input[name=del_listID]:checked").each(function() {
		delID += $(this).val() + ",";
	});
	//用户群组里的超级管理员不能删只能修改
	if(table == 'Group' && delID.indexOf(AUTH_GROUP_ID) != -1){
		$.show({
			title : '提示',
			isConfirm: false,
			content : '超级管理员群组不能删除如有改动请修改！'
		});
		return false;
	}
	//后台管理员里的admin用户不能删除只能修改
	if(table == 'Adminuser' && delID.indexOf(AUTH_USER_ID) != -1){
		$.show({
			title : '提示',
			isConfirm: false,
			content : '后台超级管理员不能删除如有改动请修改！'
		});
		return false;
	}
	if(delID.length <= 0){
		$.show({
			title : '提示',
			isConfirm: false,
			content : '请选择要删除的数据！'
		});
	}else{
		$.showAsk({
			title : '删除数据',
			type : 'warning',
			content : '确定要删除吗？',
			callback : function(){
				$.post(APP+'/Common/deleteData',{delID : delID,table : table},function(){
					reload();
				});
			}
		});
	}
}



//删除新闻
function deleteNews(table){
    var delID = '';
    $("input[name=del_listID]:checked").each(function() {
        delID += $(this).val() + ",";
    });
    if(delID.length <= 0){
        $.show({
            title : '提示',
            isConfirm: false,
            content : '请选择要删除的数据！'
        });
    }else{
        $.showAsk({
            title : '删除数据',
            type : 'warning',
            content : '确定要删除吗？',
            callback : function(){
                $.post(APP+'/News/deleteNewsImg',{delID : delID,table : table},function(){
                    window.location.href = APP+'/'+CONTROLLER_NAME+'/'+ACTION_NAME;
                });
            }
        });
    }
}



//删除商品
function deleteProduct(table){
    var delID = '';
    $("input[name=del_listID]:checked").each(function() {
        delID += $(this).val() + ",";
    });
    if(delID.length <= 0){
        $.show({
            title : '提示',
            isConfirm: false,
            content : '请选择要删除的数据！'
        });
    }else{
        $.showAsk({
            title : '删除数据',
            type : 'warning',
            content : '确定要删除吗？',
            callback : function(){
                $.post(APP+'/Product/deleteProduct',{delID : delID,table : table},function(){
                    window.location.href = APP+'/'+CONTROLLER_NAME+'/'+ACTION_NAME;
                });
            }
        });
    }
}



//刷新当前页面 但是不能跳转页面
function reload(){
	if(PAGE != '' && KEYWORD != ''){
		location.href = APP+'/'+CONTROLLER_NAME+'/'+ACTION_NAME+'/p/'+PAGE+'/keyWord/'+KEYWORD;
	}else if(PAGE != ''){
		location.href = APP+'/'+CONTROLLER_NAME+'/'+ACTION_NAME+'/p/'+PAGE;
	}else if(KEYWORD != ''){
		location.href = APP+'/'+CONTROLLER_NAME+'/'+ACTION_NAME+'/keyWord/'+KEYWORD;
	}else{
		window.location.href = APP+'/'+CONTROLLER_NAME+'/'+ACTION_NAME;
	}
}



/* 上传图片预览弹出层 */
$(function(){
    $(window).resize(function(){
        var winW = $(window).width();
        var winH = $(window).height();
        $(".add-box").on('click','.upload-img-box',function(){
        	//如果没有图片则不显示
        	if($(this).find('img').attr('src') === undefined){
        		return false;
        	}
            // 创建弹出框以及获取弹出图片
            var imgPopup = "<div id=\"uploadPop\" class=\"upload-img-popup\"></div>"
            var imgItem = $(this).find(".upload-pre-item").html();

            //如果弹出层存在，则不能再弹出
            var popupLen = $(".upload-img-popup").length;
            if( popupLen < 1 ) {
                $(imgPopup).appendTo("body");
                $(".upload-img-popup").html(imgItem + "<a class=\"close-pop\" href=\"javascript:;\" title=\"关闭\"></a>");
            }
            // 弹出层定位
            var uploadImg = $("#uploadPop").find("img");
            var popW = uploadImg.width();
            var popH = uploadImg.height();
            var left = (winW -popW)/2;
            var top = (winH - popH)/2 + 50;
            $(".upload-img-popup").css({
                "max-width" : winW * 0.9,
                "left": left,
                "top": top
            });
        });

        // 关闭弹出层
        $("body").on("click", "#uploadPop .close-pop", function(){
            $(this).parent().remove();
        });
    }).resize();

    // 缩放图片
    function resizeImg(node,isSmall){
        if(!isSmall){
            $(node).height($(node).height()*1.2);
        } else {
            $(node).height($(node).height()*0.8);
        }
    }
    /**顶部警告栏*/
	var content = $('#add-box');
	var top_alert = $('#top-alert');
	top_alert.find('.close').on('click', function () {
		top_alert.removeClass('block').slideUp(200);
	});

    window.updateAlert = function (text,c) {
		text = text||'default';
		c = c||false;
		if ( text!='default' ) {
            top_alert.find('.alert-content').text(text);
			if (top_alert.hasClass('block')) {
			} else {
				top_alert.addClass('block').slideDown(200);
			}
		} else {
			if (top_alert.hasClass('block')) {
				top_alert.removeClass('block').slideUp(200);
			}
		}
		if ( c!=false ) {
            top_alert.removeClass('alert-error alert-warn alert-info alert-success').addClass(c);
		}
	};
});
//获取子模块信息
$(document).ready(function(e) {
	$('a.com-TopMenu').click(function(){
		var thisID = $(this).attr('data-id');
		$.post(APP+'/Common/getModuleList',{crm_parents:thisID},function(data){
			var thisHTML = '';
			if(data.length > 0){
				for(var i=0; i<data.length; i++){
					thisHTML += '<li><i class="fa fa-'+data[i].moduleIcon+'"></i> <a href="'+APP+data[i].moduleLink+'" target="right">'+data[i].moduleName+'</a></li>';
				}
				$('ul.com-leftMenu').html(thisHTML);
				var iframeSrc = $('#icpnt_iframe').attr('src',data[0].moduleLink);
				window.right.location = APP+data[0].moduleLink;
			}else{
				$('ul.com-leftMenu').html('');
			}
			$(".com-leftBox").animate({width : "230px"});
			$(".com-rightBox").animate({marginLeft : "230px"});
			$(".com-hideIcon").css({"transform" : "rotate(90deg)", "top" : "15px"});
			$(".com-hideIcon").attr("title","隐藏左侧菜单");
			$(".com-hideIcon").attr("data-state","open");
		});
	});
	var bodyHeight = $(window).height() - 110;
    $(".com-leftBox").height(bodyHeight);
	$(".com-rightBox").height(bodyHeight);
	//左侧菜单动画
	$(".com-hideIcon").click(function(){
		var state = $(this).attr("data-state");
		if(state == 'hide'){
			$(".com-leftBox").animate({width : "230px"});
			$(".com-rightBox").animate({marginLeft : "230px"});
			$(this).css({"transform" : "rotate(90deg)", "top" : "15px"});
			$(this).attr("title","隐藏左侧菜单");
			$(this).attr("data-state","open");
		}else{
			$(".com-leftBox").animate({width : "0px"});
			$(".com-rightBox").animate({marginLeft : "0px"});
			$(this).css({"transform" : "rotate(0deg)", "top" : "17px"});
			$(this).attr("title","展开左侧菜单");
			$(this).attr("data-state","hide");
		}
	});
	
	//权限点击事件
	$('button.listButton').each(function(index,element){
		var thisID = $(this).attr('data-id');
		if(thisID == AUTH_ADD_ID){
			if(GROUPID.indexOf(AUTH_ADD_ID) == -1){
				$(this).remove();
			}
		}else if(thisID == AUTH_SAVE_ID){
			if(GROUPID.indexOf(AUTH_SAVE_ID) == -1){
				$(this).remove();
			}
		}else if(thisID == AUTH_DEL_ID){
			if(GROUPID.indexOf(AUTH_DEL_ID) == -1){
				$(this).remove();
			}
		}
	});
	//用户退出登录
	$(".topLogOut").click(function(){
		$.showAsk({
			type : 'warning',
			title : '退出系统',
			content : '确定要退出系统吗？',
			callback : function(){
				$.post(APP+'/Common/loginOut','',function(json){
					window.location.href = APP+'/Login/index.html';
				});
			}
		});
	});
	
	$('.topSystem').click(function(){
		var iframeSrc = $('#icpnt_iframe').attr('src','/System/adminuserList');
		window.right.location = APP+'/System/adminuserList';
	});
});
