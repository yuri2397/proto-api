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
            'view_stations',
            'view_my_station',
            'view_create_station',
            'view_station_list',
            'view_station_details',
            'view_station_products',
            'view_station_products_categories',
            'view_station_products_prices',
            'view_station_products_stock',
            'view_station_transactions',
            'view_station_transactions_details',
            'view_station_transactions_reports',
            'view_station_transactions_summary',
            'open_cash_register',
            'close_cash_register',
            'view_cash_register_summary',
            'view_pump_index',
            'view_pump_transactions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        $admin = Role::create(['name' => 'station_admin', 'guard_name' => 'api']);
        $admin->givePermissionTo(Permission::all());

        $manager = Role::create(['name' => 'station_manager', 'guard_name' => 'api']);
        $manager->givePermissionTo([
            'view_my_station',
            'view_station_products',
            'view_station_products_categories',
            'view_station_products_prices',
            'view_station_products_stock',
            'view_station_transactions',
            'open_cash_register',
            'close_cash_register',
            'view_cash_register_summary',
            'view_pump_index',
            'view_pump_transactions',
        ]);

        $employee = Role::create(['name' => 'pump_operator', 'guard_name' => 'api']);
        $employee->givePermissionTo([
            'view_pump_index',
            'view_pump_transactions',
        ]);

        $userAdmin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin')
        ]);
        $userAdmin->assignRole('station_admin');

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

        $userManager->assignRole('station_manager');

        // add tanks to station
        $tank1 = \App\Models\Tank::create([
            'name' => 'Cuve 1',
            'type' =>  \App\Models\Tank::TYPE_GASOLINE,
            'capacity' => 15000,
            'current_quantity' => 8000,
            'station_id' => $station->id
        ]);

        // app pump
        $pump1Tank1 = \App\Models\Pump::create([
            'name' => 'Pompe 1',
            'status' => 'active',
            'tank_id' => $tank1->id,
            'station_id' => $station->id
        ]);

        $pump2Tank1 = \App\Models\Pump::create([
            'name' => 'Pompe 2',
            'status' => 'active',
            'tank_id' => $tank1->id,
            'station_id' => $station->id
        ]);


        $tank2 = \App\Models\Tank::create([
            'name' => 'Cuve 2',
            'type' =>  \App\Models\Tank::TYPE_DIESEL,
            'capacity' => 20000,
            'current_quantity' => 4000,
            'station_id' => $station->id
        ]);

        \App\Models\Pump::create([
            'name' => 'Pompe 1',
            'status' => 'active',
            'tank_id' => $tank2->id,
            'station_id' => $station->id
        ]);

        \App\Models\Pump::create([
            'name' => 'Pompe 2',
            'status' => 'active',
            'tank_id' => $tank2->id,
            'station_id' => $station->id
        ]);

        $tank3 = \App\Models\Tank::create([
            'name' => 'Cuve 3',
            'type' =>  \App\Models\Tank::TYPE_DIESEL,
            'capacity' => 20000,
            'current_quantity' => 13000,
            'station_id' => $station->id
        ]);

        \App\Models\Pump::create([
            'name' => 'Pompe 1',
            'status' => 'active',
            'tank_id' => $tank3->id,
            'station_id' => $station->id
        ]);

        /**
         * FUEL TRUCK DATA AND DRIVERS AND ONE Orders
         */

        $truck = \App\Models\FuelTruck::create([
            'matricule' => 'SN 123456 DK',
            'transporter_name' => 'Diallo FuelTrans',
        ]);

        $driver = \App\Models\FuelTruckDriver::create([
            'name' => 'Moussa Diallo',
            'phone' => '+221 77 123 45 67',
        ]);

        /**
         * 1L => 800 FCFA
         */

        $config = \App\Models\FuelTruckConfig::create([
            'total_quantity' => 14000,
            'total_amount' => 14000 * 800,
            'fuel_truck_id' => $truck->id,
            'fuel_truck_driver_id' => $driver->id,
        ]);

        $config->fuelTruckConfigParts()->create([
            'quantity' => 2000,
            'capacity' => 10000,
            'type' => \App\Models\FuelTruckConfigPart::TYPE_DIESEL,
        ]);

        $config->fuelTruckConfigParts()->create([
            'quantity' => 3000,
            'capacity' => 8000,
            'type' => \App\Models\FuelTruckConfigPart::TYPE_GASOLINE,
        ]);

        $config->fuelTruckConfigParts()->create([
            'quantity' => 2000,
            'capacity' => 5000,
            'type' => \App\Models\FuelTruckConfigPart::TYPE_DIESEL,
        ]);

        $config->fuelTruckConfigParts()->create([
            'quantity' => 2000,
            'capacity' => 5000,
            'type' => \App\Models\FuelTruckConfigPart::TYPE_DIESEL,
        ]);

        $config->fuelTruckConfigParts()->create([
            'quantity' => 5000,
            'capacity' => 10000,
            'type' => \App\Models\FuelTruckConfigPart::TYPE_GASOLINE,
        ]);

        $order = \App\Models\StationFuelOrder::create([
            'fuel_truck_config_id' => $config->id,
            'quantity' => 14000,
            'amount' => 14000 * 800,
            'status' => \App\Models\StationFuelOrder::STATUS_INITIATED,
        ]);

        $order->stationFuelOrderItems()->create([
            'received_quantity' => 2000,
            'station_id' => $station->id,
            'fuel_truck_config_part_id' => $config->fuelTruckConfigParts()->where('quantity', 2000)->first()->id,
            'tank_id' => $tank1->id,
        ]);

        $order->stationFuelOrderItems()->create([
            'received_quantity' => 3000,
            'fuel_truck_config_part_id' => $config->fuelTruckConfigParts()->where('quantity', 3000)->first()->id,
            'station_id' => $station->id,
        ]);

        $order->stationFuelOrderItems()->create([
            'received_quantity' => 2000,
            'fuel_truck_config_part_id' => $config->fuelTruckConfigParts()->where('quantity', 2000)->first()->id,
            'station_id' => $station->id,
        ]);

        $order->stationFuelOrderItems()->create([
            'received_quantity' => 5000,
            'fuel_truck_config_part_id' => $config->fuelTruckConfigParts()->where('quantity', 5000)->first()->id,
            'station_id' => $station->id,
        ]);


        /**
         * CATEGORIES DATA
         */

        $categories = [
            [
                'name' => 'Carburants',
                'description' => 'Différents types de carburants comme l’essence et le diesel',
            ],
            [
                'name' => 'Lubrifiants',
                'description' => 'Différents types d’huiles et de lubrifiants pour véhicules et machines',
            ],
            [
                'name' => 'Accessoires',
                'description' => 'Accessoires pour véhicules comme les essuie-glaces, les désodorisants et les housses de siège',
            ],
            [
                'name' => 'Boissons',
                'description' => 'Boissons fraîches, eau et autres rafraîchissements disponibles à la station',
            ],
            [
                'name' => 'Snacks',
                'description' => 'Articles de collation comme les chips, les chocolats et autres aliments emballés',
            ],
            [
                'name' => 'Produits de lavage',
                'description' => 'Articles utilisés pour les services de lavage de voiture comme le savon, les éponges et les brosses',
            ],
            [
                'name' => 'Pneus et Batteries',
                'description' => 'Pneus de rechange, batteries de voiture et produits associés',
            ],
            [
                'name' => 'Produits d’entretien',
                'description' => 'Produits pour l’entretien des véhicules comme l’antigel, le liquide de frein et les nettoyants',
            ],
        ];

        foreach ($categories as $category) {
            StationProductCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
