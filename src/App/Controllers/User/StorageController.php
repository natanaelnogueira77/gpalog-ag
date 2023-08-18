<?php

namespace Src\App\Controllers\User;

use Src\App\Controllers\User\TemplateController;
use Src\Models\Pallet;
use Src\Models\Street;

class StorageController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();

        if($dbStreets = (new Street())->get()->fetch(true)) {
            $dbStreets = Street::getGroupedBy($dbStreets, 'street_number');
            $dbPalletCounts = (new Pallet())->get([
                'in' => ['p_status' => [Pallet::PS_STORED, Pallet::PS_SEPARATED]]
            ], 'street_number, COUNT(*) as pallets_count')->group('street_number')->fetch('count');
            if($dbPalletCounts) {
                foreach($dbPalletCounts as $dbPalletCount) {
                    $dbStreets[$dbPalletCount->street_number]->allocateds = $dbPalletCount->pallets_count;
                }
            }
        }

        $this->render('user/storage/index', [
            'dbStreets' => $dbStreets,
            'storageCapacity' => $dbStreets ? array_sum(array_map(fn($s) => $s->max_plts, $dbStreets)) : 0,
            'freeAmount' => $dbStreets ? array_sum(array_map(fn($s) => $s->max_plts - $s->allocateds, $dbStreets)) : 0,
            'allocatedAmount' => $dbStreets ? array_sum(array_map(fn($s) => $s->allocateds, $dbStreets)) : 0
        ]);
    }
}