<?php

namespace Database\Seeders;

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

        // $employee = Role::create(['name' => 'pump_operator', 'guard_name' => 'api']);
        // $employee->givePermissionTo([
        //     'view_pump_index',
        //     'view_pump_transactions',
        // ]);

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

        // add tanks to station
        // $tank1 = \App\Models\Tank::create([
        //     'name' => 'Cuve 1',
        //     'type' =>  \App\Models\Tank::TYPE_GASOLINE,
        //     'capacity' => 15000,
        //     'current_quantity' => 8000,
        //     'station_id' => $station->id
        // ]);

        // app pump
        // $pump1Tank1 = \App\Models\Pump::create([
        //     'name' => 'Pompe 1',
        //     'status' => 'active',
        //     'tank_id' => $tank1->id,
        //     'station_id' => $station->id
        // ]);

        // $pump2Tank1 = \App\Models\Pump::create([
        //     'name' => 'Pompe 2',
        //     'status' => 'active',
        //     'tank_id' => $tank1->id,
        //     'station_id' => $station->id
        // ]);


        // $tank2 = \App\Models\Tank::create([
        //     'name' => 'Cuve 2',
        //     'type' =>  \App\Models\Tank::TYPE_DIESEL,
        //     'capacity' => 20000,
        //     'current_quantity' => 4000,
        //     'station_id' => $station->id
        // ]);

        // \App\Models\Pump::create([
        //     'name' => 'Pompe 1',
        //     'status' => 'active',
        //     'tank_id' => $tank2->id,
        //     'station_id' => $station->id
        // ]);

        // \App\Models\Pump::create([
        //     'name' => 'Pompe 2',
        //     'status' => 'active',
        //     'tank_id' => $tank2->id,
        //     'station_id' => $station->id
        // ]);

        // $tank3 = \App\Models\Tank::create([
        //     'name' => 'Cuve 3',
        //     'type' =>  \App\Models\Tank::TYPE_DIESEL,
        //     'capacity' => 20000,
        //     'current_quantity' => 13000,
        //     'station_id' => $station->id
        // ]);

        // \App\Models\Pump::create([
        //     'name' => 'Pompe 1',
        //     'status' => 'active',
        //     'tank_id' => $tank3->id,
        //     'station_id' => $station->id
        // ]);

        // /**
        //  * FUEL TRUCK DATA AND DRIVERS AND ONE Orders
        //  */

        // $truck = \App\Models\FuelTruck::create([
        //     'matricule' => 'SN 123456 DK',
        //     'transporter_name' => 'Diallo FuelTrans',
        // ]);

        // $driver = \App\Models\FuelTruckDriver::create([
        //     'name' => 'Moussa Diallo',
        //     'phone' => '+221 77 123 45 67',
        // ]);

        // /**
        //  * 1L => 800 FCFA
        //  */

        // $config = \App\Models\FuelTruckConfig::create([
        //     'total_quantity' => 14000,
        //     'total_amount' => 14000 * 800,
        //     'fuel_truck_id' => $truck->id,
        //     'fuel_truck_driver_id' => $driver->id,
        // ]);

        // $config->fuelTruckConfigParts()->create([
        //     'name' => 'Compartiment 1',
        //     'number' => '1',
        //     'quantity' => 2000,
        //     'capacity' => 10000,
        //     'type' => \App\Models\FuelTruckConfigPart::TYPE_DIESEL,
        // ]);

        // $config->fuelTruckConfigParts()->create([
        //     'name' => 'Compartiment 2',
        //     'number' => '2',
        //     'quantity' => 3000,
        //     'capacity' => 8000,
        //     'type' => \App\Models\FuelTruckConfigPart::TYPE_GASOLINE,
        // ]);

        // $config->fuelTruckConfigParts()->create([
        //     'name' => 'Compartiment 3',
        //     'number' => '3',
        //     'quantity' => 2000,
        //     'capacity' => 5000,
        //     'type' => \App\Models\FuelTruckConfigPart::TYPE_SUPER,
        // ]);

        // $config->fuelTruckConfigParts()->create([
        //     'name' => 'Compartiment 4',
        //     'number' => '4',
        //     'quantity' => 2000,
        //     'capacity' => 5000,
        //     'type' => \App\Models\FuelTruckConfigPart::TYPE_SUPER,
        // ]);

        // $config->fuelTruckConfigParts()->create([
        //     'name' => 'Compartiment 5',
        //     'number' => '5',
        //     'quantity' => 5000,
        //     'capacity' => 10000,
        //     'type' => \App\Models\FuelTruckConfigPart::TYPE_GASOLINE,
        // ]);

        // $order = \App\Models\StationFuelOrder::create([
        //     'fuel_truck_config_id' => $config->id,
        //     'quantity' => 14000,
        //     'amount' => 14000 * 800,
        //     'status' => \App\Models\StationFuelOrder::STATUS_INITIATED,
        // ]);

        // $order->stationFuelOrderItems()->create([
        //     'received_quantity' => 2000,
        //     'station_id' => $station->id,
        //     'fuel_truck_config_part_id' => $config->fuelTruckConfigParts()->where('quantity', 2000)->first()->id,
        //     'tank_id' => $tank1->id,
        // ]);

        // $order->stationFuelOrderItems()->create([
        //     'received_quantity' => 3000,
        //     'fuel_truck_config_part_id' => $config->fuelTruckConfigParts()->where('quantity', 3000)->first()->id,
        //     'station_id' => $station->id,
        // ]);

        // $order->stationFuelOrderItems()->create([
        //     'received_quantity' => 2000,
        //     'fuel_truck_config_part_id' => $config->fuelTruckConfigParts()->where('quantity', 2000)->first()->id,
        //     'station_id' => $station->id,
        // ]);

        // $order->stationFuelOrderItems()->create([
        //     'received_quantity' => 5000,
        //     'fuel_truck_config_part_id' => $config->fuelTruckConfigParts()->where('quantity', 5000)->first()->id,
        //     'station_id' => $station->id,
        // ]);


        /**
         * CATEGORIES DATA
         */

        // $categories = [
        //     [
        //         'name' => 'Carburants',
        //         'description' => 'Différents types de carburants comme l’essence et le diesel',
        //     ],
        //     [
        //         'name' => 'Lubrifiants',
        //         'description' => 'Différents types d’huiles et de lubrifiants pour véhicules et machines',
        //     ],
        //     [
        //         'name' => 'Accessoires',
        //         'description' => 'Accessoires pour véhicules comme les essuie-glaces, les désodorisants et les housses de siège',
        //     ],
        //     [
        //         'name' => 'Boissons',
        //         'description' => 'Boissons fraîches, eau et autres rafraîchissements disponibles à la station',
        //     ],
        //     [
        //         'name' => 'Snacks',
        //         'description' => 'Articles de collation comme les chips, les chocolats et autres aliments emballés',
        //     ],
        //     [
        //         'name' => 'Produits de lavage',
        //         'description' => 'Articles utilisés pour les services de lavage de voiture comme le savon, les éponges et les brosses',
        //     ],
        //     [
        //         'name' => 'Pneus et Batteries',
        //         'description' => 'Pneus de rechange, batteries de voiture et produits associés',
        //     ],
        //     [
        //         'name' => 'Produits d’entretien',
        //         'description' => 'Produits pour l’entretien des véhicules comme l’antigel, le liquide de frein et les nettoyants',
        //     ],
        // ];

        // foreach ($categories as $category) {
        //     StationProductCategory::firstOrCreate(['name' => $category['name']], $category);
        // }
    }
}
