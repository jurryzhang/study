<?php
header('contet-type: text/html;charset=utf8');

#1459220204
$connection = new PDO("mysql:host=127.0.0.1;dbname=db_house;charset=utf8", 'db_house', 'VF77hZPUBGAzyzVErc');
init();

/**
 * @return bool
 */
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
                $tmpData['create']['_index'] = 'zz-resell-house';
                $tmpData['create']['_type']  = 'house';
                $tmpData['create']['_id']    = $value['id'];
                $datas .= json_encode($tmpData, JSON_UNESCAPED_UNICODE) . "\n";
                //小区信息
                $source['house_id']               = intval($value['id']);
                $source['community_id']           = intval($value['community_id']);
                $source['house_deleted']          = intval($value['soft_deleted']);
                $source['user_id']                = intval($value['user_id']);
                $source['price']                  = floatval($value['price']);
                $source['area']                   = floatval($value['area']);
                $source['rooms']                  = intval($value['rooms']);
                $source['halls']                  = intval($value['halls']);
                $source['floor']                  = intval($value['floor']);
                $source['total_floor']            = intval($value['total_floor']);
                $source['kitchens']               = intval($value['kitchens']);
                $source['washrooms']              = intval($value['washrooms']);
                $source['create_time']            = intval($value['create_time']);
                $source['update_time']            = intval($value['update_time']);
                $source['from_type']              = intval($value['from_type']);
                $source['title']                  = $value['title'];
                $source['district']               = $value['district'];
                $source['business']               = empty($value['business']) ? '' : $value['business'];
                $source['name']                   = $value['name'];
                $source['direction']              = $value['direction'];
                $source['has_elevator']           = $value['has_elevator'] ? true : false;
                $source['cover']                  = $value['cover'];
                $source['has_cover']              = empty($value['cover']) ? false : true;
                $source['vr']                     = intval($value['vr']);
                $source['recommend_weight']       = intval($value['recommend_weight']);
                $source['is_exquisite']           = $value['is_exquisite'] == 1 ? true : false;
                $source['city_id']                = intval($value['city_id']);
                $source['district_id']            = intval($value['district_id']);
                $source['business_id']            = intval($value['business_id']);
                $source['near_subway_station']    = $value['near_subway_station'] == 1 ? true : false;
                $source['construction_time']      = intval($value['construction_time']);
                $source['sold5years']             = intval($value['sold5years']);
                $source['community_deleted']      = intval($value['community_deleted']);
                $source['is_only']                = intval($value['is_only']);
                $source['feature_loft']           = intval($value['feature_loft']);
                $source['feature_school']         = intval($value['feature_school']);
                $source['feature_quick_check']    = intval($value['feature_quick_check']);
                $source['feature_less_initial']   = intval($value['feature_less_initial']);
                $source['support_attic']          = intval($value['support_attic']);
                $source['support_garden']         = intval($value['support_garden']);
                $source['support_terrace']        = intval($value['support_terrace']);
                $source['is_prospect']            = intval($value['is_prospect']);
                $source['agent_code']             = intval($value['agent_code']);
                $source['support_furniture']      = intval($value['support_furniture']);
                $source['support_appliance']      = intval($value['support_appliance']);
                $source['support_heating']        = intval($value['support_heating']);
                $source['support_parking']        = intval($value['support_parking']);
                $source['support_basement']       = intval($value['support_basement']);
                $source['support_gas']            = intval($value['support_gas']);
                $source['buy_time']               = intval($value['buy_time']);
                $source['decorating_type']        = $value['decorating_type'];
                $source['sell_status']            = intval($value['sell_status']);
                $source['geo_point_baidu']['lat'] = (float) $value['lat'];
                $source['geo_point_baidu']['lon'] = (float) $value['lng'];
                $source['geo_point_gaode']['lat'] = (float) $value['a_lat'];
                $source['geo_point_gaode']['lon'] = (float) $value['a_lng'];
                $source['house_deal_price']       = empty($value['house_deal_price']) ? 0.00 : $value['house_deal_price'];
                $source['house_deal_area']        = empty($value['house_deal_area']) ? 0.00 : $value['house_deal_area'];
                $source['house_desc']             = $value['house_desc'];
                $source['agent_say']              = $value['agent_say'];
                $source['agent_say_time']         = (int) $value['agent_say_time'];
                $source['agent_claim']            = (int) $value['agent_claim'];
                $datas .= json_encode($source, JSON_UNESCAPED_UNICODE) . "\n";
                // print_r($source);die;
                echo '        ' . $value['id'] . "\n";
            }
            $id = $value['id'];
            sendToEs($datas);
        } else {
            return true;
        }
    }
}

/**
 * 批量查询房源信息
 *
 * @param  [type] $start [description]
 *
 * @return [type]        [description]
 */
function getFromMysql($start)
{
    global $connection;
    $find  = $connection->query('select a.*,b.name,b.business_id,b.lat,b.lng,b.a_lng,b.a_lat,b.near_subway_station,b.construction_time,b.soft_deleted as community_deleted,c.name as business,d.house_deal_area,d.house_deal_price from house_1 a left join community_1 b on a.community_id=b.id left join business c on c.id=b.business_id left join house_deal_1 d on a.id=d.house_id where a.id>' . $start . ' order by a.id asc limit 500');
    $datas = $find->fetchAll(PDO::FETCH_ASSOC);

    return count($datas) > 0 ? $datas : false;
}

function sendToEs($data)
{
    // echo $data;die;
    $ch = curl_init(); //初始化CURL句柄
    curl_setopt($ch, CURLOPT_URL, 'http://10.172.86.100:9200/_bulk'); //设置请求的URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); //设置请求方式

    //curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: 'PUT'"));//设置HTTP头信息
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置提交的字符串
    $chss = curl_exec($ch); //执行预定义的CURL
    curl_close($ch);
    echo 'send----' . "\n";
    // var_dump($chss);die;
}

