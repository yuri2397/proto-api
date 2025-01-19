<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StationProductCategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PreloadData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'shop_order_index',
            'shop_order_show',
            'shop_order_store',
            'shop_order_update',
            'shop_order_destroy',
            'shop_sale_index',
            'shop_sale_show',
            'shop_sale_store',
            'shop_sale_update',
            'shop_sale_destroy',
            'shop_product_provider_index',
            'shop_product_provider_show',
            'shop_product_provider_store',
            'shop_product_provider_update',
            'shop_product_provider_destroy',
            'shop_cash_register_index',
            'shop_cash_register_show',
            'shop_cash_register_store',
            'shop_cash_register_update',
            'shop_cash_register_destroy',
            'shop_user_index',
            'shop_user_show',
            'shop_user_store',
            'shop_user_update',
            'shop_user_destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        $admin = Role::create(['name' => 'shop_admin', 'guard_name' => 'api']);
        $admin->givePermissionTo(Permission::all());

        $manager = Role::create(['name' => 'shop_manager', 'guard_name' => 'api']);
        $manager->givePermissionTo($permissions);
        $userAdmin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin')
        ]);
        $userAdmin->assignRole('shop_admin');

        // add station 
        $station = \App\Models\Station::create([
            'name' => 'Station API Thiès',
            'location' => 'Thiès, Sénégal - Mbour - Route de la République',
        ]);

        // add station manager 
        $userManager = User::create([
            'name' => 'Ousmane Diouf',
            'email' => 'test@test.com',
            'phone' => '+221 77 123 45 67',
            'password' => Hash::make('manager'),
            'owner_type' => \App\Models\Station::class,
            'owner_id' => $station->id
        ]);

        $userManager->assignRole('shop_manager');

        $products = [
            [
                'name' => 'Riz Jasmine 5kg',
                'status' => 'active',
                'ean13' => '1234567890123',
                'category' => 'Aliments de base',
                'description' => 'Riz parfumé de qualité supérieure, 5 kg.',
                'default_price' => 4500.00,
            ],
            [
                'name' => 'Huile d’arachide 1L',
                'status' => 'active',
                'ean13' => '9876543210987',
                'category' => 'Huiles et graisses',
                'description' => 'Huile d’arachide pure 1L.',
                'default_price' => 2500.00,
            ],
            [
                'name' => 'Savon multi-usage',
                'status' => 'inactive',
                'ean13' => null, // Pas de code EAN13
                'category' => 'Produits d’entretien',
                'description' => 'Savon pour lessive et nettoyage général.',
                'default_price' => 500.00,
            ],
            [
                'name' => 'Jus de Bissap 500ml',
                'status' => 'active',
                'ean13' => '7894561230123',
                'category' => 'Boissons',
                'description' => 'Jus de Bissap fait maison, 500ml.',
                'default_price' => 1000.00,
            ],
            [
                'name' => 'Farine de maïs 1kg',
                'status' => 'active',
                'ean13' => '3216549876543',
                'category' => 'Aliments de base',
                'description' => 'Farine de maïs bio, 1kg.',
                'default_price' => 1200.00,
            ],
        ];

        Product::insert($products);
    }
}
