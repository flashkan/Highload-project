<?php

require_once('vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class OrderDelivery
{
    public function getOrderAndDelivery()
    {
        //Создаем соединение
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

        $channel = $connection->channel();
        $channel->queue_declare('Delivery', false, true, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        //Уходим слушать сообщения из очереди в бесконечный цикл
        $channel->basic_consume('Delivery', '', false, true, false, false, array($this, 'handler'));

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
        echo " [x] Order $id delivering <br>";
        sleep(5); // Доставка продуктов покупателю.
        echo " [x] Order delivered <br>";

        $this->putOrderInArchive($data);
    }

    public function putOrderInArchive($data)
    {
        $id = $data['id'];
        $result = file_get_contents('archive.json');
        if ($result) $result = json_decode($result, true);
        else $result = [];
        $result[$id] = $data;

        file_put_contents('archive.json', json_encode($result));

        echo "Archive saved with new order - $id <br>";
    }
}

(new OrderDelivery)->getOrderAndDelivery();