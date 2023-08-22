<?php

namespace Src\Models;

use DateTime;
use GTG\MVC\DB\DBModel;
use Src\Models\Operation;
use Src\Models\Pallet;
use Src\Models\User;

class Output extends DBModel 
{
    public $operation;
    public $pallets = [];
    public $user;

    public static function tableName(): string 
    {
        return 'saida';
    }

    public static function primaryKey(): string 
    {
        return 'id';
    }

    public static function attributes(): array 
    {
        return ['usu_id', 'ope_id'];
    }

    public function rules(): array 
    {
        return [
            'usu_id' => [
                [self::RULE_REQUIRED, 'message' => _('O usuário é obrigatório!')]
            ],
            'ope_id' => [
                [self::RULE_REQUIRED, 'message' => _('A operação é obrigatória!')]
            ]
        ];
    }

    public function destroy(): bool 
    {
        if((new Pallet())->get(['sai_id' => $this->id])->count()) {
            $this->addError('destroy', _('Você não pode excluir uma ordem de saída vinculada à um pallet!'));
            return false;
        }
        return parent::destroy();
    }

    public function operation(string $columns = '*'): ?Operation 
    {
        $this->operation = $this->belongsTo(Operation::class, 'ope_id', 'id', $columns)->fetch(false);
        return $this->operation;
    }

    public function pallets(array $filters = [], string $columns = '*'): ?array
    {
        $this->pallets = $this->hasMany(Pallet::class, 'sai_id', 'id', $filters, $columns)->fetch(true);
        return $this->pallets;
    }

    public function user(string $columns = '*'): ?User 
    {
        $this->user = $this->belongsTo(User::class, 'usu_id', 'id', $columns)->fetch(false);
        return $this->user;
    }

    public static function withOperation(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo($objects, Operation::class, 'ope_id', 'operation', 'id', $filters, $columns);
    }

    public static function withPallets(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo($objects, Pallet::class, 'sai_id', 'pallets', 'id', $filters, $columns);
    }

    public static function withUser(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo($objects, User::class, 'usu_id', 'user', 'id', $filters, $columns);
    }

    public static function getByOperationId(int $operationId, string $columns = '*'): ?array 
    {
        return (new self())->get(['ope_id' => $operationId], $columns)->fetch(true);
    }

    public static function getByUserId(int $userId, string $columns = '*'): ?array 
    {
        return (new self())->get(['usu_id' => $userId], $columns)->fetch(true);
    }

    public function getCreationDateTime(): DateTime 
    {
        return new DateTime($this->created_at);
    }
}