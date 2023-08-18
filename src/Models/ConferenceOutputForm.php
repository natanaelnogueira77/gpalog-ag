<?php

namespace Src\Models;

use GTG\MVC\Model;
use Src\Models\Operation;
use Src\Models\Pallet;

class ConferenceOutputForm extends Model 
{
    const STEP_SERVICE_ORDER = 1;
    const STEP_PALLET = 2;
    const STEP_COMPLETION = 3;
    const STEP_EXPEDITION = 4;

    public $service_order = null;
    public $pallet_number = null;
    public $load_plate = null;
    public $dock = null;
    public $step = 0;
    private $operation = null;
    private $pallet = null;
    private $has_service_order = false;
    private $has_pallet = false;
    private $has_completion = false;

    public function rules(): array 
    {
        return (
            $this->isOnServiceOrder() || $this->isOnPallet() || $this->isOnCompletion() 
            ? [
                'service_order' => [
                    [self::RULE_REQUIRED, 'message' => _('A ordem de serviço é obrigatória!')]
                ]
            ] : []
        ) + (
            $this->hasServiceOrder() && ($this->isOnPallet() || $this->isOnCompletion())
            ? [
                'pallet_number' => [
                    [self::RULE_REQUIRED, 'message' => _('O número do pallet é obrigatório!')]
                ]
            ] : []
        ) + (
            $this->hasPallet() && $this->isOnCompletion() 
            ? [
                'load_plate' => [
                    [self::RULE_REQUIRED, 'message' => _('A placa de carregamento é obrigatória!')]
                ],
                'dock' => [
                    [self::RULE_REQUIRED, 'message' => _('A doca é obrigatória!')]
                ]
            ]
            : []
        );
    }

    public function getOperation(): ?Operation 
    {
        if(!$this->validate()) {
            return null;
        }

        if(!$this->operation = Operation::getByServiceOrder($this->service_order)) {
            $this->addError('service_order', _('Nenhuma ordem de serviço foi encontrada!'));
            return null;
        }

        $this->has_service_order = true;
        return $this->operation;
    }

    public function getPallet(): ?Pallet 
    {
        if(!$this->validate()) {
            return null;
        }

        if(!$this->pallet = Pallet::getByCode($this->pallet_number)) {
            $this->addError('pallet_number', _('Nenhum pallet foi encontrado!'));
            return null;
        } elseif(!$this->pallet->isSeparated()) {
            $this->addError('pallet_number', _('Esse pallet ainda não foi separado para saída!'));
            return null;
        } elseif($this->pallet->output()->ope_id != $this->operation->id) {
            $this->addError('pallet_number', _('Esse pallet não pertence à esse ID de separação!'));
            return null;
        }

        $this->has_pallet = true;
        return $this->pallet;
    }

    public function validateCompletion(): bool 
    {
        if(!$this->validate()) {
            return false;
        }

        $this->has_completion = true;
        return true;
    }

    public function isOnServiceOrder(): bool 
    {
        return $this->step == self::STEP_SERVICE_ORDER;
    }

    public function isOnPallet(): bool 
    {
        return $this->step == self::STEP_PALLET;
    }

    public function isOnCompletion(): bool 
    {
        return $this->step == self::STEP_COMPLETION;
    }
    
    public function isOnExpedition(): bool 
    {
        return $this->step == self::STEP_EXPEDITION;
    }

    public function hasServiceOrder(): bool 
    {
        return $this->has_service_order;
    }

    public function hasPallet(): bool 
    {
        return $this->has_pallet;
    }

    public function hasCompletion(): bool 
    {
        return $this->has_completion;
    }
}