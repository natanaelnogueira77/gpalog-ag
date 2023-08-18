<?php 

namespace Src\Database\Seeders;

use GTG\MVC\DB\Seeder;
use Src\Models\Config;

class ConfigSeeder extends Seeder 
{
    public function run(): void 
    {
        Config::insertMany([
            ['meta' => 'style', 'value' => 'light'],
            ['meta' => 'logo', 'value' => 'public/storage/users/user1/logo.png'],
            ['meta' => 'logo_icon', 'value' => 'public/storage/users/user1/logo.png'],
            ['meta' => 'login_img', 'value' => 'public/storage/users/user1/login-img.jpg']
        ]);
    }
}