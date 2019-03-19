//异步请求参数post
function postInfo(table,where,field,postType,dataID){
	$.post(APP+'/Common/getSelectScreen',{table:table,where:where,field:field},function(ret){
		if(ret.status == 1){
			switch(postType){
				case 'picture':
					$('.upload-img-box').show();
					$('.upload-pre-item>img').attr('src',ret.data[0].path);
				break;
				case 'picList':
					console.log(ret);
					var thisHTML = '';
					for(var i=0;i<ret.data.length;i++){
						thisHTML += '<div class="upload-img-box" style="float: left;">';
						thisHTML += '<div class="upload-pre-item">';
						thisHTML += '<img src="'+ret.data[i].path+'" title="点击放大" data-id="'+ret.data[i].id+'"/>';
						thisHTML += '<span class="btn-close btn-close-pictures" title="删除图片" onclick="del(event);">';
						thisHTML += '</span>';
						thisHTML += '</div>';
						thisHTML += '</div>';
					}
					$('#pictureID').parent().append(thisHTML);
				break;
				
			}
		}else{
			alert(ret.err);
		}
	});
}
//页面修改时 复选框默认选中
function chenkSelect(dataID,divClass,type)
{
	switch(type)
	{
		case 'editData':
			var data_list = dataID.split(',');
			$('.'+divClass).each(function(index){
				for(var i=0;i<data_list.length;i++){
					if($('.'+divClass+':eq('+index+')').attr('data-id') == data_list[i]){
						$('.'+divClass+':eq('+index+')').addClass("on");
					}
				}
			});
		break;
	}
	
}