<?php 

namespace Src\Database\Seeders;

use GTG\MVC\DB\Seeder;
use Src\Models\Street;

class StreetSeeder extends Seeder 
{
    public function run(): void 
    {
        Street::insertMany([
            [
                'usu_id' => 1, 
                'street_number' => 617, 
                'start_position' => 1, 
                'end_position' => 64, 
                'max_height' => 5, 
                'profile' => 2.20, 
                'max_plts' => 312,
                'obs' => 'POSSÍVEL ARMAZENAGEM DE PH NA 2ª ESTRUTURA'
            ],
            [
                'usu_id' => 1, 
                'street_number' => 618, 
                'start_position' => 1, 
                'end_position' => 64, 
                'max_height' => 5, 
                'profile' => 2.20, 
                'max_plts' => 312,
                'obs' => 'POSSÍVEL ARMAZENAGEM DE PH NA 2ª ESTRUTURA'
            ],
            [
                'usu_id' => 1, 
                'street_number' => 619, 
                'start_position' => 1, 
                'end_position' => 64, 
                'max_height' => 5, 
                'profile' => 2.20, 
                'max_plts' => 312,
                'obs' => 'POSSÍVEL ARMAZENAGEM DE PH NA 5ª ESTRUTURA'
            ],
            [
                'usu_id' => 1, 
                'street_number' => 620, 
                'start_position' => 1, 
                'end_position' => 64, 
                'max_height' => 6, 
                'profile' => 1.40, 
                'max_plts' => 380,
                'obs' => 'NÃO TEM POSSIBILIDADE DE ARMAZENAR PH'
            ],
            [
                'usu_id' => 1, 
                'street_number' => 621, 
                'start_position' => 1, 
                'end_position' => 59, 
                'max_height' => 6, 
                'profile' => 1.40, 
                'max_plts' => 332,
                'obs' => 'NÃO TEM POSSIBILIDADE DE ARMAZENAR PH'
            ],
            [
                'usu_id' => 1, 
                'street_number' => 622, 
                'start_position' => 1, 
                'end_position' => 28, 
                'max_height' => 6, 
                'profile' => 1.40, 
                'max_plts' => 168,
                'obs' => 'NÃO TEM POSSIBILIDADE DE ARMAZENAR PH'
            ],
            [
                'usu_id' => 1, 
                'street_number' => 631, 
                'start_position' => 1, 
                'end_position' => 20, 
                'max_height' => 5, 
                'profile' => 2.20, 
                'max_plts' => 100,
                'obs' => 'POSSÍVEL ARMAZENAGEM DE PH NAS 1ª, 2ª E 3ª ESTRUTURAS'
            ]
        ]);
    }
}