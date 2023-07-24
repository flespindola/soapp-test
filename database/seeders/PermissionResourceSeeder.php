<?php

namespace Database\Seeders;

use App\Models\PermissionResource;
use App\Models\PermissionResourceCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        PermissionResourceCategory::upsert([
            ['id' => 1, 'name' => 'Geral', 'description' => 'Âmbito Geral'],
            ['id' => 2, 'name' => 'Financeiro', 'description' => 'Módulo Financeiro'],
        ], ['id'], ['name', 'description']);

        PermissionResource::upsert([
            [
                'id' => 1,
                'name' => 'Contactos/Empresas',
                'description' => 'Acesso a empresas',
                'permission_resource_category_id' => 1
            ],
        ], ['id'], ['name', 'description', 'permission_resource_category_id']);

    }
}
