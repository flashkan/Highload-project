<?php
require_once('vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class OrderSend
{
    public function orderFood($food, $address)
    {
        $data = [
            'food' => $food,
            'address' => $address,
        ];

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

        $channel = $connection->channel();
        $channel->queue_declare('OrderFood', false, true, false, false);

        $channel->basic_publish(new AMQPMessage(json_encode($data)), '', 'OrderFood');

        echo " [x] New Order <br>";

        $channel->close();
        $connection->close();
    }
}

(new OrderSend)->orderFood('bananas', 'address: some street 24');