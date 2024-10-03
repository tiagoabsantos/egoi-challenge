<?php

namespace Application\Service;

use Stomp\Client;
use Stomp\Exception\StompException;

class ActiveMqConsumer
{
    private $client;

    public function __construct(string $brokerUrl)
    {
        $this->client = new Client($brokerUrl);
        $this->client->connect();
    }

    public function receiveMessages(string $queue)
    {
        try {
            while (true) {
                $message = $this->client->readFrame();
                if ($message) {
                    $data = json_decode($message->body, true);
                    $users = $data['users'];
                    $smsMessage = $data['message'];

                    foreach ($users as $user) {
                        echo "Numero: $user. Mensagem recebida da fila $queue: " . $message->body;
                        $this->client->ack($smsMessage);
                    }
                }
            }
        } catch (StompException $e) {
            echo "Erro ao receber mensagens: " . $e->getMessage();
        }
    }

    public function disconnect()
    {
        $this->client->disconnect();
    }
}
