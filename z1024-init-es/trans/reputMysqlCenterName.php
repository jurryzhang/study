<?php
header('contet-type: text/html;charset=utf8');
date_default_timezone_set('Asia/shanghai');
#1459220204
$connection = new PDO("mysql:host=127.0.0.1;dbname=statistics;charset=utf8", 'statistics', 'LYb3GAEd6465Ee488');
for ($i = 26; $i < 27; $i++) {
    init($i);
}

/**
 * @return bool
 */
function init($i)
{
    $i = $i<10 ? '0'.$i:$i;
    global $connection;
    $centers = getCenter();
    $id      = 0;
    $date    = '2016-12-' . $i;
    $table   = 'collection-' . $date;
    while (true) {
        $data  = getFromMysql($id, $date);
        $datas = '';
        if ($data) {
            foreach ($data as $value) {
                $sql = 'update `' . $table . '` set center_name="' . $centers[$value['community_id']]['center_name'] . '" where id=' . $value['id'] . ";\n";
                echo $sql;
                $connection->query($sql);
            }
            $id = $value['id'];
        } else {
            break;
            return true;
        }
    }
}

/**
 *  *
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

function getCenter()
{
    global $connection;
    $sql  = 'select community_id,center_name from community_center';
    $find = $connection->query($sql);
    $arr  = $find->fetchAll(PDO::FETCH_ASSOC);
    return array_combine(array_column($arr, 'community_id'), $arr);
}

function sendToEs($data, $url)
{
    // echo $url . "\n";
    // print_r($data);die;
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);

    $ch = curl_init(); //    curl_setopt($ch, CURLOPT_URL, $url); //    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); //
    //curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: 'PUT'"));//    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //    $chss = curl_exec($ch); //    curl_close($ch);
    var_dump('send----' . $chss);
    echo "\n";
    // var_dump($chss);
}

