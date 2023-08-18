<?php

namespace Src\App\Controllers\Auth;

use GTG\MVC\Components\Email;
use GTG\MVC\Controller;
use Src\Models\Config;
use Src\Models\ForgotPasswordForm;
use Src\Models\ResetPasswordForm;
use Src\Models\User;

class ResetPasswordController extends Controller 
{
    public function index(array $data): void 
    {
        $configMetas = (new Config())->getGroupedMetas(['logo', 'logo_icon', 'login_img']);
        $forgotPasswordForm = new ForgotPasswordForm();

        if($this->request->isPost()) {
            if($user = $forgotPasswordForm->loadData($data)->user()) {
                $user->saveMeta('last_pass_request', date('Y-m-d H:i:s'));

                $email = new Email();
                $email->add(_('Redefinir Senha'), $this->getView('emails/reset-password', [
                    'user' => $user,
                    'logo' => url((new Config())->getMeta('logo'))
                ]), $user->name, $user->email);
                
                if(!$email->send()) {
                    $this->session->setFlash('error', $email->error()->getMessage());
                }

                $this->session->setFlash('success', 
                    sprintf(_("Um email foi enviado para %s. Verifique para poder redefinir sua senha."), $user->email)
                );
                $this->redirect('auth.index');
            } else {
                $this->session->setFlash('error', _('Erros de validação! Verifique os campos.'));
            }
        }

        $this->render('auth/reset-password', [
            'background' => $configMetas && $configMetas['login_img'] ? url($configMetas['login_img']) : '',
            'logo' => $configMetas && $configMetas['logo'] ? url($configMetas['logo']) : '',
            'shortcutIcon' => $configMetas && $configMetas['logo_icon'] ? url($configMetas['logo_icon']) : '',
            'forgotPasswordForm' => $forgotPasswordForm
        ]);
    }

    public function verify(array $data): void 
    {
        $configMetas = (new Config())->getGroupedMetas(['logo', 'logo_icon', 'login_img']);
        
        if(!$user = User::getByToken($data['code'])) {
            $this->session->setFlash(_('A chave de verificação é inválida!'));
            $this->redirect('auth.index');
        }
        
        $resetPasswordForm = new ResetPasswordForm();
        if($this->request->isPost()) {
            if($resetPasswordForm->loadData($data)->validate()) {
                $user->password = $this->password;
                $user->save();

                $this->session->setAuth($user);
                $this->session->setFlash('success', _('A senha foi redefinida com sucesso!'));
                $this->redirect('auth.index');
            } else {
                $this->session->setFlash('error', _('Erros de validação! Verifique os campos.'));
            }
        }

        $this->render('auth/reset-password', [
            'code' => $data['code'],
            'background' => $configMetas && $configMetas['login_img'] ? url($configMetas['login_img']) : '',
            'logo' => $configMetas && $configMetas['logo'] ? url($configMetas['logo']) : '',
            'shortcutIcon' => $configMetas && $configMetas['logo_icon'] ? url($configMetas['logo_icon']) : '',
            'resetPasswordForm' => $resetPasswordForm
        ]);
    }
}