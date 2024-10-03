<?php

namespace Application\Service;

use Stomp\Client;
use Stomp\Exception\StompException;

class ActiveMqProducer
{
    private $client;

    public function __construct(string $brokerUrl)
    {
        $this->client = new Client($brokerUrl);
        $this->client->connect();
    }

    public function sendMessage(string $queue, string $message)
    {
        try {
            $this->client->send($queue, $message);
            echo "Mensagem enviada para a fila $queue: $message";
        } catch (StompException $e) {
            echo "Erro ao enviar mensagem: " . $e->getMessage();
        }
    }

    public function disconnect()
    {
        $this->client->disconnect();
    }
}
