//Power luxin
//www.icpnt.com
var popWin = {
    scrolling: 'no',
	initData : '',
    //是否显示滚动条 no,yes,auto

int: function() {
        this.mouseClose();
        //this.closeMask();
		this.closeFadeOut();
        //this.mouseDown();

    },
easyWin : function(content){
	var easyHtml = ''
	easyHtml += '<div id="mask" style="width:100%; height:100%; position:fixed; top:0; left:0; background:#cccccc; filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity:0.5; z-index:1999;"></div>'
	easyHtml += '<div id="updateBox" style="width:800px; display:none; z-index:1999; opacity:0; height:600px; background:#fff; box-shadow: 1px 1px 6px #292828; border-radius:3px; position:absolute; top:50%; left:50%; margin-left:-400px; margin-top:-300px; font-size:12px; padding:10px;">'+content+'</div>';
	$("body",window.parent.document).append(easyHtml);
	popWin.easyAnimeat();
	//popWin.closeEasyWin();
	},	
easyAnimeat : function(){
	$("#updateBox",window.parent.document).show().animate({width : "400px" , height : "200px" , marginLeft: "-200px" , marginTop: "-100px" , opacity: 1});
	},	
closeEasyWin: function() {
        $("#closeUpdateWin",window.parent.document).on('click', 
        function() {
            $("#mask,#updateBox",window.parent.document).fadeOut(function() {
                $(this).remove();
            });

        });

    },	

//list loading function
loadingList : function(){
	
	var loadList = '';
	loadList += '<div id="loadingList" style="width:100px; height:100px; border-radius:8px; position:absolute; left:50%; margin-left:-50px; top:15%;"><i class="fa fa-spinner fa-spin" style="font-size:52px; color:#0683b1; margin:24px 0 0 24px;"></i></div>';
	$("body").append(loadList);
	
},	

closeLoadList : function(){
	$("#loadingList").fadeOut(function(){
		$(this).remove();
	});
},
	
//新增提交等待，不需要可删除
lodingWin : function(imgName,content,tip,path){
	if($("#lodingBox").attr("id") == 'lodingBox'){
		$("#lodingBox").remove();
	}
	var tipWidth;
	var lodingHtml = '';
	var suffix;
	var bgColorVal;
	var borderColor;
	switch (imgName){
		case 'loading':
		suffix = 'gif';
		break;
		case 'warning':
		bgColorVal = '#fcf8e3';
		borderColor = '#faebcc';
		suffix = 'png';
		break;
		case 'success':
		bgColorVal = '#dff0d8';
		borderColor = '#d6e9c6';
		suffix = 'png';
		break;
		case 'error':
		bgColorVal = '#f2dede';
		borderColor = '#ebccd1';
		suffix = 'png';
		break;
		default :
		suffix = 'png';
	}
	
	if(tip == 'tip'){
		lodingHtml += '<div id="lodingBox" style="width:auto; line-height:37px; padding: 10px 40px; font-family:Microsoft Yahei; font-size: 16px; font-weight: bolder; position:fixed; top:20%; left:50%; z-index:4999; margin-top:-18.5px;background:'+bgColorVal+';border: 1px solid '+borderColor+';"><img style="float:left;" src="/Public/CRM/images/'+imgName+'.'+suffix+'" width="37" height="37" /><span style="margin-left:10px;">'+content+'</span></div>';
	}else{
	if($("#lodingMask").attr("id") == 'lodingMask'){
		lodingHtml += '<div id="lodingBox" style="width:auto; height:37px; line-height:37px; font-family:Microsoft Yahei; font-size: 16px; font-weight: bolder; position:fixed; top:50%; left:50%; z-index:4999; margin-top:-19px;"><img style="float:left;" src="/Public/CRM/images/'+imgName+'.'+suffix+'" width="37" height="37" /><span style="margin-left:10px;">'+content+'</span></div>';
	}else{	
	lodingHtml += '<div id="lodingMask" style="width:100%; height:100%; position:fixed; top:0; left:0; z-index:3999;background:#cccccc; filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity:0.5;"></div>';
	lodingHtml += '<div id="lodingBox" style="line-height: 37px;font-size: 16px;font-weight: bolder;position: fixed;top: 50%;background: rgba(0, 0, 0, 0.58);padding: 30px 50px;left: 50%;color: #fff;border-radius: 8px;z-index: 4999;margin-top: -98px;"><img style="float:left;" src="/Public/CRM/images/'+imgName+'.'+suffix+'" width="37" height="37" /><span style="margin-left:10px;">'+content+'</span></div>';
	}
	}
	
	switch(path)
	{
	case 1://1表示在父级页面显示
	  $("body",window.parent.document).append(lodingHtml);
	  break;
	case 2://2表示在框架页内显示
	  $("body",window.parent.frames["popWinIframe"].document).append(lodingHtml);
	  tipWidth = $("#lodingBox",window.parent.frames["popWinIframe"].document).width();
	  tipWidth = tipWidth / 2;
	  $("#lodingBox",window.parent.frames["popWinIframe"].document).css("margin-left","-"+tipWidth+"px");
	  break;
	default://0表示在当前页面显示
	  $("body").append(lodingHtml);
	  tipWidth = $("#lodingBox").width() + 100;
	  tipWidth = tipWidth / 2;
	  $("#lodingBox").css("margin-left","-"+tipWidth+"px");
	}
	
	
	if(tip == 'tip'){
	$('body').oneTime('1s',popWin.closeLoadingWin);
	}
	
},	

closeLoadingWin : function(){
	$("div#lodingBox",window.parent.document).fadeOut(function(){
		$(this).remove();
	});
	$("div#lodingBox").fadeOut(function(){
		$(this).remove();
	});
},

showWin: function(width, height, title, src) {
        var iframeHeight = height - 42;
        var marginLeft = width / 2;
        var marginTop = height / 2;
        var inntHtml = '';
        inntHtml += '<div id="mask" style="width:100%; height:100%; position:fixed; top:0; left:0; background:#cccccc; filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity:0.5; z-index:1999;"></div>'
        inntHtml += '<div id="maskTop" style="width:50px; overflow:hidden; height:50px; background: #fff; color: #333; position: fixed; top: 50%; left: 200%; margin-left: -25px; margin-top: -25px; z-index: 2999; filter: progid:DXImageTransform.Microsoft.Shadow(color=#909090,direction=120,strength=4); -moz-box-shadow: 2px 2px 10px #909090; -webkit-box-shadow: 2px 2px 10px #909090; box-shadow: 2px 2px 10px #909090;">'
        inntHtml += '<div id="maskTitle" style="height: 40px; line-height: 40px; font-family: Microsoft Yahei; font-size: 16px; color: #fff; padding-left: 20px; background: #34ABD7; position: relative;">'
        inntHtml += '' + title + ''
       // inntHtml += '<div id="popWinClose" style="width: 28px; height: 28px; cursor: pointer; position: absolute; top: -12px; right: -9px;>'
		inntHtml += '<i id="popWinClose" class="fa fa-times" style="float: right; margin: 12px 15px 0 0;cursor: pointer; -webkit-transition: -webkit-transform 2s;"></i>'
		//inntHtml += '</div>'
        inntHtml += '</div>'
        inntHtml += '<iframe id="showWin_iframe" name="showWin_iframe" width="' + width + '" height="' + iframeHeight + '" frameborder="0" scrolling="' + this.scrolling + '" src="' + src + '"></iframe>';

        $("body",window.parent.document).append(inntHtml);
		$("#maskTop",window.parent.document).animate({left : "50%", width : width+"px", marginLeft : "-"+marginLeft+"px"},{queue:false, duration:600, easing: 'easeOutBack', complete:function(){
			$("#maskTop",window.parent.document).animate({height : height+"px", marginTop : "-"+marginTop+"px"},{queue:false, duration:600, easing: 'easeOutBack'});
		}});
		
        this.int();
    },
	
	
	showWinBack: function(editWin) {
		parent.popWin.initData = editWin;
        var iframeHeight = editWin.height - 42;
        var marginLeft = editWin.width / 2;
        var marginTop = editWin.height / 2;
        var inntHtml = '';
        inntHtml += '<div id="mask" style="width:100%; height:100%; position:fixed; top:0; left:0; background:#cccccc; filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity:0.5; z-index:1999;"></div>'
        inntHtml += '<div id="maskTop" style="width:50px; overflow:hidden; height:50px; background: #fff; color: #333; position: fixed; top: 50%; left: 200%; margin-left: -25px; margin-top: -25px; z-index: 2999; filter: progid:DXImageTransform.Microsoft.Shadow(color=#909090,direction=120,strength=4); -moz-box-shadow: 2px 2px 10px #909090; -webkit-box-shadow: 2px 2px 10px #909090; box-shadow: 2px 2px 10px #909090;">'
        inntHtml += '<div id="maskTitle" style="height: 40px; line-height: 40px; font-family: Microsoft Yahei; font-size: 16px; color: #fff; padding-left: 20px; background: #34ABD7; position: relative;">'
        inntHtml += '' + editWin.title + ''
       // inntHtml += '<div id="popWinClose" style="width: 28px; height: 28px; cursor: pointer; position: absolute; top: -12px; right: -9px;>'
		inntHtml += '<i id="popWinClose" class="fa fa-times" style="float: right; margin: 12px 15px 0 0;cursor: pointer; -webkit-transition: -webkit-transform 2s;"></i>'
		//inntHtml += '</div>'
        inntHtml += '</div>'
        inntHtml += '<iframe id="popWinIframe" name="popWinIframe" width="' + editWin.width + '" height="' + iframeHeight + '" frameborder="0" scrolling="' + this.scrolling + '" src="' + editWin.src + '"></iframe>';

        $("body",window.parent.document).append(inntHtml);
		$("#maskTop",window.parent.document).animate({left : "50%", width : editWin.width+"px", marginLeft : "-"+marginLeft+"px"},{queue:false, duration:600, easing: 'easeOutBack', complete:function(){
			$("#maskTop",window.parent.document).animate({height : editWin.height+"px", marginTop : "-"+marginTop+"px"},{queue:false, duration:600, easing: 'easeOutBack'});
			editWin.func();
		}});
		
        this.int();
    },
	
	
	Question : function(title){
		parent.popWin.initData = title;
		var inntHtml = '';
        inntHtml += '<div id="mask" style="width:100%; height:100%; position:fixed; top:0; left:0; background:#cccccc; filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity:0.5; z-index:1999;"></div>'
        inntHtml += '<div id="maskTop" style="width: 400px; height: 180px; background: #fff; color: #333; position: fixed; top: 50%; left: 50%; margin-left: -200px; margin-top: -90px; z-index: 2999; filter: progid:DXImageTransform.Microsoft.Shadow(color=#909090,direction=120,strength=4); -moz-box-shadow: 2px 2px 10px #909090; -webkit-box-shadow: 2px 2px 10px #909090; box-shadow: 2px 2px 10px #909090;">'
        inntHtml += '<div id="maskTitle" style="height: 40px; line-height: 40px; font-family: Microsoft Yahei; font-size: 16px; color: #fff; padding-left: 20px; background:#34ABD7; position: relative;">'
        inntHtml += '' + title.titles + ''
        inntHtml += '<i id="popWinClose2" class="fa fa-times" style="float: right; margin: 12px 15px 0 0;cursor: pointer; -webkit-transition: -webkit-transform 2s;"></i>'
        inntHtml += '</div>'
        inntHtml += '<div id="contentBox" style="width:215px; height:auto; overflow:hidden; margin:0 auto; margin-top:30px;">'
		inntHtml += '<div style="width:37px; height:37px; float:left;"><img src="/Public/CRM/images/warning.png"/></div>'
		inntHtml += '<div id="questContent" style="width:auto; height:37px; line-height:37px; float:left;margin-left:15px; font-size:18px;">'+title.content+'</div></div>'
		inntHtml += '<div style="width:160px; height:35px; cursor: pointer; margin:0 auto; margin-top:20px;"><button style="margin-right:15px;" type="button" onclick="popWin.titleCallBack()" id="delOkButton_nm" class="btn btn-success"><i class="fa fa-check"></i> 确定</button><button type="button" onclick="popWin.cancelCallBack()" id="delCancelButtin_nm" class="btn btn-default"><i class="fa fa-times"></i> 取消</button></div>';
		$("body",window.parent.document).append(inntHtml);
		this.int();
	},	

	titleCallBack : function() {
		popWin.initData.func();
		$("#mask,#maskTop",window.parent.document).fadeOut(function() {
			$(this).remove();
		});
	},

	cancelCallBack : function(){
		//popWin.initData.cancelFunc();
		$("#mask,#maskTop",window.parent.document).fadeOut(function() {
			$(this).remove();
		});
	},

	mouseClose: function() {
        $("#popWinClose,#popWinClose2",window.parent.document).on('mouseenter', 
        function() {
			 $(this).css("-webkit-transform" , "rotate(360deg)");
        });

        $("#popWinClose,#popWinClose2",window.parent.document).on('mouseleave', 
        function() {
           $(this).css("-webkit-transform" , "rotate(0deg)");

        });

    },
	
	closeFadeOut : function(){
		$("#popWinClose2",window.parent.document).on('click', function(){
			$("#mask,#maskTop",window.parent.document).fadeOut(function() {
				$(this).remove();
			});
		});
	},

closeMask: function() {

	$("#maskTop",window.parent.document).animate({height : "50px",marginTop:"-25px"},{queue:false, duration:600, easing: 'easeOutBack',complete:function(){
		$("#maskTop",window.parent.document).animate({left : "200%"},{queue:false, duration:600, easing: 'easeOutBack',complete:function(){
			$("#mask,#maskTop",window.parent.document).fadeOut(function() {
				$(this).remove();
			});
		}});
		
	}});
	

    }

/*mouseDown : function(){
		var dragging = false;
		var iX, iY;
		//var elmen = $("div#maskTop");
		$("#maskTop").on('mousedown' , function(e){
			dragging = true;
                iX = e.clientX - this.offsetLeft;
                iY = e.clientY - this.offsetTop;
                this.setCapture && this.setCapture();
                return false;
		});
		document.onmousemove = function(e) {
                if (dragging) {
                var e = e || window.event;
                var oX = e.clientX - iX;
                var oY = e.clientY - iY;
                $("#maskTop").css({"left":oX + "px", "top":oY + "px"});
                return false;
                }
            };
            $(document).mouseup(function(e) {
                dragging = false;
                $("#maskTop")[0].releaseCapture();
                e.cancelBubble = true;
            })
	},*/

};