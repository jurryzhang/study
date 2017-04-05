<?php
header('contet-type: text/html;charset=utf8');
date_default_timezone_set('Asia/shanghai');
#1459220204
$connection = new PDO("mysql:host=127.0.0.1;dbname=statistics;charset=utf8", 'root', 'root');
for($i = 10;$i<27; $i++){
    init($i);
}
/**
 * @return bool
 */
function init($i)
{
    $i    = $i < 10 ? '0' . $i : $i;
    $id   = 0;
    $date = '2016-12-'.$i;
    while (true) {
        $data  = getFromMysql($id, $date);
        $datas = '';
        if ($data) {
            foreach ($data as $value) {
             $tmpData                     = array();
                $tmpData['create']           = array();
                $tmpData['create']['_index'] = 'statistics-' . $date;
                $tmpData['create']['_type']  = 'collection';
                $datas .= json_encode($tmpData, JSON_UNESCAPED_UNICODE) . "\n";
                //数据
                $value['@timestamp'] = date('Y-m-d H:i:s', $value['view_time']);
                $datas .= json_encode($value, JSON_UNESCAPED_UNICODE) . "\n";
                // print_r($value);die;
                $id = $value['id'];
                unset($value['view_time'], $value['id']);
            }
            sendToEs($datas);
            unset($data);
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
function getFromMysql($start, $date)
{
    global $connection;
    $sql   = 'select * from `collection-' . $date . '` where id>' . $start . ' order by id asc limit 1000';
    $find  = $connection->query($sql);
    $datas = $find->fetchAll(PDO::FETCH_ASSOC);

    return count($datas) > 0 ? $datas : false;
}

function sendToEs($data)
{
    // echo $url . "\n";
    // print_r($data);die;
    #$data = json_encode($data, JSON_UNESCAPED_UNICODE);

    $ch = curl_init(); //初始化CURL句柄
    curl_setopt($ch, CURLOPT_URL, 'http://10.172.86.100:9200/_bulk'); //设置请求的URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); //设置请求方式

    //curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: 'PUT'"));//设置HTTP头信息
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置提交的字符串
    $chss = curl_exec($ch); //执行预定义的CURL
    curl_close($ch);
    var_dump('send----' . $chss);
    echo "\n";
    // var_dump($chss);
}

