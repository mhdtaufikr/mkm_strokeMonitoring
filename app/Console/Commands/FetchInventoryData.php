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
        set_time_limit(300);
        $apiKey = '315f9f6eb55fd6db9f87c0c0862007e0615ea467'; // Replace with the actual API key
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

                // Fetch existing inventory IDs in the database for this location
                $existingInventoryIds = Inventory::where('location_id', $locationId)->pluck('_id')->toArray();
                $fetchedInventoryIds = [];

                foreach ($items as $item) {
                    // Ensure location exists in the database
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

                    // Find inventory based on code and location_id
                    $inventory = Inventory::where('code', $item['code'])->where('location_id', $item['location_id'])->first();

                    if ($inventory && $inventory->name == 'Auto-generated') {
                        // If inventory is found and named 'Auto-generated', update with API data
                        $inventory->update([
                            'name' => $item['name'],
                            'qty' => $item['qty'],
                            'variantCode' => $item['custom_field']['variantCode'] ?? null,
                            'location_id' => $item['location_id'],
                            'organization_id' => $item['organization_id'],
                            'updated_at' => $item['updated_at'],
                            'created_at' => $item['created_at'],
                            'part_no' => $item['custom_field']['part_no'] ?? null // Save part_no
                        ]);

                        // Update planned_inventory_items with the new inventory_id
                        PlannedInventoryItem::where('inventory_id', $inventory->_id)->update([
                            'inventory_id' => $inventory->_id
                        ]);
                    } else {
                        // If not found, create a new inventory entry
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
                                'created_at' => $item['created_at'],
                                'part_no' => $item['custom_field']['part_no'] ?? null // Save part_no
                            ]
                        );
                    }

                    // Add the fetched inventory ID to the array
                    $fetchedInventoryIds[] = $inventory->_id;
                }

                // Identify inventory IDs to update qty to 0
                $inventoryIdsToUpdate = array_diff($existingInventoryIds, $fetchedInventoryIds);

                // Update inventories not in the API response to have qty 0
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
