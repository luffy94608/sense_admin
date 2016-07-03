<?php
/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 1/28/16
 * Time: 3:27 PM
 */

require '../../library/PHPMailer/PHPMailerAutoload.php';
$ini_array = parse_ini_file("message_queue.ini",true);


$exchangeName = $ini_array['rabbitmq_mail']['exchange_name'];
$queueName = $ini_array['rabbitmq_mail']['queue_name'];
$routeKey = $ini_array['rabbitmq_mail']['route_key'];

//$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
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
    $ini_array = parse_ini_file("message_queue.ini",true);
    try{
        $msg = json_decode($envelope->getBody());
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $ini_array['rabbitmq_mail']['host'];
        $mail->Username = $ini_array['rabbitmq_mail']['user_name'];
        $mail->Password = $ini_array['rabbitmq_mail']['pass_word'];
        $mail->SMTPAuth = true;
        $mail->Port = $ini_array['rabbitmq_mail']['port'];
        $mail->setFrom($ini_array['rabbitmq_mail']['from_email']);
        $mail->setLanguage('zh-CN');
        $mail->CharSet = "UTF-8";


        $mail->addAddress($msg->to);

        $mail->isHTML(true);
        $mail->Subject = $msg->subject;
        $mail->Body    = $msg->body;
        $mail->AltBody = $msg->alt_body;

        if(!$mail->send()) {
            echo 'Message could not be sent.'.$msg->to;
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
        else{
            echo 'sent';
        }
        $queue->ack($envelope->getDeliveryTag());
    }
    catch(Exception $e){
        echo $e->getMessage();
        $queue->ack($envelope->getDeliveryTag());
    }
}



