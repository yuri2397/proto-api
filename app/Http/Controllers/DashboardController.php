<?php

namespace App\Http\Controllers;

use App\Models\FuelTruckConfig;
use App\Models\FuelTruckConfigPart;
use App\Models\PumpCashRegister;
use Illuminate\Http\Request;
use App\Models\Station;
use App\Models\StationCashRegister;
use App\Models\Tank;

class DashboardController extends Controller
{
    const GASOLINE_PRICE = 775;
    const SUPER_PRICE = 995;
    public function adminDashboard()
    {
        $data = [];
        $stations = Station::all();
        $data['total_active_stations'] = $stations->count();

        $data['last_cash_register_amount'] = [];
        foreach ($stations as $station) {
            $cashRegister = StationCashRegister::where('station_id', $station->id)->orderBy('created_at', 'desc')->first();
            if ($cashRegister) {
                $item = [
                    'cash_register' => $cashRegister,
                    'station' => $station,
                    'ca' => $this->calculateTankCashRegister($cashRegister),
                ];
                $data['last_cash_register'][] = $item;
            }
        }

        $data['last_cash_register_quantity'] = [];
        foreach ($stations as $station) {
            $cashRegister = StationCashRegister::where('station_id', $station->id)->orderBy('created_at', 'desc')->first();
            if ($cashRegister) {
                $item = [
                    'gosoline' => $this->calculateTankCashRegisterQuantity($cashRegister)['gasoline'],
                    'super' => $this->calculateTankCashRegisterQuantity($cashRegister)['super'],
                    'station' => $station,
                ];
                $data['last_cash_register_quantity'][] = $item;
            }
        }

        // fuel truck config
        $fuelTruckConfigs = [];
        foreach ($stations as $station) {
            $fuelTruckConfig = FuelTruckConfigPart::where('station_id', $station->id)->orderBy('created_at', 'desc')->first();
            if ($fuelTruckConfig) {
                $item = [
                    'fuel_truck_config' => $fuelTruckConfig,
                    'station' => $station,
                ];
                $fuelTruckConfigs[] = $item;
            }
        }

        $data['fuel_truck_configs'] = $fuelTruckConfigs;
        
        return $data;
    }
    

    // calculer les C.A par cuve pour le $cashRegister 
    private function calculateTankCashRegister($cashRegister)
    {
        $caSuper = 0;
        $caGasoline = 0;
        foreach ($cashRegister->tankCashRegisters as $tankCashRegister) {
            $e = abs($tankCashRegister->opening_quantity - $tankCashRegister->closing_quantity);
            if ($tankCashRegister->tank->type == 'gasoline') {
                $caGasoline += $e * self::GASOLINE_PRICE;
            } else {
                $caSuper += $e * self::SUPER_PRICE;
            }
        }
        return [
            'caSuper' => $caSuper,
            'caGasoline' => $caGasoline,
            'total' => $caSuper + $caGasoline,
            'credit_sale' => 0
        ];
    }

    private function calculateTankCashRegisterQuantity(StationCashRegister $cashRegister)
    {
        $tankCashRegisters = $cashRegister->tankCashRegisters;
        $gasolineTankCashRegisters = $tankCashRegisters->where('tank.type', 'gasoline');
        $superTankCashRegisters = $tankCashRegisters->where('tank.type', 'super');
        
        
        
        return [
            'gasoline' => [
                'opening' => $gasolineTankCashRegisters->sum('opening_quantity'),
                'closing' => $gasolineTankCashRegisters->sum('closing_quantity'),
            ],
            'super' => [
                'opening' => $superTankCashRegisters->sum('opening_quantity'),
                'closing' => $superTankCashRegisters->sum('closing_quantity'),
            ],
        ];
    }
}

