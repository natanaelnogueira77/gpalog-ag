<?php

namespace Src\Models;

use GTG\MVC\Model;
use Src\Models\User;

class ForgotPasswordForm extends Model 
{
    public $email = '';

    public function rules(): array 
    {
        return [
            'email' => [
                [self::RULE_REQUIRED, 'message' => _('O email é obrigatório!')], 
                [self::RULE_EMAIL, 'message' => _('O email é inválido!')],
                [self::RULE_MAX, 'max' => 100, 'message' => sprintf(_('O email precisa ter no máximo %s caractéres!'), 100)]
            ]
        ];
    }

    public function user(): ?User 
    {
        if(!$this->validate()) {
            return null;
        }

        if(!$user = User::getByEmail($this->email)) {
            $this->addError('email', _('O email não foi encontrado!'));
            return null;
        } elseif($lastRequest = $user->getMeta('last_pass_request')) {
            if(strtotime($lastRequest) >= strtotime('-1 hour')) {
                $this->addError('email', _('Uma requisição já foi enviada para este email! Espere 1 hora para poder enviar outra.'));
                return null;
            }
        }

        return $user;
    }
}