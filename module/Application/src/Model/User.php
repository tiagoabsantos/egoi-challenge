<?php

namespace Application\Model;

use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\Input;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\Regex;
use Laminas\Validator\EmailAddress;

class User implements InputFilterAwareInterface
{
    public $id;
    public $name;
    public $email;
    public $phone;

    private $inputFilter;

    public function exchangeArray(array $data)
    {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->name = !empty($data['name']) ? $data['name'] : null;
        $this->email = !empty($data['email']) ? $data['email'] : null;
        $this->phone = !empty($data['phone']) ? $data['phone'] : null;
    }

    public function getArrayCopy()
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name'     => 'name',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => NotEmpty::class,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'O nome é obrigatório.',
                            ],
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'email',
                'required' => false,
                'validators' => [
                    [
                        'name'    => EmailAddress::class,
                        'options' => [
                            'allow'  => true,
                            'useMxCheck' => false,
                            'messages' => [
                                EmailAddress::INVALID_FORMAT => 'O formato do email é inválido.',
                            ],
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'phone',
                'required' => true,
                'filters'  => [
                    ['name' => 'Digits'],
                ],
                'validators' => [
                    [
                        'name'    => Regex::class,
                        'options' => [
                            'pattern'  => '/^[0-9]{9}$/',
                            'messages' => [
                                Regex::NOT_MATCH => 'O número de telefone deve conter exatamente 9 dígitos numéricos.',
                            ],
                        ],
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
}
