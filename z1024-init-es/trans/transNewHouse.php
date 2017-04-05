<?php
require "vendor/autoload.php";
use Overtrue\Pinyin\Pinyin;
$pdo  = new PDO("mysql:host=127.0.0.1;dbname=db_house;charset=utf8", 'root', 'root');
$data = $pdo->query('select * from newhouse_1 where soft_deleted=0 order by id asc');

$datas = '';
while ($community = $data->fetch(PDO::FETCH_ASSOC)) {
    $source = array();
    // print_r($community);die;
    //小区信息
    $houseType                        = getHouseType($community['id'], $pdo);
    $url                              = 'http://127.0.0.1:9200/new_house_1/newhouse/' . $community['id'];
    $source['address']                = $community['address'];
    $source['name']                   = $community['community_name'];
    $source['encomname']              = getEnComName($community['community_name']);
    $source['price']                  = intval($community['price_avg']);
    $source['construction_category']  = explode(',', $community['construction_category']);
    $source['renovate']               = $community['renovate'];
    $source['property_age']           = intval($community['property_age']);
    $source['district']               = intval($community['district']);
    $source['selling_type']           = getIntArr(explode(',', $community['selling_type']));
    $source['property_type']          = explode(',', $community['property_type']);
    $source['cooperate']              = empty($community['buildings_price']) ? 0 : 1;
    $source['room']                   = $houseType['room'];
    $source['sell_tel']               = $community['sell_tel'];
    $source['update_time']            = intval($community['update_time']);
    $source['create_time']            = intval($community['create_time']);
    $source['sell_state']             = intval($community['sell_state']);
    $source['titlepic']               = empty($community['titlepic']) ? null : $community['titlepic'];
    $source['house_type']             = $houseType['house_type'];
    $source['city_id']                = 4101;
    $source['geo_point_baidu']['lat'] = (float) $community['lat'];
    $source['geo_point_baidu']['lon'] = (float) $community['lng'];
    $source['geo_point_gaode']['lat'] = (float) $community['a_lat'];
    $source['geo_point_gaode']['lon'] = (float) $community['a_lng'];
    
    //add redundancy columns
    $source['recommend_weight']  = $community['recommend_weight'];
    $source['recommend_reason']  = $community['recommend_reason'];
    $source['buildings_price']   = $community['buildings_price'];
    $source['price_avg']         = $community['price_avg'];
    $source['open_time']         = $community['open_time'];
    $source['commit_house']      = $community['commit_house'];
    $source['sell_address']      = $community['sell_address'];
    $source['land_area']         = $community['land_area'];
    $source['volume_ratio']      = $community['volume_ratio'];
    $source['total_building']    = $community['total_building'];
    $source['total_num']         = $community['total_num'];
    $source['property_company']  = $community['property_company'];
    $source['property_costs']    = floatval($community['property_costs']);
    $source['construction_area'] = $community['construction_area'];
    $source['feature']           = $community['feature'];
    // $type                             = getHouseType($community['id'], $pdo);
    // echo '        ' . $value['id'] . '--' . $source['name'] . '---' . $source['encomname'] . '---' . $source['abbr'] . "\n";

    // $id = $value['id'];
    sendToEs($url, $source);
}

function getIntArr($arr)
{
    $tmp = array();
    foreach ($arr as $v) {
        $tmp[] = intval($v);
    }
    return $tmp;
}
function getHouseType($id, $pdo)
{
    $tmp         = array('room' => array(), 'house_type' => array());
    $res         = $pdo->query('select building_area,room from newhousetype_1 where community_id=' . $id);
    $tmp['room'] = array();
    while ($area = $res->fetch()) {
        $tmp['house_type'][] = floatval($area['building_area']);
        if (!in_array($area['room'], $tmp['room'])) {
            $tmp['room'][] = floatval($area['room']);
        }
    }
    return $tmp;
}
function getEnComName($string, $abbr = false)
{
    $prefix  = '/（.*）/';
    $sString = preg_replace($prefix, '', $string);
    $pinyin  = new Pinyin();
    if ($abbr) {
        $res = $pinyin->abbr($sString, ' ');
    } else {
        $res = $pinyin->permalink($sString, '');
    }
    return $res;
}

function sendToEs($url, $data)
{
    // print_r($data);
    // return;
    $ch = curl_init(); //初始化CURL句柄
    curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); //设置请求方式

    //curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: 'PUT'"));//设置HTTP头信息
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //设置提交的字符串
    $chss = curl_exec($ch); //执行预定义的CURL
    curl_close($ch);
    echo 'send----' . "\n";
    var_dump($chss);
}

