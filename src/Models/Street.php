<?php

namespace Src\Models;

use GTG\MVC\DB\DBModel;
use Src\Models\Pallet;
use Src\Models\User;

class Street extends DBModel 
{
    public $allocateds;
    public $provider;
    public $user;

    public static function tableName(): string 
    {
        return 'rua';
    }

    public static function primaryKey(): string 
    {
        return 'id';
    }

    public static function attributes(): array 
    {
        return ['usu_id', 'street_number', 'start_position', 'end_position', 'max_height', 'profile', 'max_plts', 'obs'];
    }

    public function rules(): array 
    {
        return [
            'usu_id' => [
                [self::RULE_REQUIRED, 'message' => _('O usuário é obrigatório!')]
            ],
            'street_number' => [
                [self::RULE_REQUIRED, 'message' => _('O número da rua é obrigatório!')]
            ],
            'start_position' => [
                [self::RULE_REQUIRED, 'message' => _('A posição inicial é obrigatória!')]
            ],
            'end_position' => [
                [self::RULE_REQUIRED, 'message' => _('A posição final é obrigatória!')]
            ],
            'max_height' => [
                [self::RULE_REQUIRED, 'message' => _('A altura máxima é obrigatória!')]
            ],
            'profile' => [
                [self::RULE_REQUIRED, 'message' => _('O perfil é obrigatório!')]
            ],
            'max_plts' => [
                [self::RULE_REQUIRED, 'message' => _('A capacidade máxima é obrigatória!')]
            ],
            'obs' => [
                [self::RULE_MAX, 'max' => 500, 'message' => sprintf(_('A observação deve conter no máximo %s caractéres!'), 500)]
            ]
        ];
    }

    public function save(): bool 
    {
        $this->obs = $this->obs ? $this->obs : null;
        return parent::save();
    }

    public function user(string $columns = '*'): ?User 
    {
        $this->user = $this->belongsTo(User::class, 'usu_id', 'id', $columns)->fetch(false);
        return $this->user;
    }

    public static function withUser(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo($objects, User::class, 'usu_id', 'user', 'id', $filters, $columns);
    }

    public static function getByUserId(int $userId, string $columns = '*'): ?array 
    {
        return (new self())->get(['usu_id' => $userId], $columns)->fetch(true);
    }

    public static function getAvailablePlacesByHeight(float $height, ?int $limit = null): ?array 
    {
        if(!$streets = (new Street())->get(['raw' => "profile = {$height}"])->order('street_number')->fetch(true)) {
            return null;
        }

        $pallets = (new Pallet())->get([
            'p_status' => Pallet::PS_STORED, 
            'raw' => "pallet_height = {$height}"
        ], 'street_number, position, height')->fetch(true);

        if($pallets) {
            $gPallets = [];
            foreach($pallets as $pallet) {
                $gPallets[$pallet->street_number][$pallet->position][$pallet->height] = true;
            }
            $pallets = $gPallets;
        }

        $availablePlaces = [];
        foreach($streets as $street) {
            for($i = $street->start_position; $i <= $street->end_position; $i++) {
                for($j = 1; $j <= $street->max_height; $j++) {
                    if($street->max_plts < count($pallets) + count($availablePlaces)) {
                        continue 3;
                    }

                    if(!is_null($limit) && $limit == 0) {
                        return $availablePlaces;
                    }

                    if(!isset($pallets[$street->street_number][$i][$j])) {
                        $availablePlaces[] = [
                            'street_number' => $street->street_number,
                            'position' => $i,
                            'height' => $j
                        ];
                        if(!is_null($limit)) $limit--;
                    }
                }
            }
        }

        return $availablePlaces;
    }
}