<?php



function http_curl($url, $type = 'get', $res = 'json', $arr = '')
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

function curl_get($url, &$httpCode = 0)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //不做证书校验，部署在linux环境下请改为true
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $file_contents;

}



//批量替换富文本编辑器中的图片路径为绝对路径
function replacePicUrl($content = null, $strUrl = null) {
    if ($strUrl) {
        //提取图片路径的src的正则表达式 并把结果存入$matches中
        preg_match_all("/<img(.*)src=\"([^\"]+)\"[^>]+>/isU",$content,$matches);
        $img = "";
        if(!empty($matches)) {
            //注意，上面的正则表达式说明src的值是放在数组的第三个中
            $img = $matches[2];
        }else {
            $img = "";
        }
        if (!empty($img)) {
            $patterns= array();
            $replacements = array();
            foreach($img as $imgItem){
                $final_imgUrl = $strUrl.$imgItem;
                $replacements[] = $final_imgUrl;
                $img_new = "/".preg_replace("/\//i","\/",$imgItem)."/";
                $patterns[] = $img_new;
            }
            //让数组按照key来排序
            ksort($patterns);
            ksort($replacements);
            //替换内容
            $vote_content = preg_replace($patterns, $replacements, $content);
            return $vote_content;
        }else {
            return $content;
        }
    } else {
        return $content;
    }
}


?>