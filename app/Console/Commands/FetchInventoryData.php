<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Inventory;
use App\Models\MstLocation;
use App\Models\PlannedInventoryItem;

class FetchInventoryData extends Command
{
    protected $signature = 'fetch:inventory';
    protected $description = 'Fetch inventory data from API and store in database';

    public function handle()
    {
        $apiKey = '315f9f6eb55fd6db9f87c0c0862007e0615ea467'; // Ganti dengan API key sebenarnya
        $locationIds = [
            '5fc4b12bc329204cb00b56bf'
        ];

        foreach ($locationIds as $locationId) {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey
            ])->get('https://api.mile.app/public/v1/warehouse/inventory', [
                'location_id' => $locationId,
                'stock_status' => '',
                'limit' => -1,
                'page' => 1,
                's' => '',
                'show_item' => false
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $items = $data['collection']['data'];

                // Dapatkan ID inventaris yang ada di database untuk lokasi ini
                $existingInventoryIds = Inventory::where('location_id', $locationId)->pluck('_id')->toArray();
                $fetchedInventoryIds = [];

                foreach ($items as $item) {
                    // Pastikan lokasi ada di database
                    $location = MstLocation::updateOrCreate(
                        ['_id' => $item['location_id']],
                        [
                            'name' => $item['location']['name'],
                            'code' => $item['location']['location_code'],
                            'address' => $item['location']['address'],
                            'phone' => $item['location']['phone'],
                            'location_type' => $item['location']['location_type'],
                            'lat' => $item['location']['lat'],
                            'lng' => $item['location']['lng']
                        ]
                    );

                    // Cari inventaris berdasarkan code dan location_id
                    $inventory = Inventory::where('code', $item['code'])->where('location_id', $item['location_id'])->first();

                    if ($inventory && $inventory->name == 'Auto-generated') {
                        // Jika inventaris ditemukan dan bernama 'Auto-generated', update dengan data dari API
                        $inventory->update([
                            'name' => $item['name'],
                            'qty' => $item['qty'],
                            'variantCode' => $item['custom_field']['variantCode'] ?? null,
                            'location_id' => $item['location_id'],
                            'organization_id' => $item['organization_id'],
                            'updated_at' => $item['updated_at'],
                            'created_at' => $item['created_at']
                        ]);

                        // Update planned_inventory_items dengan inventory_id yang baru
                        PlannedInventoryItem::where('inventory_id', $inventory->_id)->update([
                            'inventory_id' => $inventory->_id
                        ]);
                    } else {
                        // Jika tidak ditemukan, buat entri baru di inventaris
                        $inventory = Inventory::updateOrCreate(
                            ['_id' => $item['_id']],
                            [
                                'code' => $item['code'],
                                'name' => $item['name'],
                                'qty' => $item['qty'],
                                'variantCode' => $item['custom_field']['variantCode'] ?? null,
                                'location_id' => $item['location_id'],
                                'organization_id' => $item['organization_id'],
                                'updated_at' => $item['updated_at'],
                                'created_at' => $item['created_at']
                            ]
                        );
                    }

                    // Tambahkan ID inventaris yang berhasil diambil ke array
                    $fetchedInventoryIds[] = $inventory->_id;
                }

                // Tentukan ID inventaris yang perlu diupdate qty menjadi 0
                $inventoryIdsToUpdate = array_diff($existingInventoryIds, $fetchedInventoryIds);

                // Update inventaris yang tidak ada di API untuk memiliki qty 0
                Inventory::whereIn('_id', $inventoryIdsToUpdate)->update(['qty' => 0]);
            } else {
                Log::error('Failed to fetch inventory data for location ' . $locationId, [
                    'response_body' => $response->body()
                ]);
            }
        }

        $this->info('Inventory data fetched and stored successfully.');
    }
}
