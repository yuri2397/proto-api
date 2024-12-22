<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\Tank;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\StationCreatedMail;
use App\Models\FuelTruckConfig;
use App\Models\FuelTruckConfigPart;
use App\Models\StationCashRegister;
use App\Models\TankStockFlow;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class StationController extends Controller
{
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'manager' => ['required', 'array'],
            'manager.name' => 'required|string|max:255',
            'manager.email' => 'required|email|unique:users,email',
            'manager.phone' => 'required|unique:users,phone',
            'tanks' => 'required',
            'array',
            'tanks.*.name' => 'required|string|max:255',
            'tanks.*.type' => 'required|string',
            'tanks.*.capacity' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $station = Station::create([
                'name' => $request->name,
                'location' => $request->location,
                'status' => Station::STATUS_ACTIVE
            ]);

            $manager = User::create([
                'name' => $request->manager['name'],
                'email' => $request->manager['email'],
                'phone' => $request->manager['phone'],
                'owner_type' => Station::class,
                'owner_id' => $station->id,
                'password' => bcrypt(Str::random(8)),
            ]);

            foreach ($request->input('tanks') as $item) {
                $tankItem = Tank::create([
                    'name' => $item['name'],
                    'capacity' => $item['capacity'],
                    'station_id' => $station->id,
                    'type' => $item['type'],
                    'current_quantity' => 0,
                    'status' => Tank::STATUS_ACTIVE
                ]);
            }

            $manager->assignRole('station_manager');

            Mail::to($manager->email)->send(new StationCreatedMail(user: $manager));

            DB::commit();
            return response()->json([
                'message' => __('Station created successfully!'),
                'station' => $station
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Station::with($request->with ?? []);

        $query->where('status', 'active');


        // Application des filtres
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('manager_id')) {
            $query->where('manager_id', $request->manager_id);
        }

        // Pagination
        $stations = $query->paginate(perPage: $request->perPage ?? 10, page: $request->page ?? 1, columns: $request->columns ?? ['*']);

        return response()->json($stations);
    }

    public function show(Request $request, Station $station)
    {
        return $station->load($request->with ?? []);
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'status' => 'required|string',
            'manager_id' => 'required|exists:users,id',
        ]);

        $station = Station::findOrFail($id);
        $station->update([
            'name' => $request->name,
            'type' => $request->type,
            'status' => $request->status,
            'manager_id' => $request->manager_id,
        ]);

        return response()->json([
            'message' => 'Station updated successfully!',
            'station' => $station
        ]);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $station = Station::findOrFail($id);
        $station->delete();

        return response()->json([
            'message' => 'Station deleted successfully!'
        ]);
    }

    // getMyStation
    public function getMyStation(Request $request)
    {
        $station = $request->user()->owner;
        return $station->load($request->input('with') ?? []);
    }

    // openedCashRegister
    public function openedCashRegister(Station $station)
    {
        $lastUnClosedCashRegister = StationCashRegister::where('station_id', $station->id)->whereNull('closing_date')->first();
        if ($lastUnClosedCashRegister) {
            return $lastUnClosedCashRegister->load('pumpCashRegisters.pump', 'pumpCashRegisters.pumpOperator', 'tankCashRegisters.tank',);
        }
        return null;
    }

    public function closeCashRegister(Station $station, Request $request)
    {
        $request->validate([
            'cash_register_id' => 'required|exists:station_cash_registers,id',
            'closing_amount' => 'required|numeric',
            'pumps' => 'required|array',
            'pumps.*.pump_id' => 'required|exists:pumps,id',
            'pumps.*.closing_quantity' => 'required|numeric',
            'tanks' => 'required|array',
            'tanks.*.tank_id' => 'required|exists:tanks,id',
            'tanks.*.closing_quantity' => 'required|numeric',
        ]);

        // test if the cash register is opened
        $currentCashRegister =  $this->openedCashRegister($station);
        if (!$currentCashRegister) {
            return $this->jsonResponse([
                'message' => 'Aucune caisse ouverte trouvée!',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            DB::beginTransaction();
            $now = now();
            $stationCashRegister = StationCashRegister::findOrFail($request->cash_register_id);
            
            $stationCashRegister->update([
                'closing_amount' => $request->closing_amount,
                'closing_date' => $now,
            ]);

            foreach ($request->input('pumps') as $item) {
                $pumpCashRegister = $stationCashRegister->pumpCashRegisters()->where('pump_id', $item['pump_id'])->first();
                $pumpCashRegister->update([
                    'closing_quantity' => $item['closing_quantity'],
                    'closing_date' => $now,
                ]);
            }

            foreach ($request->input('tanks') as $item) {
                $tankCashRegister = $stationCashRegister->tankCashRegisters()->where('tank_id', $item['tank_id'])->first();
                $tankCashRegister->update([
                    'closing_quantity' => $item['closing_quantity'],
                    'closing_date' => $now,
                ]);
            }

            $this->updateStationTankCurrentQuantity($stationCashRegister);

            DB::commit();

            return response()->json([
                'message' => 'La caisse a été clôturée avec succès!',
                'cash_register' => $stationCashRegister
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    private function updateStationTankCurrentQuantity(StationCashRegister $stationCashRegister)
    {
        $tankRegisters = $stationCashRegister->tankCashRegisters;
        foreach ($tankRegisters as $tankRegister) {
            $tank = $tankRegister->tank;
            $mouvedQuantity = (float)$tank->current_quantity - (float)$tankRegister->closing_quantity;
            TankStockFlow::create([
                'quantity' => -$mouvedQuantity,
                'previous_quantity' => $tank->current_quantity,
                'after_quantity' =>  $tankRegister->closing_quantity,
                'type' => 'cash_register_closing',
                'user_id' => auth()->id(),
                'tank_id' => $tank->id,
                'dataable_type' => StationCashRegister::class,
                'dataable_id' => $stationCashRegister->id,
                'data' => [
                    'station_cash_register_id' => $stationCashRegister->id,
                    'tank_register_id' => $tankRegister->id,
                    'mouved_quantity' => $mouvedQuantity,
                    'tank_register_closing_quantity' => $tankRegister->closing_quantity,
                    'tank_register_opening_quantity' => $tankRegister->opening_quantity,
                ],
            ]);
            $tank->update([
                'current_quantity' => $tankRegister->closing_quantity,
            ]);
        }
    }

    public function openCashRegister(Station $station, Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric',
            'pumps' => 'required|array',
            'pumps.*.pump_id' => 'required|exists:pumps,id',
            'pumps.*.open_quantity' => 'required|numeric',
            'pumps.*.pump_operator_id' => 'required|exists:pump_operators,id',
            'tanks' => 'required|array',
            'tanks.*.tank_id' => 'required|exists:tanks,id',
            'tanks.*.open_quantity' => 'required|numeric',
        ]);
       
        $currentCashRegister = $this->openedCashRegister($station);
        if ($currentCashRegister) {
            return $this->jsonResponse([
                'message' => 'Une caisse est déjà ouverte!',
                'cash_register' => $currentCashRegister->load('pumpCashRegisters', 'tankCashRegisters')
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            DB::beginTransaction();

            $now = now();

            $stationCashRegister = StationCashRegister::create([
                'station_id' => $station->id,
                'opening_amount' => $request->opening_amount,
                'reference' => 'CR-' . Str::upper(Str::random(7)),
                'opening_date' => $now,
            ]);

            foreach ($request->input('pumps') as $item) {
                $stationCashRegister->pumpCashRegisters()->create([
                    'pump_id' => $item['pump_id'],
                    'pump_operator_id' => $item['pump_operator_id'],
                    'opening_quantity' => $item['open_quantity'],
                    'opening_date' => $now,
                ]);
            }

            foreach ($request->input('tanks') as $item) {
                $stationCashRegister->tankCashRegisters()->create([
                    'tank_id' => $item['tank_id'],
                    'opening_quantity' => $item['open_quantity'],
                    'opening_date' => $now,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'La caisse a été ouverte avec succès!',
                'cash_register' => $stationCashRegister
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTrace()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    // report dasboard
    public function reportDashboard(Request $request, Station $station)
    {
       $request->validate([
        'from' => 'required|date',
        'to' => 'required|date',
       ]);

        /**
         * Je veux faire un tableau de bord avec les données de la station. Il fait pouvoir filtrer dans le temp.
         * Commandes de carburant :
         * 
         * Dates des commandes et statut (en attente, livrée, annulée).
         * Niveau de stock actuel pour chaque type de carburant.
         * Ventes journalières :
         * Montant total des ventes par type de produit/service (carburant, accessoires, services de lavage, etc.).
         * Nombre de transactions.
         * Graphique des ventes horaires ou par type de produit.
         * Caisse journalière :
         * Entrées et sorties de caisse.
         * Solde actuel de la caisse.
         * Total des paiements par méthode (espèces, carte bancaire, mobile money, etc.).

         * Gestion des stocks
         * Niveaux des réservoirs :
         * Volume actuel de chaque réservoir de carburant (en litres).
         * Alertes pour les stocks faibles.
         * Inventaire des produits :
         * Quantité restante des produits en boutique.
         * Produits à commander en urgence.

         * Gestion des employés
         * Performances :
         * Rapport des ventes par employé.
         * Horaires et présences.
         * Notifications pour les absences ou retards.

         * Chiffre d’affaires quotidien, hebdomadaire, mensuel.
         */

         $data = [];

        // Niveaux des réservoirs betwween from and to tankstockflows   
        if ($request->has('from') && $request->has('to')) {
            $tankStockFlows = TankStockFlow::where('station_id', $station->id)
                ->whereBetween('created_at', [$request->from, $request->to])
                ->get();
            $data['tank_levels'] = $tankStockFlows->groupBy('tank_id')->map(function ($group) {
                return [
                    'tank_id' => $group->first()->tank_id,
                    'tank_name' => $group->first()->tank->name,
                    'tank_current_quantity' => $group->sum('after_quantity'),
                ];
            });
        }

    }
}
