<?php

require_once('vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class OrderPayment
{
    public function getOrderAndPayment()
    {
        //Создаем соединение
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

        $channel = $connection->channel();
        $channel->queue_declare('Payment', false, true, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        //Уходим слушать сообщения из очереди в бесконечный цикл
        $channel->basic_consume('Payment', '', false, true, false, false, array($this, 'handler'));

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function handler($msg)
    {
        $data = json_decode($msg->body, true);
        $id = $data['id'];
        echo " [x] Start transaction on order $id <br>";
        sleep(5); // Оплата с счета покупателя.
        echo " [x] Transaction on order $id successfully finished <br>";

        $this->sendOrderOnDelivery($data);
    }

    public function sendOrderOnDelivery($data)
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('Delivery', false, true, false, false);
        $channel->basic_publish(new AMQPMessage(json_encode($data)), '', 'Delivery');
        $channel->close();
        $connection->close();
    }
}

(new OrderPayment)->getOrderAndPayment();