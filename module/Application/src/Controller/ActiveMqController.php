<?php

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Application\Service\ActiveMqProducer;

class ActiveMqController extends AbstractActionController
{
    private $producer;

    public function __construct(ActiveMqProducer $producer)
    {
        $this->producer = $producer;
    }

    public function indexAction()
    {
        $queue = '/queue/sms';
        $message = 'Olá, esta é uma mensagem SMS enviada via ActiveMQ!';

        $users = json_decode($this->getRequest()->getContent(), true)["contacts"];

        if (empty($users)) {
            return new JsonModel([
                'status' => 'error',
                'message' => 'Não existem contactos para serem enviados'
            ]);
        }

        $payload = json_encode([
            'recipients' => $users,
            'message' => $message,
        ]);

        $this->producer->sendMessage($queue, $payload);

        return new JsonModel(
            ['status' => 'success', 'message' => 'Mensagem enviada com sucesso!']
        );
    }
}
