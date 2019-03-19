<?php
namespace Api\Controller;
use Api\Exception\UserException;
use Think\Controller;
class CommonController extends Controller {
	//请求接口验证
	//域名验证

    public function _initialize(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods:POST,GET");
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    }

    /*字符串截断函数+省略号*/
    function subtext($text, $length)
    {
        if(mb_strlen($text, 'utf8') > $length)
            return mb_substr($text, 0, $length, 'utf8').'...';
        return $text;
    }
	
	//统一返回res
//	public function return_ajax($code=400;$msg='',$data=''){
//		$this->ajaxReturn(array('code'=>$code,'msg'=>$msg,'data'=>$data));
//	}

    public function return_ajax($code=400,$msg='',$data=array()){
        $this->ajaxReturn(array('code'=>$code,'msg'=>$msg,'data'=>$data));
    }

    //判断是否有token对应的用户
    public function is_user()
    {
        $userModel = M('user');
        $user = $userModel->where("id=$this->uid")->find();
        if (!$user) {
            $this->ajaxReturn((new UserException())->getException());
        }
        return $user;
    }

    //获取access_token  将获取access_token存放在session中
    public function getAccessToken(){
        if($_SESSION['access_token'] && $_SESSION['expire_time']>time()){  //如果access_token在session中并且没有过期
            return $_SESSION['access_token'];
        }else{ //如果access_token不存在或者已经过期 重新获取access_token并存入session中
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . C('WX.app_id') . '&secret=' . C('WX.app_secret');
            $access_token_array = $this->http_curl($url);
            $access_token = $access_token_array['access_token'];
            $_SESSION['access_token'] = $access_token;
            $_SESSION['expire_time'] = time()+7200;
            return $access_token;
//            var_dump($arr);
        }
    }


    //随机数
    public function createNonce($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public function uploadCommon() {
        $config = array(
            'mimes' => array(), //允许上传的文件MiMe类型
            'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
            'exts' => array( 'jpg', 'gif', 'png', 'jpeg', 'mp3' ), //允许上传的文件后缀
            'autoSub' => true, //自动子目录保存文件
            'subName' => array( 'date', 'Ymd' ), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => './Uploads/Manage/', //保存根路径
            'savePath' => '', //保存路径
        );
        $upload = new\ Think\ Upload( $config ); // 实例化上传类
        $info = $upload->upload();
        if ( !$info ) {
            $this->ajaxReturn( $upload->getError() );
        } else {
            return $info;
        }
    }

    public function http_curl($url, $type = 'get', $res = 'json', $arr = '')
    {   //抓取
        //获取imooc
        //1.初始化curl
        $ch = curl_init();
        //2.设置curl的参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }
        //3.采集
        $output = curl_exec($ch);
        //4.关闭
        curl_close($ch);
        if ($res == 'json') {
            return json_decode($output, true);
        }
    }


    public function request_url($MENU_URL, $data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $MENU_URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $info = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Errno'.curl_error($ch);
        }
        curl_close($ch);
        var_dump($info);
        return $info;
    }


    /**
     * 打印数据
     * @param  string $txt 日志记录
     * @param  string $file 日志目录
     * @return
     */
    public function printLog($txt = "", $file = "ceshi.log")
    {
        $myfile = fopen($file, "a+");
        $StringTxt = "[" . date("Y-m-d H:i:s") . "]" . var_export($txt, true) . "\n";
        fwrite($myfile, $StringTxt);
        fclose($myfile);

    }




}