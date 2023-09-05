<?php

namespace Src\Models;

use GTG\MVC\Model;
use Src\Models\User;

class UserForm extends Model 
{
    public $id = null;
    public $utip_id = 0;
    public $name = null;
    public $email = null;
    public $password = null;
    public $password_confirm = null;
    public $update_password = null;
    public $registration_number = null;

    public function rules(): array 
    {
        return [
            'utip_id' => [
                [self::RULE_REQUIRED, 'message' => _('O tipo de usuário é obrigatório!')]
            ],
            'name' => [
                [self::RULE_REQUIRED, 'message' => _('O nome é obrigatório!')],
                [self::RULE_MAX, 'max' => 100, 'message' => sprintf(_('O nome deve conter no máximo %s caractéres!'), 100)]
            ],
            'email' => [
                [self::RULE_REQUIRED, 'message' => _('O email é obrigatório!')], 
                [self::RULE_EMAIL, 'message' => _('O email é inválido!')], 
                [self::RULE_MAX, 'max' => 100, 'message' => sprintf(_('O email deve conter no máximo %s caractéres!'), 100)]
            ]
        ] + (
            ($this->id && $this->update_password) || !$this->id 
            ? [
                'password' => [
                    [self::RULE_REQUIRED, 'message' => _('A senha é obrigatória!')], 
                    [self::RULE_MIN, 'min' => 5, 'message' => sprintf(_('A senha deve conter no mínimo %s caractéres!'), 5)]
                ],
                'password_confirm' => [
                    [self::RULE_REQUIRED, 'message' => _('A confirmação de senha é obrigatória!')], 
                    [self::RULE_MATCH, 'match' => 'password', 'message' => _('As senhas não correspondem!')]
                ]
            ] : []
        ) + [
            self::RULE_RAW => [
                function ($model) {
                    if(!$model->hasError('email')) {
                        if((new User())->get(['email' => $model->email] + (isset($model->id) ? ['!=' => ['id' => $model->id]] : []))->count()) {
                            $model->addError('email', _('O email informado já está em uso! Tente outro.'));
                        }
                    }

                    if(!$model->hasError('utip_id') && $model->utip_id == User::UT_OPERATOR) {
                        if(!$model->registration_number) {
                            $model->addError('registration_number', _('O número de matrícula é obrigatório!'));
                        } elseif(strlen($model->registration_number) > 20) {
                            $model->addError('registration_number', sprintf(_('O número de matrícula deve conter no máximo %s caractéres!'), 20));
                        } else {
                            if(!$model->id && User::getByRegistrationNumber($model->registration_number)) {
                                $model->addError('registration_number', _('O número de matrícula informado já está em uso! Tente outro.'));
                            }
                        }
                    }
                }
            ]
        ];
    }
}