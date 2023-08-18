<?php

namespace Src\App\Controllers\Admin;

use Src\App\Controllers\Admin\TemplateController;
use Src\Models\Config;
use Src\Models\User;
use Src\Models\UserType;

class AdminController extends TemplateController  
{
    public function index(array $data): void 
    {
        $this->addData();

        if($dbUserCounts = (new User())->get([], 'utip_id, COUNT(*) as users_count')->group('utip_id')->fetch('count')) {
            foreach($dbUserCounts as $dbUserCount) {
                $usersCount[$dbUserCount->utip_id] = $dbUserCount->users_count;
            }
        }

        $this->render('admin/index', [
            'configMetas' => (new Config())->getGroupedMetas(['login_img', 'logo', 'logo_icon', 'style']),
            'userTypes' => (new UserType())->get()->fetch(true),
            'usersCount' => $usersCount
        ]);
    }

    public function system(array $data): void 
    {
        if(!$objects = (new Config())->saveManyMetas([
            'style' => $data['style'],
            'logo' => $data['logo'],
            'logo_icon' => $data['logo_icon'],
            'login_img' => $data['login_img']
        ])) {
            $this->setMessage('error', _('Lamentamos, mas ocorreu algum erro na requisição!'))->APIResponse([], 422);
            return;
        }

        if($errors = Config::getErrorsFromMany($objects, true)) {
            $this->setMessage('error', _('Erros de validação! Verifique os campos.'))->setErrors($errors)->APIResponse([], 422);
            return;
        }
        
        $this->setMessage('success', _('Configurações atualizadas com sucesso!'))->APIResponse([], 200);
    }
}