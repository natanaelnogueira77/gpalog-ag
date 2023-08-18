<?php

namespace Src\Models;

use GTG\MVC\Model;
use Src\Models\User;

class ResetPasswordForm extends Model 
{
    public $password = '';
    public $password_confirm = '';

    public function rules(): array 
    {
        return [
            'password' => [
                [self::RULE_REQUIRED, 'message' => _('A senha é obrigatória!')], 
                [self::RULE_MIN, 'min' => 5, 'message' => sprintf(_('A senha deve conter no mínimo %s caractéres!'), 5)]
            ],
            'password_confirm' => [
                [self::RULE_REQUIRED, 'message' => _('A confirmação de senha é obrigatória!')], 
                [self::RULE_MATCH, 'match' => 'password', 'message' => _('As senhas não correspondem!')]
            ]
        ];
    }
}