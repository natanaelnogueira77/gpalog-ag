<?php

namespace Src\Models;

use GTG\MVC\DB\DBModel;

class Config extends DBModel 
{
    const LOGIN_IMG_KEY = 'login_img';
    const LOGO_KEY = 'logo';
    const LOGO_ICON_KEY = 'logo_icon';
    const STYLE_KEY = 'style';

    public static function tableName(): string 
    {
        return 'config';
    }

    public static function primaryKey(): string 
    {
        return 'id';
    }

    public static function attributes(): array 
    {
        return ['meta', 'value'];
    }

    public static function metaTableData(): ?array 
    {
        return [
            'class' => self::class,
            'meta' => 'meta',
            'value' => 'value'
        ];
    }

    public function rules(): array 
    {
        return [
            'meta' => [
                [self::RULE_REQUIRED, 'message' => _('O metadado é obrigatório!')],
                [self::RULE_MAX, 'max' => 50, 'message' => sprintf(_('O metadado deve conter no máximo %s caractéres!'), 50)]
            ],
            self::RULE_RAW => [
                function ($object) {
                    if(!$object->hasError('meta')) {
                        if($object->meta == self::LOGIN_IMG_KEY) {
                            if(!$object->value) {
                                $object->addError(self::LOGIN_IMG_KEY, _('A imagem de fundo do login é obrigatória!'));
                            } elseif(!in_array(pathinfo($object->value, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
                                $object->addError(self::LOGIN_IMG_KEY, _('A imagem de fundo não é uma imagem válida!'));
                            }
                        } elseif($object->meta == self::LOGO_KEY) {
                            if(!$object->value) {
                                $object->addError(self::LOGO_KEY, _('O logo é obrigatório!'));
                            } elseif(!in_array(pathinfo($object->value, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
                                $object->addError(self::LOGO_KEY, _('O logo não é uma imagem válida!'));
                            }
                        } elseif($object->meta == self::LOGO_ICON_KEY) {
                            if(!$object->value) {
                                $object->addError(self::LOGO_ICON_KEY, _('O ícone é obrigatório!'));
                            } elseif(!in_array(pathinfo($object->value, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
                                $object->addError(self::LOGO_ICON_KEY, _('O ícone não é uma imagem válida!'));
                            }
                        } elseif($object->meta == self::STYLE_KEY) {
                            if(!$object->value) {
                                $object->addError(self::STYLE_KEY, _('O tema é obrigatório!'));
                            } elseif(!in_array($object->value, ['light', 'dark'])) {
                                $object->addError(self::STYLE_KEY, _('O tema é inválido!'));
                            }
                        }
                    }
                }
            ]
        ];
    }
}