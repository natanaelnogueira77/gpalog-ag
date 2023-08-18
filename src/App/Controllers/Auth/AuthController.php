<?php

namespace Src\App\Controllers\Auth;

use GTG\MVC\Controller;
use Src\Models\Config;
use Src\Models\LoginForm;
use Src\Models\SocialUser;
use Src\Models\User;

class AuthController extends Controller 
{
    public function index(array $data): void 
    {
        $configMetas = (new Config())->getGroupedMetas(['logo', 'logo_icon', 'login_img']);

        $loginForm = new LoginForm();
        if($this->request->isPost()) {
            if($user = $loginForm->loadData($data)->login()) {
                $this->session->setAuth($user);
                $this->session->setFlash('success', sprintf(_("Seja bem-vindo(a), %s!"), $user->name));
                if(isset($data['redirect'])) {
                    $this->response->redirect(url($data['redirect']));
                } else {
                    if($user->isAdmin()) {
                        $this->redirect('admin.index');
                    } elseif($user->isADMUser()) {
                        $this->redirect('user.operations.index');
                    } elseif($user->isOperator()) {
                        $this->redirect('user.conference.index');
                    } else {
                        $this->redirect('user.index');
                    }
                }
            } else {
                $this->session->setFlash('error', _('Usuário e/ou senha inválidos!'));
            }
        }

        $this->render('auth/login', [
            'background' => $configMetas && $configMetas['login_img'] ? url($configMetas['login_img']) : null,
            'logo' => $configMetas && $configMetas['logo'] ? url($configMetas['logo']) : null,
            'shortcutIcon' => $configMetas && $configMetas['logo_icon'] ? url($configMetas['logo_icon']) : null,
            'redirect' => $_GET['redirect'],
            'loginForm' => $loginForm
        ]);
    }

    public function check(array $data): void 
    {
        $loginForm = new LoginForm();
        if($user = $loginForm->loadData($data)->login()) {
            $this->session->setAuth($user);
            $this->APIResponse([], 200);
        } else {
            $this->setMessage('error', _('Usuário e/ou senha inválidos!'))->APIResponse([], 422);
        }
    }

    public function logout(array $data): void 
    {
        $this->session->removeAuth();
        $this->redirect('auth.index');
    }

    public function expired(array $data): void 
    {
        if(!$this->session->getAuth()) {
            $this->APIResponse(['success' => true], 200);
            return;
        }

        $this->APIResponse(['success' => false], 200);
    }
}