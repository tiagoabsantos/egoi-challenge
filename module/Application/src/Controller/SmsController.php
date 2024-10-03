<?php

namespace Application\Controller;

use Application\Service\SmsService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Exception;

class SmsController extends AbstractActionController
{
    public function indexAction()
    {
        $users = json_decode($this->getRequest()->getContent(), true)["contacts"];

        if (empty($users)) {
            return new JsonModel([
                'status' => 'error',
                'message' => 'Não existem contactos para serem enviados'
            ]);
        }

        $message = 'Olá, esta é uma mensagem SMS enviada assíncronamente!';

        try {
            (new SmsService())->sendSmsAsync($users, $message);
            return new JsonModel([
                'status' => 'success',
                'message' => 'Mensagens enviadas com sucesso!'
            ]);
        } catch (Exception $e) {
            return new JsonModel([
                'status' => 'error',
                'message' => 'Erro ao enviar SMS: ' . $e->getMessage()
            ]);
        }
    }
}
