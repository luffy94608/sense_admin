<?php

include 'XingeApp.php';

/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 12/29/15
 * Time: 4:02 PM
 */

$ini_array = parse_ini_file("message_queue.ini",true);

$exchangeName = $ini_array['rabbitmq_message']['exchange_name'];
$queueName = $ini_array['rabbitmq_message']['queue_name'];
$routeKey = $ini_array['rabbitmq_message']['route_key'];

$connection = new AMQPConnection(array('host' => $ini_array['rabbitmq_base']['host']
, 'port' => $ini_array['rabbitmq_base']['port'], 'vhost' => $ini_array['rabbitmq_base']['vhost']
, 'login' => $ini_array['rabbitmq_base']['login'], 'password' => $ini_array['rabbitmq_base']['password']));
$connection->connect() or die("Cannot connect to the broker!\n");
$channel = new AMQPChannel($connection);
$exchange = new AMQPExchange($channel);
$exchange->setName($exchangeName);
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->declare();
$queue = new AMQPQueue($channel);
$queue->setName($queueName);
$queue->setFlags(AMQP_DURABLE);
$queue->declare();
$queue->bind($exchangeName, $routeKey);
$connection_db = new MongoClient($server = "mongodb://10.44.139.66:27017"); // 连接到 localhost:27017
$Android_AccessID = $ini_array['rabbitmq_message']['Android_AccessID'];
$Android_SecretKey = $ini_array['rabbitmq_message']['Android_SecretKey'];
$IOS_AccessID = $ini_array['rabbitmq_message']['IOS_AccessID'];
$IOS_SecretKey = $ini_array['rabbitmq_message']['IOS_SecretKey'];
var_dump('[*] Waiting for messages. To exit press CTRL+C');
while (TRUE) {
    try {
        $queue->consume('callback');
        $channel->qos(0, 1);
    } catch (Exception $e) {
        $connection = new AMQPConnection(array('host' => $ini_array['rabbitmq_base']['host']
        , 'port' => $ini_array['rabbitmq_base']['port'], 'vhost' => $ini_array['rabbitmq_base']['vhost']
        , 'login' => $ini_array['rabbitmq_base']['login'], 'password' => $ini_array['rabbitmq_base']['password']));
        $connection->connect() or die("Cannot connect to the broker!\n");
        $channel = new AMQPChannel($connection);
        $exchange = new AMQPExchange($channel);
        $exchange->setName($exchangeName);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->declare();
        $queue = new AMQPQueue($channel);
        $queue->setName($queueName);
        $queue->setFlags(AMQP_DURABLE);
        $queue->declare();
        $queue->bind($exchangeName, $routeKey);
    }

}

$connection_db->close();
$connection->disconnect();


function callback($envelope, $queue)
{
    $msg = json_decode($envelope->getBody());
    $uids = $msg->user_ids;
    $mess = $msg->mess;
    try {
        global $connection_db;
        global $IOS_AccessID;
        global $Android_AccessID;
        global $IOS_SecretKey;
        global $Android_SecretKey;
        if (!$connection_db->connected) {
            var_dump('reconnected-------------------------------------------');
            $connection_db = new MongoClient($server = "mongodb://10.44.139.66:27017");
        }
        $db = $connection_db->pinche;

    } catch (Exception $e) {
        var_dump($e->getMessage());
        return;
    }
    $mess_list = [];
    foreach ($uids as $id) {
        $token = $db->push_token->findOne(['_id' => $id]);
        array_push($mess_list,[
            "type"=> 12,
            "content"=> $mess,
            "timestamp"=> time(),
            "target_id"=> null,
            "index"=> -1,
            "status"=> 0,
            "object_uid"=> $id]);
        if ($token['platform'] == 'Android') {
            XingeApp::PushTokenAndroid($Android_AccessID, $Android_SecretKey, '哈罗同行', $mess, $token['token']);

        } else {
            XingeApp::PushTokenIos($IOS_AccessID, $IOS_SecretKey, $mess, $token['token'], XingeApp::IOSENV_PROD);
        }
    }
    $db->notification_center_new->batchInsert($mess_list);
    $queue->ack($envelope->getDeliveryTag());
}
