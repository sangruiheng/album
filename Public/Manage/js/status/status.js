//修改表的状态
$('.status').click(function(){
	var this_=$(this);
	var table,id,status,statusInfo,text1,text2;
	table  = this_.find('a').attr('data-table');
	id     = this_.find('a').attr('data-id');
	status = this_.find('a').attr('data-status');
	text1  = this_.find('a').attr('data-text1');
	text2  = this_.find('a').attr('data-text2');
	if(status == 0){
		statusInfo = 1;
	}else{
		statusInfo = 0;
	}
	$.post(APP+'/Common/table_status',{table:table,id:id,status:statusInfo},function(data){
		if(data.success == 1){
			if(data.status == 0 && id == data.id){
				this_.html('<a class="btn btn-danger" data-table="'+data.table+'" data-id="'+data.id+'" data-status="'+data.status+'" data-text1="'+text1+'" data-text2="'+text2+'" href="javascript:void(0);">'+text1+'</a>');
			}else{
				this_.html('<a class="btn btn-success" data-table="'+data.table+'" data-id="'+data.id+'" data-status="'+data.status+'" data-text1="'+text1+'" data-text2="'+text2+'" href="javascript:void(0);">'+text2+'</a>');
			}
		}else{
			updateAlert(data.info);
            setTimeout(function(){
                $('#top-alert').find('button').click();
                this_.removeClass('disabled').prop('disabled',false);
            },1500);
		}
	});
});

//下架
$('.system_status').click(function(){
    var this_=$(this);
    var table,id,status,statusInfo,text1,text2;
    table  = this_.find('a').attr('data-table');
    id     = this_.find('a').attr('data-id');
    status = this_.find('a').attr('data-status');
    text1  = this_.find('a').attr('data-text1');
    text2  = this_.find('a').attr('data-text2');
    if(status == 0){
        statusInfo = 1;
    }else{
        statusInfo = 0;
    }
    $.post(APP+'/Album/lowerFrame',{table:table,id:id,status:statusInfo},function(data){
        if(data.success == 1){
            if(data.status == 0 && id == data.id){
                this_.html('<a class="btn btn-danger" data-table="'+data.table+'" data-id="'+data.id+'" data-status="'+data.status+'" data-text1="'+text1+'" data-text2="'+text2+'" href="javascript:void(0);">'+text1+'</a>');
            }else{
                this_.html('<a class="btn btn-success" data-table="'+data.table+'" data-id="'+data.id+'" data-status="'+data.status+'" data-text1="'+text1+'" data-text2="'+text2+'" href="javascript:void(0);">'+text2+'</a>');
            }
        }else{
            updateAlert(data.info);
            setTimeout(function(){
                $('#top-alert').find('button').click();
                this_.removeClass('disabled').prop('disabled',false);
            },1500);
        }
    });
});
