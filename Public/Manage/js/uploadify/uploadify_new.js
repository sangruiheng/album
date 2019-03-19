/*------------------------------------------------------------/
功能：js上传图片到后台
参数：element参数为JSON对象
JSON对象示例：{
	"element_id": "pictureID", //input中元素ID 必传
	"upload_type": ".jpg,.png,.pneg", //上传文件类型 必传
	"upload_size": 0,//上传文件的大小 默认0不限制
	"upload_proportion": "100*100", //该参数可不传 该参数的作用在于限制图片的宽和高
	"upload_text": "上传图片",//必传 该参数是button按钮中的文字信息
};
返回： Callback 200成功图片路径 400返回错误信息
/------------------------------------------------------------*/
; var file_upload_picture = function(element,Callback){
	//将当前元素隐藏并且生成新的button按钮
	console.log(element.element_id);
	$("#"+element.element_id).hide();
	var thisHTML = '';
	thisHTML += "<div style='windth:60px;heigth:40px;'onclick='aaa("+element.element_id+");'>";
	thisHTML += "<div style='line-heigth:40px;'>";
	thisHTML += "<span style=''>";
	thisHTML += element.upload_text;
	thisHTML += "</span>";
	thisHTML += "</div>";
	thisHTML += "</div>";
	$("#"+element.element_id).after(thisHTML);
	
	
	//将上传成功的路劲返回到回调函数中
    if(typeof(Callback) === "function"){
    	Callback();
    }
};
function aaa(element_id){
	$("#"+element_id).click();
}
