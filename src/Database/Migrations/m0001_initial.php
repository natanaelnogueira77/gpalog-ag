<?php 

namespace Src\Database\Migrations;

use GTG\MVC\DB\Migration;

class m0001_initial extends Migration 
{
    public function up(): void
    {
        $this->db->createTable('conferencia', function ($table) {
            $table->id();
            $table->integer('ope_id');
            $table->integer('adm_usu_id');
            $table->integer('start_usu_id')->nullable();
            $table->dateTime('date_start')->nullable();
            $table->integer('end_usu_id')->nullable();
            $table->dateTime('date_end')->nullable();
            $table->integer('c_status');
            $table->timestamps();
        });

        $this->db->createTable('config', function ($table) {
            $table->id();
            $table->string('meta', 50);
            $table->text('value')->nullable();
        });

        $this->db->createTable('entrada', function ($table) {
            $table->id();
            $table->integer('con_id');
            $table->integer('usu_id');
            $table->integer('pro_id');
            $table->integer('package');
            $table->integer('physic_boxes_amount');
            $table->integer('closed_plts_amount');
            $table->integer('units_amount');
            $table->integer('service_type');
            $table->float('pallet_height', 10, 2);
            $table->string('barcode', 50);
            $table->timestamps();
        });

        $this->db->createTable('fornecedor', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->string('name', 100);
            $table->timestamps();
        });

        $this->db->createTable('operacao', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->integer('for_id');
            $table->string('occurrence_number', 20);
            $table->string('password_number', 20);
            $table->string('order_number', 20);
            $table->string('invoice_number', 20);
            $table->string('plate', 20);
            $table->tinyInteger('has_palletization');
            $table->tinyInteger('has_rework');
            $table->tinyInteger('has_storage');
            $table->tinyInteger('has_import');
            $table->tinyInteger('has_tr');
            $table->timestamps();
        });

        $this->db->createTable('pallet', function ($table) {
            $table->id();
            $table->integer('con_id');
            $table->integer('pro_id');
            $table->integer('store_usu_id');
            $table->integer('package');
            $table->integer('physic_boxes_amount');
            $table->integer('units_amount');
            $table->integer('service_type');
            $table->float('pallet_height', 10, 2);
            $table->integer('street_number');
            $table->integer('position');
            $table->integer('height');
            $table->string('code', 20);
            $table->integer('sai_id')->nullable();
            $table->integer('release_usu_id')->nullable();
            $table->dateTime('release_date')->nullable();
            $table->string('load_plate', 20)->nullable();
            $table->string('dock', 20)->nullable();
            $table->integer('p_status');
            $table->timestamps();
        });

        $this->db->createTable('produto', function ($table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('prov_id');
            $table->string('prov_name', 100);
            $table->integer('prod_id');
            $table->integer('emb_fb')->nullable();
            $table->string('ean', 20)->nullable();
            $table->string('dun14', 20)->nullable();
            $table->integer('p_length')->nullable();
            $table->integer('p_width')->nullable();
            $table->integer('p_height')->nullable();
            $table->integer('p_base')->nullable();
            $table->float('p_weight', 10, 2)->nullable();
            $table->string('plu', 20)->nullable();
            $table->timestamps();
        });

        $this->db->createTable('rua', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->integer('street_number');
            $table->integer('start_position');
            $table->integer('end_position');
            $table->integer('max_height');
            $table->float('profile', 10, 2);
            $table->integer('max_plts');
            $table->string('obs', 500)->nullable();
            $table->timestamps();
        });

        $this->db->createTable('saida', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->integer('ope_id');
            $table->timestamps();
        });

        $this->db->createTable('social_usuario', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->string('social_id', 255)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('social', 100)->nullable();
            $table->timestamps();
        });

        $this->db->createTable('usuario', function ($table) {
            $table->id();
            $table->integer('utip_id');
            $table->string('name', 50);
            $table->string('email', 100);
            $table->string('password', 100);
            $table->string('token', 100);
            $table->string('slug', 100);
            $table->timestamps();
        });

        $this->db->createTable('usuario_meta', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->string('meta', 50);
            $table->text('value')->nullable();
        });

        $this->db->createTable('usuario_tipo', function ($table) {
            $table->id();
            $table->string('name_sing', 50);
            $table->string('name_plur', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->db->dropTableIfExists('conferencia');
        $this->db->dropTableIfExists('config');
        $this->db->dropTableIfExists('entrada');
        $this->db->dropTableIfExists('fornecedor');
        $this->db->dropTableIfExists('operacao');
        $this->db->dropTableIfExists('pallet');
        $this->db->dropTableIfExists('produto');
        $this->db->dropTableIfExists('rua');
        $this->db->dropTableIfExists('saida');
        $this->db->dropTableIfExists('social_usuario');
        $this->db->dropTableIfExists('usuario');
        $this->db->dropTableIfExists('usuario_meta');
        $this->db->dropTableIfExists('usuario_tipo');
    }
}