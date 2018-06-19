<?php

function pr($are)
{
    echo '<pre>';
    print_r($are);
    echo '</pre>';
}

function getqishu()
{

    $week = date('W');
    //echo (string)($week*3)."=============".$week;
    $k = $week * 3 - 3;
    $now_w = date("w");

    $fptime = strtotime(date("Y-m-d 20:30:00"));
    $kjtime = strtotime(date("Y-m-d 21:30:00"));

    switch ($now_w) {
        case 1:
            $k = $k - 2;
            if (time() > $kjtime) {
                $k = $k + 1;
            }
            break;
        case 2:
            $k = $k - 1;
            break;
        case 3:
            $k = $k - 1;
            if (time() > $kjtime) {
                $k = $k + 1;
            }
            break;
        case 4:
            $k = $k;
            break;
        case 5:
            $k = $k;
            break;
        case 6:
            $k = $k;
            if (time() > $kjtime) {
                $k = $k + 1;
            }
            break;
        case 7:
            $k = $k + 1;
            break;
    }

    if ($k < 100) {
        $k = "0" . (string)$k;
    }
    $year = date('Y');
    // $year=substr($year,'2,5');
    $qishu = $year . $k;
    return $qishu;

}


/**
 * 获取客户端IP
 * @return [string] [description]
 */
function getClientIp()
{
    $ip = NULL;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) unset($arr[$pos]);
        $ip = trim($arr[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}

/**
 * 获取在线IP
 * @return String
 */
function getOnlineIp($format = 0)
{
    global $S_GLOBAL;
    if (empty($S_GLOBAL['onlineip'])) {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $onlineip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
        $S_GLOBAL['onlineip'] = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
    }

    if ($format) {
        $ips = explode('.', $S_GLOBAL['onlineip']);
        for ($i = 0; $i < 3; $i++) {
            $ips[$i] = intval($ips[$i]);
        }
        return sprintf('%03d%03d%03d', $ips[0], $ips[1], $ips[2]);
    } else {
        return $S_GLOBAL['onlineip'];
    }
}


/**
 * 获取url
 * @return [type] [description]
 */
function getUrl()
{
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

/**
 * 获取当前站点的访问路径根目录
 * @return [type] [description]
 */
function getSiteUrl()
{
    $uri = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : ($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
    return 'http://' . $_SERVER['HTTP_HOST'] . substr($uri, 0, strrpos($uri, '/') + 1);
}


/**
 * 字符串截取，支持中文和其他编码
 * @param  [string]  $str     [字符串]
 * @param  integer $start [起始位置]
 * @param  integer $length [截取长度]
 * @param  string $charset [字符串编码]
 * @param  boolean $suffix [是否有省略号]
 * @return [type]           [description]
 */
function msubstr($str, $start = 0, $length = 15, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr")) {
        return mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        return iconv_substr($str, $start, $length, $charset);
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    if ($suffix) {
        return $slice . "…";
    }
    return $slice;
}

/**
 * php 实现js escape 函数
 * @param  [type] $string   [description]
 * @param  string $encoding [description]
 * @return [type]           [description]
 */
function escape($string, $encoding = 'UTF-8')
{
    $return = null;
    for ($x = 0; $x < mb_strlen($string, $encoding); $x++) {
        $str = mb_substr($string, $x, 1, $encoding);
        if (strlen($str) > 1) { // 多字节字符
            $return .= "%u" . strtoupper(bin2hex(mb_convert_encoding($str, 'UCS-2', $encoding)));
        } else {
            $return .= "%" . strtoupper(bin2hex($str));
        }
    }
    return $return;
}

/**
 * php 实现 js unescape函数
 * @param  [type] $str [description]
 * @return [type]      [description]
 */
function unescape($str)
{
    $str = rawurldecode($str);
    preg_match_all("/(?:%u.{4})|.{4};|&#\d+;|.+/U", $str, $r);
    $ar = $r[0];
    foreach ($ar as $k => $v) {
        if (substr($v, 0, 2) == "%u") {
            $ar[$k] = iconv("UCS-2", "utf-8//IGNORE", pack("H4", substr($v, -4)));
        } elseif (substr($v, 0, 3) == "") {
            $ar[$k] = iconv("UCS-2", "utf-8", pack("H4", substr($v, 3, -1)));
        } elseif (substr($v, 0, 2) == "&#") {
            echo substr($v, 2, -1) . "";
            $ar[$k] = iconv("UCS-2", "utf-8", pack("n", substr($v, 2, -1)));
        }
    }
    return join("", $ar);
}

/**
 * 数字转人名币
 * @param  [type] $num [description]
 * @return [type]      [description]
 */
function num2rmb($num)
{
    $c1 = "零壹贰叁肆伍陆柒捌玖";
    $c2 = "分角元拾佰仟万拾佰仟亿";
    $num = round($num, 2);
    $num = $num * 100;
    if (strlen($num) > 10) {
        return "oh,sorry,the number is too long!";
    }
    $i = 0;
    $c = "";
    while (1) {
        if ($i == 0) {
            $n = substr($num, strlen($num) - 1, 1);
        } else {
            $n = $num % 10;
        }
        $p1 = substr($c1, 3 * $n, 3);
        $p2 = substr($c2, 3 * $i, 3);
        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
            $c = $p1 . $p2 . $c;
        } else {
            $c = $p1 . $c;
        }
        $i = $i + 1;
        $num = $num / 10;
        $num = (int)$num;
        if ($num == 0) {
            break;
        }
    }
    $j = 0;
    $slen = strlen($c);
    while ($j < $slen) {
        $m = substr($c, $j, 6);
        if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
            $left = substr($c, 0, $j);
            $right = substr($c, $j + 3);
            $c = $left . $right;
            $j = $j - 3;
            $slen = $slen - 3;
        }
        $j = $j + 3;
    }
    if (substr($c, strlen($c) - 3, 3) == '零') {
        $c = substr($c, 0, strlen($c) - 3);
    } // if there is a '0' on the end , chop it out
    return $c . "整";
}

/**
 * 特殊的字符
 * @param  [type] $str [description]
 * @return [type]      [description]
 */
function makeSemiangle($str)
{
    $arr = array(
        '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
        '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
        'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
        'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
        'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
        'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
        'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
        'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
        'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
        'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
        'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
        'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
        'ｙ' => 'y', 'ｚ' => 'z',
        '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
        '】' => ']', '〖' => '[', '〗' => ']', '｛' => '{', '｝' => '}', '《' => '<',
        '》' => '>',
        '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
        '：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',
        '；' => ';', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
        '”' => '"', '“' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
        '　' => ' ', '．' => '.');
    return strtr($str, $arr);
}

/**
 * 下载
 * @param  [type] $filename [description]
 * @param  string $dir [description]
 * @return [type]           [description]
 */
function downloads($filename, $dir = './')
{
    $filepath = $dir . $filename;
    if (!file_exists($filepath)) {
        header("Content-type: text/html; charset=utf-8");
        echo "File not found!";
        exit;
    } else {
        $file = fopen($filepath, "r");
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: " . filesize($filepath));
        Header("Content-Disposition: attachment; filename=" . $filename);
        echo fread($file, filesize($filepath));
        fclose($file);
    }
}

/**
 * 创建一个目录树
 * @param  [type]  $dir  [description]
 * @param  integer $mode [description]
 * @return [type]        [description]
 */
function mkdirs($dir, $mode = 0777)
{
    if (!is_dir($dir)) {
        mkdirs(dirname($dir), $mode);
        return mkdir($dir, $mode);
    }
    return true;
}

/*
 * php 防注入、防跨站 函数
 */
function fn_safe($str_string)
{
    //直接剔除
    $_arr_dangerChars = array(
        "|", ";", "$", "@", "+", "\t", "\r", "\n", ",", "(", ")", PHP_EOL //特殊字符
    );

    //正则剔除
    $_arr_dangerRegs = array(
        /* -------- 跨站 --------*/

        //html 标签
        "/<(script|frame|iframe|bgsound|link|object|applet|embed|blink|style|layer|ilayer|base|meta)\s+\S*>/i",

        //html 属性
        "/on(afterprint|beforeprint|beforeunload|error|haschange|load|message|offline|online|pagehide|pageshow|popstate|redo|resize|storage|undo|unload|blur|change|contextmenu|focus|formchange|forminput|input|invalid|reset|select|submit|keydown|keypress|keyup|click|dblclick|drag|dragend|dragenter|dragleave|dragover|dragstart|drop|mousedown|mousemove|mouseout|mouseover|mouseup|mousewheel|scroll|abort|canplay|canplaythrough|durationchange|emptied|ended|error|loadeddata|loadedmetadata|loadstart|pause|play|playing|progress|ratechange|readystatechange|seeked|seeking|stalled|suspend|timeupdate|volumechange|waiting)\s*=\s*(\"|')?\S*(\"|')?/i",

        //html 属性包含脚本
        "/\w+\s*=\s*(\"|')?(java|vb)script:\S*(\"|')?/i",

        //js 对象
        "/(document|location)\s*\.\s*\S*/i",

        //js 函数
        "/(eval|alert|prompt|msgbox)\s*\(.*\)/i",

        //css
        "/expression\s*:\s*\S*/i",

        /* -------- sql 注入 --------*/

        //显示 数据库 | 表 | 索引 | 字段
        "/show\s+(databases|tables|index|columns)/i",

        //创建 数据库 | 表 | 索引 | 视图 | 存储过程 | 存储过程
        "/create\s+(database|table|(unique\s+)?index|view|procedure|proc)/i",

        //更新 数据库 | 表
        "/alter\s+(database|table)/i",

        //丢弃 数据库 | 表 | 索引 | 视图 | 字段
        "/drop\s+(database|table|index|view|column)/i",

        //备份 数据库 | 日志
        "/backup\s+(database|log)/i",

        //初始化 表
        "/truncate\s+table/i",

        //替换 视图
        "/replace\s+view/i",

        //创建 | 更改 字段
        "/(add|change)\s+column/i",

        //选择 | 更新 | 删除 记录
        "/(select|update|delete)\s+\S*\s+from/i",

        //插入 记录 | 选择到文件
        "/insert\s+into/i",

        //sql 函数
        "/load_file\s*\(.*\)/i",

        //sql 其他
        "/(outfile|infile)\s+(\"|')?\S*(\"|')/i",
    );

    $_str_return = $str_string;
    //$_str_return = urlencode($_str_return);

    foreach ($_arr_dangerChars as $_key => $_value) {
        $_str_return = str_ireplace($_value, "", $_str_return);
    }

    foreach ($_arr_dangerRegs as $_key => $_value) {
        $_str_return = preg_replace($_value, "", $_str_return);
    }

    $_str_return = htmlentities($_str_return, ENT_QUOTES, "UTF-8", true);

    return $_str_return;
}

//中文字符串反转
function cnstrrev($str)
{
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        $char = $str{0};
        if (ord($char) > 127) //ord()函数取得第一个字符的ASCII码，如果大于0xa0(127)的话则是中文字符
        {
            $i += 2;//utf-8编码的情况下，一个中文字符占三个字节
            if ($i < $len) {
                $arr[] = substr($str, 0, 3);//utf-8编码的情况下，一个中文字符占三个字节
                $str = substr($str, 3);
            }
        } else {
            $arr[] = $char;
            $str = substr($str, 1);//否则为非中文，占一个字符
        }
    }
    return join(array_reverse($arr));//以相反的元素顺序返回数组：
}

//生成24位唯一订单号
function create_orderid()
{
    return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/*
        * 获取客户端ip
        * */
function get_client_ip2($type = 0)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) unset($arr[$pos]);
        $ip = trim($arr[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = ip2long($ip);
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

//加密
function encode($string = '', $skey = 'ysc')
{
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key] .= $value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}

//解密
function decode($string = '', $skey = 'ysc')
{
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}

//代码运行时间实例
function getruntime()
{
    $starttime = explode(' ', microtime());
    echo microtime();
    /*········以下是代码区·········*/
    for ($i = 0; $i < 10000; $i++) {
        $i;
    }
    /*········以上是代码区·········*/
    //程序运行时间
    $endtime = explode(' ', microtime());
    $thistime = $endtime[0] + $endtime[1] - ($starttime[0] + $starttime[1]);
    $thistime = round($thistime, 3);
    return "本网页执行耗时：" . $thistime . " 秒。" . time();

}

/**
 * 获取 IP  地理位置
 * 淘宝IP接口
 * 说明：新浪的接口，直接能获取到地址信息，淘宝的接口需要提供ip，不过获取的信息更全面
 * @Return: array
 */
function getCity($ip = '')
{
    if ($ip == '') {
        $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json";
        $ip = json_decode(file_get_contents($url), true);
        $data = $ip;
    } else {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        $ip = json_decode(file_get_contents($url));
        if ((string)$ip->code == '1') {
            return false;
        }
        $data = (array)$ip->data;
    }

    return $data;
}

/**
 * 根据腾讯IP分享计划的地址获取IP所在地，比较精确
 * @param  [type] $queryIP [description]
 * @return [type]          [description]
 */
function getCity_QQ($queryIP)
{
    $url = 'http://ip.qq.com/cgi-bin/searchip?searchip1=' . $queryIP;

    $ch = curl_init($url);
    // curl_setopt($ch,CURLOPT_ENCODING ,'gb2312');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
    $result = curl_exec($ch);
    // $result = mb_convert_encoding($result, "utf-8", "gb2312"); // 编码转换，否则乱码
    curl_close($ch);

    preg_match("@<span>(.*)</span></p>@iU", $result, $ipArray);
    $loc = $ipArray[1];

    return $loc;
}
//上传图片
function get_picture($file, $type)
{
    //pr($file['photo']['tmp_name']);exit;
    $date = date('Ymdhis');
    $extpos = strrpos($file['photo']['name'][0], '.');//返回字符串filename中'.'号最后一次出现的数字位置
    $ext = substr($file['photo']['name'][0], $extpos + 1);
    //echo $ext;exit;
    $path1 = 'uploadfile/' . $type . '/' . $date . '.' . $ext;
    if(move_uploaded_file($file['photo']['tmp_name'][0], $path1)){
        $path1 = '192.168.1.249/'. $path1;
        return $path1;
    }
    else{
        return $file['photo']['tmp_name'][0];

    }

}
// 参数解释
// $string： 明文 或 密文
// $operation：DECODE表示解密,其它表示加密
// $key： 密匙
// $expiry：密文有效期
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 4;

    // 密匙
    $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);//$GLOBALS['discuz_auth_key']自行设置

    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length):
        substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
        sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {
        // substr($result, 0, 10) == 0 验证数据有效性
        // substr($result, 0, 10) - time() > 0 验证数据有效性
        // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
        // 验证数据有效性，请看未加密明文的格式
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
            substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}
 function httpGet($url,$post_data=null) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    if($post_data!=null){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }
    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
}
function getCurl($url,$Referer=null){//get https的内容
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);//不输出内容
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT,15, false);

    curl_setopt($ch, CURLOPT_ENCODING, "");


    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
    if($Referer!=null){
        curl_setopt($ch,CURLOPT_REFERER,$Referer);
    }
    $result= curl_exec($ch);
    curl_close($ch);
    return $result;
}
//函数实现快速排序
function quick_sort($arr)
{
    //判断参数是否是一个数组
    if(!is_array($arr)) return false;
    //递归出口:数组长度为1，直接返回数组
    $length=count($arr);
    if($length<=1) return $arr;
    //数组元素有多个,则定义两个空数组
    $left=array();
    $right=array();
    //使用for循环进行遍历，把第一个元素当做比较的对象
    for($i=1;$i<$length;$i++)
    {
        //判断当前元素的大小
        if($arr[$i]<$arr[0]){
            $left[]=$arr[$i];
        }else{
            $right[]=$arr[$i];
        }
    }
    //递归调用
    $left=quick_sort($left);
    $right=quick_sort($right);
    //将所有的结果合并
    return array_merge($left,array($arr[0]),$right);


}
function CartesianProduct($sets){//笛卡尔积

    // 保存结果
    $result = array();

    // 循环遍历集合数据
    for($i=0,$count=count($sets); $i<$count-1; $i++){

        // 初始化
        if($i==0){
            $result = $sets[$i];
        }

        // 保存临时数据
        $tmp = array();

        // 结果与下一个集合计算笛卡尔积
        foreach($result as $res){
            foreach($sets[$i+1] as $set){
                $tmp[] = round($res*$set,2);
            }
        }

        // 将笛卡尔积写入结果
        $result = $tmp;

    }

    return $result;

}
/**
 * 阶乘
 */
function factorial($n,$m=1) {
    //array_product 计算并返回数组的乘积
    //range 创建一个包含指定范围的元素的数组
    return array_product(range($m, $n));
}

/**
 * 排列数
 */
function arrangement($n, $m) {
    return factorial($n,$n-$m+1);
}

/**
 * 组合数
 */
function combination($n, $m) {
    return arrangement($n, $m)/factorial($m);
}

