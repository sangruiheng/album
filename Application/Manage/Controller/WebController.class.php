<?php 
namespace Manage\Controller; 
use Manage\Controller\CommonController; 
class WebController extends CommonController{ 
    /*图片上传*/ 
    public function picList(){ 
        $this->getMList('picture',$_GET['keyWord']); 
    }
    
    public function aaa(){
    	header("Content-type: text/html; charset=utf-8");
    	
    	$t1 = microtime(true);
    	$aaa = M('category')->field('id,title')->select();
    	$t2 = microtime(true);
    	echo '耗时'.round($t2-$t1,6).'秒<br/>';
    	
    	$t3 = microtime(true);
    	$bbb = M('category')->field('addTime,saveTime')->select();
    	$t4 = microtime(true);
    	echo '耗时'.round($t4-$t3,6).'秒<br/>';
    }
} 
