var options = {
	type:"POST",//请求方式：get或post
	dataType:"json",//数据返回类型：xml、json、script
	beforeSerialize:function(){
		
	},
	//data:{'icpnt':icpnt},//自定义提交的数据
	beforeSubmit:function(){
		//alert(CONTROLLER_NAME);
		if(CONTROLLER_NAME == 'Login'){
			$.showLoading('正在登陆……');
		}else{
			$.showLoading('正在提交……');
		}
	},
	success:function(json){//表单提交成功回调函数
		if(typeof(json.info) != "undefined" && typeof(json.url) != "undefined"){
			$.closeLoading(function(){
				window.location.href = json.url;
			});
		}else if(typeof(json.url) != "undefined" && typeof(json.info) == "undefined"){
			if(json.code.items1 == 'SUCCESS'){
				alert('发送成功');
				$.closeLoading(function(){
					window.location.href = '/'+CONTROLLER_NAME+'/'+json.url;
				});
			}else{
				alert(json.code.items2);
				console.log(json.code.items2);
				$.closeLoading();
			}
				
		}else{
			$.closeLoading();
		}
		
		$(".addForm").resetForm();
	},
	error:function(err){
		//console.log(err);
		alert("表单提交异常！"+JSON.stringify(err));
		$.closeLoading();
	}
};
$(document).ready(function(e) {
	$.Tipmsg.r= null;
	$(".addForm").Validform({
		tiptype:function(msg){
			updateAlert(msg);
            setTimeout(function(){
                $('#top-alert').find('button').click();
                $('.ajax-post').removeClass('disabled').prop('disabled',false);
            },1500);
			//$.loginTip(msg);
		},
		tipSweep:true
	});
	$(".addForm2").Validform({
		tiptype:function(msg){
			
			$.loginTip(msg);
		},
		tipSweep:true
	});
    $(".addForm").ajaxForm(options);
});