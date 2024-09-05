<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\InventoryItem;
use App\Models\Inventory;
use App\Models\MstStrokeDies; // Ensure you include the model for MstStrokeDies

class FetchInventoryItemData extends Command
{
    protected $signature = 'fetch:inventory-item';
    protected $description = 'Fetch inventory item data from API and store in database';

    public function handle()
    {
        $apiKey = '315f9f6eb55fd6db9f87c0c0862007e0615ea467'; // Replace with your actual API key
        $locationIds = [
            '5fc4b12bc329204cb00b56bf'
        ];

        foreach ($locationIds as $locationId) {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey
            ])->get('https://api.mile.app/public/v1/warehouse/inventory-item', [
                'location_id' => $locationId,
                'limit' => -1,
                'page' => 1,
                'serial_number' => '',
                'rack' => '',
                'rack_type' => '',
                'start_date' => '',
                'end_date' => ''
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $items = $data['data'];

                foreach ($items as $item) {
                    // Find the corresponding inventory by code and location_id
                    $inventory = Inventory::where('code', $item['code'])->where('location_id', $locationId)->first();

                    if ($inventory) {
                        // Extract rack_type from the first word of rack
                        $rackType = explode(' ', $item['rack'])[0];

                        // Determine if the item exists in the current location's inventory items
                        $existingInventoryItem = InventoryItem::where('_id', $item['_id'])
                            ->where('inventory_id', $inventory->_id)
                            ->first();

                        if ($existingInventoryItem) {
                            // If it exists, update the existing record
                            $existingInventoryItem->update([
                                'serial_number' => $item['serial_number'] ?? null,
                                'rack' => $item['rack'] ?? null,
                                'rack_type' => $rackType ?? null,
                                'qty' => $item['qty'] ?? 0,
                                'status' => $item['status'] ?? null,
                                'receiving_date' => $item['receive_date'] ?? null,
                                'refNumber' => $item['refNumber'] ?? null,
                                'is_out' => $item['is_out'] ?? false,
                                'vendor_name' => $this->getVendorName($item, $locationId),
                                'updated_at' => $item['updated_at'] ?? now(),
                                'created_at' => $item['created_at'] ?? now()
                            ]);
                        } else {
                            // If it doesn't exist, create a new record
                            InventoryItem::create([
                                '_id' => $item['_id'],
                                'inventory_id' => $inventory->_id,
                                'serial_number' => $item['serial_number'] ?? null,
                                'rack' => $item['rack'] ?? null,
                                'rack_type' => $rackType ?? null,
                                'qty' => $item['qty'] ?? 0,
                                'status' => $item['status'] ?? null,
                                'receiving_date' => $item['receive_date'] ?? null,
                                'refNumber' => $item['refNumber'] ?? null,
                                'is_out' => $item['is_out'] ?? false,
                                'vendor_name' => $this->getVendorName($item, $locationId),
                                'updated_at' => $item['updated_at'] ?? now(),
                                'created_at' => $item['created_at'] ?? now()
                            ]);
                        }

                        Log::info('Inventory Item Processed: ', ['inventory_item' => $item['_id']]);
                    }
                }

                // After processing all API items, update current quantities in the stroke dies
                $this->updateAllCurrentQtys();

                // Log fetched inventory item IDs (for debugging purposes)
                Log::info('Fetched inventory item IDs for location ' . $locationId, array_keys($existingInventoryItems->toArray()));
            } else {
                Log::error('Failed to fetch inventory item data for location ' . $locationId, [
                    'response_body' => $response->body()
                ]);
            }
        }

        $this->info('Inventory item data fetched and stored successfully.');
    }

    private function getVendorName($item, $locationId)
    {
        $vendorName = $item['cutting_center'] ?? null;

        if (in_array($locationId, ['65a72c7fad782dc26a0626f6', '617bd0ad83ef510374337d84'])) {
            return $vendorName ?? 'SENOPATI';
        }

        if ($locationId == '6582ef8060c9390d890568d4') {
            return $vendorName ?? 'MKM';
        }

        return $vendorName;
    }

    private function updateAllCurrentQtys()
    {
        // Fetch all distinct inventory IDs from inventory items
        $inventoryIds = InventoryItem::distinct()->pluck('inventory_id');

        foreach ($inventoryIds as $inventoryId) {
            $inventory = Inventory::find($inventoryId);

            if ($inventory) {
                $this->updateCurrentQtyForInventory($inventory);
            }
        }
    }

    private function updateCurrentQtyForInventory($inventory)
    {
        // Find all stroke die entries related to this inventory item based on part_no
        $strokes = MstStrokeDies::where('part_no', $inventory->part_no)->get();

        foreach ($strokes as $stroke) {
            // Calculate the total accumulated quantity from inventory items for the current inventory since the cutoff_date
            $cutoffDate = $stroke->cutoff_date ? $stroke->cutoff_date : '1970-01-01'; // Default to epoch start if no cutoff_date is set

            // Sum the quantities of inventory items with status 'good' and receiving_date greater than or equal to cutoff_date
            $totalInventoryQty = InventoryItem::where('inventory_id', $inventory->_id)
                ->where('status', 'good') // Only include items with 'good' status
                ->where('receiving_date', '>=', $cutoffDate) // Filter by cutoff_date
                ->sum('qty');

            // Update current_qty only if the total inventory quantity is greater than the current_qty
            if ($totalInventoryQty > $stroke->current_qty) {
                $stroke->current_qty = $totalInventoryQty;
                $stroke->save();
                Log::info('Updated current_qty for stroke: ' . $stroke->id);
            } else {
                Log::info('No update needed for stroke: ' . $stroke->id);
            }
        }
    }
}
