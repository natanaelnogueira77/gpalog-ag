<?php

namespace Src\App\Controllers\Admin;

use GTG\MVC\Controller;
use Src\Models\Config;

class TemplateController extends Controller 
{
    public function addData(): void 
    {
        $configMetas = (new Config())->getGroupedMetas(['logo', 'logo_icon', 'style']);
        $user = $this->session->getAuth();
        $user->userType();

        $logo = $configMetas && $configMetas['logo'] ? url($configMetas['logo']) : '';
        $logoIcon = $configMetas && $configMetas['logo_icon'] ? url($configMetas['logo_icon']) : '';
        $style = $configMetas['style'];

        $bgColors = [
            'left' => [
                'light' => 'bg-heavy-rain sidebar-text-dark',
                'dark' => 'bg-slick-carbon sidebar-text-light'
            ],
            'header' => [
                'light' => 'bg-heavy-rain header-text-dark',
                'dark' => 'bg-slick-carbon header-text-light'
            ]
        ];

        $leftMenu = [
            [
                'type' => 'heading', 
                'desc' => _('Painéis')
            ],
            [
                'type' => 'item', 
                'level' => 1, 
                'icon' => 'metismenu-icon pe-7s-display2', 
                'url' => $this->getRoute('admin.index'), 
                'desc' => _('Painel Principal')
            ],
            [
                'type' => 'heading', 
                'desc' => _('Usuários')
            ],
            [
                'type' => 'item', 
                'level' => 1, 
                'icon' => 'metismenu-icon pe-7s-users', 
                'url' => $this->getRoute('admin.users.index'), 
                'desc' => _('Usuários')
            ],
            [
                'type' => 'item', 
                'level' => 1, 
                'icon' => 'metismenu-icon pe-7s-user', 
                'url' => $this->getRoute('admin.users.create'), 
                'desc' => _('Cadastrar Usuário')
            ],
            [
                'type' => 'item', 
                'level' => 1, 
                'icon' => 'metismenu-icon pe-7s-server', 
                'url' => $this->getRoute('user.operations.index'), 
                'desc' => _('Operações')
            ],
            [
                'type' => 'item', 
                'level' => 1, 
                'icon' => 'metismenu-icon pe-7s-box2', 
                'url' => $this->getRoute('user.streets.index'), 
                'desc' => _('Ruas')
            ],
            [
                'type' => 'item', 
                'level' => 1, 
                'icon' => 'metismenu-icon pe-7s-box2', 
                'url' => $this->getRoute('user.products.index'), 
                'desc' => _('Produtos')
            ],
            [
                'type' => 'item', 
                'level' => 1, 
                'icon' => 'metismenu-icon pe-7s-users', 
                'url' => $this->getRoute('user.providers.index'), 
                'desc' => _('Fornecedores')
            ],
            [
                'type' => 'item', 
                'level' => 1, 
                'icon' => 'metismenu-icon pe-7s-server', 
                'url' => $this->getRoute('user.storage.index'), 
                'desc' => _('Armazenagem')
            ],
            [
                'type' => 'item', 
                'level' => 1, 
                'icon' => 'metismenu-icon pe-7s-next-2', 
                'url' => $this->getRoute('user.outputs.index'), 
                'desc' => _('Saídas')
            ],
            [
                'type' => 'item', 
                'level' => 1, 
                'icon' => 'metismenu-icon pe-7s-date', 
                'url' => $this->getRoute('user.inputOutputHistory.index'), 
                'desc' => _('Histórico de Entrada e Saída')
            ]
        ];

        $headerMenu = [
            [
                'type' => 'item', 
                'level' => 1, 
                'icon' => 'nav-link-icon fa fa-home', 
                'url' => $this->getRoute('auth.index'), 
                'desc' => _('Início')
            ],
            [
                'type' => 'item', 
                'level' => 1, 
                'icon' => 'nav-link-icon fa fa-phone', 
                'url' => $this->getRoute('contact.index'), 
                'desc' => _('Contato')
            ]
        ];

        $this->view->addData([
            'user' => $user,
            'storeAt' => 'public/storage/users/user' . $user->id,
            'logo' => $logo,
            'shortcutIcon' => $logoIcon,
            'loadingText' => _('Aguarde, carregando...'),
            'noLeft' => false,
            'noFooter' => false,
            'left' => [
                'color' => $configMetas['style'] ? $bgColors['left'][$configMetas['style']] : null,
                'menu' => $leftMenu,
                'active' => url() . filter_input(INPUT_GET, 'route', FILTER_DEFAULT)
            ],
            'header' => [
                'left' => true,
                'color' => $configMetas['style'] ? $bgColors['header'][$configMetas['style']] : null,
                'menu' => $headerMenu,
                'right' => [
                    'show' => true,
                    'languages' => [
                        'heading' => _('Linguagens'),
                        'curr_img' => url("public/imgs/flags/{$this->session->getLanguage()[1]}.png"),
                        'items' => [
                            ['url' => $this->getRoute('language.index', ['lang' => 'pt']), 'desc' => _('Português')],
                            ['url' => $this->getRoute('language.index', ['lang' => 'en']), 'desc' => _('Inglês')],
                            ['url' => $this->getRoute('language.index', ['lang' => 'es']), 'desc' => _('Espanhol')]
                        ]
                    ],
                    'items' => [
                        ['url' => $this->getRoute('admin.index'), 'desc' => _('Painel Principal')],
                        ['url' => $this->getRoute('user.edit.index'), 'desc' => _('Editar meus Dados')],
                        ['url' => $this->getRoute('auth.index'), 'desc' => _('Voltar ao Início')],
                        ['divider' => true],
                        ['url' => $this->getRoute('auth.logout'), 'desc' => _('Sair')]
                    ],
                    'avatar' => 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user->email))),
                    'avatar_title' => $user->name,
                    'avatar_subtitle' => $user->userType->name_sing
                ]
            ],
            'footer' => ['rightText' => sprintf(_('Painel %s'), $this->appData['app_version'])]
        ]);
    }
}