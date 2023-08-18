<?php

namespace Src\App\Controllers\Auth;

use GTG\MVC\Controller;
use Src\Models\Config;
use Src\Models\OperatorLoginForm;
use Src\Models\User;

class OperatorLoginController extends Controller 
{
    public function index(array $data): void 
    {
        $configMetas = (new Config())->getGroupedMetas(['logo', 'logo_icon', 'login_img']);

        $operatorLoginForm = new OperatorLoginForm();
        if($this->request->isPost()) {
            if($user = $operatorLoginForm->loadData($data)->login()) {
                $this->session->setAuth($user);
                $this->session->setFlash('success', sprintf(_("Seja bem-vindo(a), %s!"), $user->name));
                if(isset($data['redirect'])) {
                    $this->response->redirect(url($data['redirect']));
                } else {
                    $this->redirect('user.conference.index');
                }
            } else {
                $this->session->setFlash('error', _('Usuário e/ou senha inválidos!'));
            }
        }

        $this->render('auth/operator-login', [
            'title' => sprintf(_('Entrar - Operação | %s'), $this->appData['app_name']),
            'logo' => $configMetas && $configMetas['logo'] ? url($configMetas['logo']) : null,
            'shortcutIcon' => $configMetas && $configMetas['logo_icon'] ? url($configMetas['logo_icon']) : null,
            'redirect' => $_GET['redirect'],
            'operatorLoginForm' => $operatorLoginForm
        ]);
    }

    public function logout(array $data): void 
    {
        $this->session->removeAuth();
        $this->redirect('operatorLogin.index');
    }
}