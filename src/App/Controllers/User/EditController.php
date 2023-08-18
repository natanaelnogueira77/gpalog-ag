<?php

namespace Src\App\Controllers\User;

use Src\App\Controllers\User\TemplateController;
use Src\Models\User;
use Src\Models\UserForm;

class EditController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/edit', ['user' => $this->session->getAuth()]);
    }

    public function update(array $data): void 
    {
        $user = $this->session->getAuth();
        $userForm = new UserForm();
        if(!$userForm->loadData(['id' => $user->id, 'utip_id' => $user->utip_id] + $data)->validate()) {
            $this->setMessage('error', _('Erros de validação! Verifique os campos.'))
                ->setErrors($userForm->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $dbUser = $user;
        $dbUser->loadData([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['update_password'] ? $data['password'] : $user->password,
            'slug' => $data['slug']
        ]);

        if(!$dbUser->save()) {
            $this->setMessage('error', _('Erros de validação! Verifique os campos.'))
                ->setErrors($dbUser->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->session->setAuth($dbUser);
        $this->setMessage('success', _('Seus dados foram atualizados com sucesso!'))->APIResponse([], 200);
        return;
    }
}