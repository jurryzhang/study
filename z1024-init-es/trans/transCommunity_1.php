<?php
header('contet-type: text/html;charset=utf8');

require "vendor/autoload.php";
use Overtrue\Pinyin\Pinyin;

#1459220204
$connection = new PDO("mysql:host=127.0.0.1;dbname=db_house;charset=utf8", 'db_house', 'VF77hZPUBGAzyzVErc');
init();

function init()
{
    $id = 0;
    while (true) {
        $data  = getFromMysql($id);
        $datas = '';
        if ($data) {
            foreach ($data as $value) {
                // print_r($value);die;
                $tmpData                     = array();
                $tmpData['create']           = array();
                $tmpData['create']['_index'] = 'community_zz';
                $tmpData['create']['_type']  = 'community';
                $tmpData['create']['_id']    = $value['id'];
                $datas .= json_encode($tmpData, JSON_UNESCAPED_UNICODE) . "\n";
                //小区信息
                $source['community_id']           = intval($value['id']);
                $source['address']                = $value['address'];
                $source['name']                   = $value['name'];
                $source['city_id']                = intval($value['city_id']);
                $source['center_id']              = intval($value['center_id']);
                $source['district_id']            = intval($value['district_id']);
                $source['business_id']            = intval($value['business_id']);
                $source['near_subway_station']    = intval($value['near_subway_station']);
                $source['construction_time']      = intval($value['construction_time']);
                $source['soft_deleted']           = intval($value['soft_deleted']);
                $source['encomname']              = getEnComName($value['name']);
                $source['abbr']                   = getEnComName($value['name'], true);
                $source['geo_point_baidu']['lat'] = (float) $value['lat'];
                $source['geo_point_baidu']['lon'] = (float) $value['lng'];
                $source['geo_point_gaode']['lat'] = (float) $value['a_lat'];
                $source['geo_point_gaode']['lon'] = (float) $value['a_lng'];
                $datas .= json_encode($source, JSON_UNESCAPED_UNICODE) . "\n";
                echo '        ' . $value['id'] . '--' . $source['name'] . '---' . $source['encomname'] . '---' . $source['abbr'] . "\n";
            }
            $id = $value['id'];
            sendToEs($datas);
die;
        } else {
            return true;
        }
    }
}

/**
 * 批量查询房源信息
 * @param  [type] $start [description]
 * @return [type]        [description]
 */
function getFromMysql($start)
{
    global $connection;
    $datas = array();
    $find  = $connection->query('select id,name,lng,lat,address,city_id,center_id,district_id,business_id,near_subway_station,construction_time,soft_deleted,a_lng,a_lat from community_1 where id>' . $start . ' and soft_deleted=0 and id=6549');
    $datas = $find->fetchAll(PDO::FETCH_ASSOC);
    return count($datas) > 0 ? $datas : false;
}

function sendToEs($data)
{
     echo $data;die;
    $ch = curl_init(); //初始化CURL句柄
    curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:9200/_bulk'); //设置请求的URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); //设置请求方式

    //curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: 'PUT'"));//设置HTTP头信息
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置提交的字符串
    $chss = curl_exec($ch); //执行预定义的CURL
    curl_close($ch);
    echo 'send----' . "\n";
    var_dump($chss);die;
}

function getEnComName($string, $abbr = false)
{
    $prefix  = '/（.*）/';
    $sString = preg_replace($prefix, '', $string);
    $pinyin  = new Pinyin();
    if ($abbr) {
        $res = $pinyin->abbr($sString);
    } else {
        $res = $pinyin->permalink($sString, '');
    }
    return $res;
}

