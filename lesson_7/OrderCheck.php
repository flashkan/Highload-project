<?php

require_once('vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class OrderCheck
{
    private $foodsAndPrices = [
        'apples' => '50',
        'bananas' => '30',
        'eggs' => '20',
        'meats' => '100',
        'tomatoes' => '40'
    ];

    public function OrderCheckAndSend()
    {
        //Создаем соединение
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

        $channel = $connection->channel();
        $channel->queue_declare('OrderFood', false, true, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        //Уходим слушать сообщения из очереди в бесконечный цикл
        $channel->basic_consume('OrderFood', '', false, true, false, false, array($this, 'handler'));

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function handler($msg)
    {
        $data = json_decode($msg->body, true);
        $food = $data['food'];
        echo " [x] Order on $food \n";
        if (array_key_exists($food, $this->foodsAndPrices)) {
            $data['id'] = uniqid();
            $data['price'] = $this->foodsAndPrices[$food];
            $data['feedback'] = '';

            echo 'Add order ' . $data['id'] . ' cost ' . $this->foodsAndPrices[$food] . "\n";

            $this->sendOrderOnPayment($data);
        } else echo $food . " not found \n";
    }

    public function sendOrderOnPayment($data)
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('Payment', false, true, false, false);
        $channel->basic_publish(new AMQPMessage(json_encode($data)), '', 'Payment');
        $channel->close();
        $connection->close();
    }
}

(new OrderCheck)->OrderCheckAndSend();