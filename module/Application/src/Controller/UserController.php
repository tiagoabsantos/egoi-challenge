<?php

namespace Application\Controller;

use Application\Model\User;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Sql\Sql;

class UserController extends AbstractRestfulController
{
    protected $dbAdapter;

    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function indexAction()
    {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select('users');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $users = [];
        foreach ($result as $row) {
            $users[] = $row;
        }

        return new JsonModel($users);
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $body = json_decode($request->getContent(), true);

        $user = new User();
        $user->exchangeArray($body);

        $inputFilter = $user->getInputFilter();
        $inputFilter->setData($body);

        if (!$inputFilter->isValid()) {
            return new JsonModel([
                'status' => 'error',
                'messages' => $inputFilter->getMessages(),
            ]);
        }

        $sql = new Sql($this->dbAdapter);
        $insert = $sql->insert('users');
        $insert->values([
            'name' => $body['name'],
            'phone' => $body['phone'],
            'email' => $body['email'],
        ]);

        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();

        return new JsonModel([
            'status' => 'success',
            'data' => $user->getArrayCopy(),
        ]);
    }

    public function editAction()
    {
        $request = $this->getRequest();

        $id = (int) $this->params()->fromRoute('id', 0);

        if (empty($id)) {
            return new JsonModel([
                'status' => 'error',
                'message' => 'Não foi possivel obter o id.'
            ]);
        }

        $body = json_decode($request->getContent(), true);

        $sql = new Sql($this->dbAdapter);
        $update = $sql->update('users');
        $update->set([
            'name' => $body['name'],
            'phone' => $body['phone'],
            'email' => $body['email'],
        ])->where(['id' => $id]);

        $statement = $sql->prepareStatementForSqlObject($update);
        $statement->execute();

        return new JsonModel([
            'status' => 'updated',
            'data' => $body
        ]);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (empty($id)) {
            return new JsonModel([
                'status' => 'error',
                'message' => 'Não foi possivel obter o id.'
            ]);
        }

        $sql = new Sql($this->dbAdapter);
        $delete = $sql->delete('users')->where(['id' => $id]);

        $statement = $sql->prepareStatementForSqlObject($delete);
        $statement->execute();

        return new JsonModel([
            'status' => 'deleted'
        ]);
    }
}
