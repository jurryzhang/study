<?php
header('contet-type: text/html;charset=utf8');

#1459220204
$connection = new PDO("mysql:host=127.0.0.1;dbname=db_house;charset=utf8", 'root', 'root');
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
                $tmpData = array();
                $datas .= json_encode($tmpData, JSON_UNESCAPED_UNICODE) . "\n";
                $url = 'http://127.0.0.1:9200/rent_1/rent/' . $value['id'];
                //小区信息
                $source['rent_id']                 = intval($value['id']);
                $source['community_name']          = $value['name'];
                $source['community_id']            = intval($value['community_id']);
                $source['title']                   = $value['title'];
                $source['address']                 = $value['address'];
                $source['payment']                 = floatval($value['payment']);
                $source['area']                    = floatval($value['area']);
                $source['price']                   = floatval($value['price']);
                $source['rooms']                   = intval($value['rooms']);
                $source['city_id']                 = intval($value['city_id']);
                $source['halls']                   = intval($value['halls']);
                $source['floor']                   = intval($value['floor']);
                $source['total_floor']             = intval($value['total_floor']);
                $source['kitchens']                = intval($value['kitchens']);
                $source['washrooms']               = intval($value['washrooms']);
                $source['create_time']             = intval($value['create_time']);
                $source['update_time']             = intval($value['update_time']);
                $source['mortgage']                = floatval($value['mortgage']);
                $source['decorating_type']         = $value['decorating_type'];
                $source['recommend_weight']        = intval($value['recommend_weight']);
                $source['rent_type']               = intval($value['rent_type']);
                $source['is_exquisite']            = intval($value['is_exquisite']);
                $source['rent_status']             = intval($value['rent_status']);
                $source['district']                = $value['district'];
                $source['from_type']               = intval($value['from_type']);
                $source['direction']               = $value['direction'];
                $source['cover']                   = empty($value['cover']) ? null : $value['cover'];
                $source['support_bed']             = intval($value['support_bed']);
                $source['support_sofa']            = intval($value['support_sofa']);
                $source['support_toilet']          = intval($value['support_toilet']);
                $source['support_cook']            = intval($value['support_cook']);
                $source['support_wardrobe']        = intval($value['support_wardrobe']);
                $source['support_net']             = intval($value['support_net']);
                $source['support_air_conditioner'] = intval($value['support_air_conditioner']);
                $source['support_washer']          = intval($value['support_washer']);
                $source['support_fridge']          = intval($value['support_fridge']);
                $source['support_gas']             = intval($value['support_gas']);
                $source['support_basement']        = intval($value['support_basement']);
                $source['support_parking']         = intval($value['support_parking']);
                $source['support_heating']         = intval($value['support_heating']);
                $source['support_heater']          = intval($value['support_heater']);
                $source['support_furniture']       = intval($value['support_furniture']);
                $source['support_appliance']       = intval($value['support_appliance']);
                $source['support_terrace']         = intval($value['support_terrace']);
                $source['support_garden']          = intval($value['support_garden']);
                $source['feature_quick_check']     = intval($value['feature_quick_check']);
                $source['feature_school']          = intval($value['feature_school']);
                $source['feature_loft']            = intval($value['feature_loft']);
                $source['business_id']             = intval($value['business_id']);
                $source['create_time']             = intval($value['create_time']);
                $source['update_time']             = intval($value['update_time']);
                $source['soft_deleted']            = intval($value['soft_deleted']);
		$source['agent_code']              = intval($value['agent_code']);
                $source['business_name']           = $value['business_name'];
                $source['geo_point_baidu']['lat']  = (float) $value['lat'];
                $source['geo_point_baidu']['lon']  = (float) $value['lng'];
                $source['geo_point_gaode']['lat']  = (float) $value['a_lat'];
                $source['geo_point_gaode']['lon']  = (float) $value['a_lng'];

                sendToEs(json_encode($source, JSON_UNESCAPED_UNICODE), $url);
                // $datas .= json_encode($source, JSON_UNESCAPED_UNICODE) . "\n";
                // echo '        ' . $value['id'] . "\n";
            }
            $id = $value['id'];
        } else {
            return true;
        }
    }
}

function getRenovate($type)
{
    $array = array(1 => '毛坯', 2 => '简装', 3 => '中装', 4 => '精装', 5 => '豪装');
    $res   = array_search($type, $array);
    return $res ? $res : 4;
}

function getDirection($direction)
{
    $array = array(
        1  => "南北",
        2  => "南",
        3  => "东",
        4  => "北",
        5  => "东南",
        6  => "西",
        7  => "东西",
        8  => "西北",
        9  => "东北",
        10 => "西南",
    );

    $res = array_search($direction, $array);
    return $res ? $res : 4;
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
    $find  = $connection->query('select a.*,b.business_id,b.lat,b.lng,b.a_lng,b.a_lat,c.name as business_name from rent_1 a left join community_1 b on a.community_id=b.id left join business c on b.business_id=c.id where a.id >' . $start . ' order by a.id asc limit 500');
    $datas = $find->fetchAll(PDO::FETCH_ASSOC);

    return count($datas) > 0 ? $datas : false;
}

function sendToEs($data, $url)
{
    // echo $data;die;
    $ch = curl_init(); //初始化CURL句柄
    curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); //设置请求方式

    //curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: 'PUT'"));//设置HTTP头信息
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置提交的字符串
    $chss = curl_exec($ch); //执行预定义的CURL
    curl_close($ch);
    echo 'send----' . "\n";
    var_dump($chss);
}

