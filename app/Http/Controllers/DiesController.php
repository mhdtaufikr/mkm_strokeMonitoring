<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PmFormHead;
use App\Models\BomDie;
use App\Models\PmFormDetail;
use App\Models\MstStrokeDies;
use App\Models\Dropdown;
use Illuminate\Support\Facades\DB;
use App\Models\Repair;
use App\Models\MtcOrder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class DiesController extends Controller
{
    public function index(){

        // Retrieve all ChecksheetFormHead records sorted by the newest data

        // Retrieve all machines with their op_name and location
        $item = PmFormHead::with('MstStrokeDies')->get();
        $dies = MstStrokeDies::get();

        return view('dies.index',compact('item','dies'));
    }
    public function checksheet(Request $request)
    {
        // Debugging statement: Remove in production


        // Create a variable to hold either `machine` or `no_machine`
        $machine = $request->mechine ?? $request->no_mechine;

        // Redirect to `checksheetAsset` route with `machine` parameter as `no_asset`
        return redirect()->route('apar.check.noasset', ['no_asset' => $machine]);
    }

    public function storeBom(Request $request)
    {
        $idDies = $request->id_dies;
        $items = $request->input('items');

        // Loop through each item and save it to the `bom_dies` table
        foreach ($items as $item) {
            BomDie::create([
                'id_dies' => $idDies,
                'name' => $item['name'],
                'size' => $item['size'],
                'qty' => $item['qty']
            ]);
        }

        return redirect()->back()->with('success', 'BOM items added successfully.');
    }


    public function checksheetAsset($no_asset)
    {
        // Find the asset based on `no_asset`
        $data = MstStrokeDies::where('asset_no', $no_asset)->first();
        $pm = PmFormHead::where('dies_id', $data->id)->orderBy('date', 'desc')->get();
        $repair = Repair::where('id_dies', $data->id)->orderBy('date', 'desc')->get();
        $bom = BomDie::where('id_dies',$data->id)->get();
        if (!$data) {
            // Redirect back if the asset is not found
            return redirect()->back()->with('failed', 'APAR information not found.');
        }

        // Return the view with the retrieved data
        return view('dies.dies', compact('data','pm','repair','bom'));
    }


    public function pm($id){
        $id = decrypt($id);
        $dieComponent = Dropdown::where('category','DIE COMPONENT')->get();
        $dieTools = Dropdown::where('category','DIE TOOLS')->get();
        $dieCast = Dropdown::where('category','DIE CASTING & PLATE')->get();
        $dieAsc = Dropdown::where('category','DIE ASSOSERIES')->get();

        return view('dies.pm',compact('dieComponent','dieTools','dieCast','id','dieAsc'));
    }

    public function repair($id){
        $id = decrypt($id);
        $id_req = null;
        $data = MstStrokeDies::where('id', $id)->first();
        return view('dies.repair',compact('id','data','id_req'));
    }

    public function repairReq($id,$id_req){
        $id = decrypt($id);
        $id_req = decrypt($id_req);
        $data = MstStrokeDies::where('id', $id)->first();
        return view('dies.repair',compact('id','data','id_req'));
    }

    public function storePM(Request $request)
{
    // Validate the incoming request data
    $request->validate([
        'id' => 'required|integer', // Assuming 'id' represents 'dies_id'
        'pic' => 'required|string|max:45',
        'signature' => 'required|string',
        'items' => 'required|array',
    ]);

    // Initialize image path variable
    $imgPath = null;

    try {
        DB::beginTransaction();

        // Handle image upload if provided
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('images');
            $file->move($destinationPath, $fileName);
            $imgPath = 'images/' . $fileName;
        }

        // Save to pm_form_heads table
        $pmFormHead = new PmFormHead();
        $pmFormHead->dies_id = $request->input('id'); // Store dies_id from request
        $pmFormHead->signature = $request->input('signature');
        $pmFormHead->date = now(); // or $request->input('date') if date is provided in request
        $pmFormHead->pic = $request->input('pic');
        $pmFormHead->img = $imgPath;
        $pmFormHead->remarks = $request->input('remarks');
        $pmFormHead->save();

        // Get the ID of the newly created pm_form_heads record
        $idHeader = $pmFormHead->id;

        // Loop through each item in the 'items' array and save to pm_form_details table
        foreach ($request->input('items') as $itemName => $itemData) {
            $pmFormDetail = new PmFormDetail();
            $pmFormDetail->id_header = $idHeader;
            $pmFormDetail->item_check = $itemName;
            $pmFormDetail->OK = isset($itemData['OK']) ? (int)$itemData['OK'] : 0;
            $pmFormDetail->NG = isset($itemData['NG']) ? (int)$itemData['NG'] : 0;
            $pmFormDetail->remarks = $itemData['remarks'] ?? null;
            $pmFormDetail->save();
        }

        // Retrieve `no_asset` and update stroke die's current_qty and cutoff_date
        $die = MstStrokeDies::findOrFail($request->id);
        $no_asset = $die->asset_no;

        // Reset the current quantity to 0 and set the cutoff date to current timestamp
        $die->current_qty = 0;
        $die->cutoff_date = now();
        $die->save();

        DB::commit();

        // Redirect to the specified route `apar.check.noasset` with `no_asset` parameter
        return redirect()->route('apar.check.noasset', ['no_asset' => $no_asset])
                         ->with('status', 'Dies checklist submitted successfully and quantity reset.');

    } catch (\Exception $e) {
        // Rollback the transaction if an exception occurs
        DB::rollBack();

        // Log the exception for debugging purposes
        Log::error('Error storing dies checklist: ' . $e->getMessage());

        // Redirect back with an error message
        return redirect()->back()->withErrors(['failed' => 'Failed to submit dies checklist. Please try again.']);
    }
}



public function storeRepair(Request $request)
{
    // Validate incoming request data
    $request->validate([
        'id_dies' => 'required|integer',
        'id_order' => 'required|integer',
        'pic' => 'required|string|max:45',
        'date' => 'required|date',
        'problem' => 'required|string',
        'action' => 'required|string',
        'start_time' => 'required',
        'end_time' => 'required',
        'status' => 'required|string|max:45',
        'signature' => 'required|string',
    ]);

    try {
        DB::beginTransaction();

        // Handle image uploads for `img_before` and `img_after`
        $imgBeforePath = null;
        if ($request->hasFile('img_before')) {
            $imgBefore = $request->file('img_before');
            $imgBeforeName = uniqid() . '_' . $imgBefore->getClientOriginalName();
            $imgBefore->move(public_path('images'), $imgBeforeName);
            $imgBeforePath = 'images/' . $imgBeforeName;
        }

        $imgAfterPath = null;
        if ($request->hasFile('img_after')) {
            $imgAfter = $request->file('img_after');
            $imgAfterName = uniqid() . '_' . $imgAfter->getClientOriginalName();
            $imgAfter->move(public_path('images'), $imgAfterName);
            $imgAfterPath = 'images/' . $imgAfterName;
        }

        // Parse start_time and end_time to datetime
        $startTime = $request->input('date') . ' ' . $request->input('start_time') . ':00';
        $endTime = $request->input('date') . ' ' . $request->input('end_time') . ':00';

        // Save repair data to repairs table
        $repair = new Repair();
        $repair->id_dies = $request->input('id_dies');
        $repair->pic = $request->input('pic');
        $repair->date = $request->input('date');
        $repair->problem = $request->input('problem');
        $repair->action = $request->input('action');
        $repair->start_time = $startTime;
        $repair->end_time = $endTime;
        $repair->remarks = $request->input('remarks');
        $repair->status = $request->input('status');
        $repair->signature = $request->input('signature');
        $repair->img_before = $imgBeforePath;
        $repair->img_after = $imgAfterPath;
        $repair->id_order = $request->input('id_order'); // Add id_order to the repair record
        $repair->save();

        // Update the status of the corresponding order in mtc_orders
        MtcOrder::where('id', $request->input('id_order'))->update(['status' => '1']);

        // Retrieve the `no_asset` from `MstStrokeDies` based on `id_dies`
        $die = MstStrokeDies::findOrFail($request->id_dies);
        $no_asset = $die->asset_no;

        DB::commit();

        // Redirect to the specified route `apar.check.noasset` with `no_asset` parameter
        return redirect()->route('apar.check.noasset', ['no_asset' => $no_asset])
                         ->with('status', 'Repair record saved successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error storing repair record: ' . $e->getMessage());
        return redirect()->back()->withErrors(['failed' => 'Failed to save repair record. Please try again.']);
    }
}




public function addImage(Request $request)
{
    $request->validate([
        'id' => 'required|exists:mst_strokedies,id', // Ensure die ID exists
        'new_images.*' => 'file|mimes:jpeg,png,jpg,gif|max:10000', // Validate each image
    ]);

    $die = MstStrokeDies::findOrFail($request->id);
    $imagePaths = $die->img ? json_decode($die->img, true) : [];

    if ($request->hasFile('new_images')) {
        foreach ($request->file('new_images') as $file) {
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('image/dies');
            $file->move($destinationPath, $fileName);

            // Update the path to reflect the public folder location
            $imagePaths[] = 'image/dies/' . $fileName;
        }
        $die->img = json_encode($imagePaths);
        $die->save();
    }

    $no_asset = $die->asset_no;

    // Redirect to the specified route `apar.check.noasset` with `no_asset` parameter
    return redirect()->route('apar.check.noasset', ['no_asset' => $no_asset])
                     ->with('status', 'Images uploaded successfully.');
}




public function deleteImage(Request $request)
{
    $request->validate([
        'id' => 'required|exists:mst_strokedies,id',
        'img_path' => 'required|string'
    ]);

    $die = MstStrokeDies::findOrFail($request->id);
    $imagePaths = json_decode($die->img, true) ?? [];

    // Remove the image from the list and delete the file
    if (($key = array_search($request->img_path, $imagePaths)) !== false) {
        unset($imagePaths[$key]);
        Storage::delete(str_replace('storage/', 'public/', $request->img_path));
        $die->img = json_encode(array_values($imagePaths));
        $die->save();
    }

    $no_asset = $die->asset_no;

    // Redirect to `checksheetAsset` route with `no_asset` parameter
    return redirect()->route('apar.check.noasset', ['no_asset' => $no_asset])
                     ->with('success', 'Image deleted successfully.');
}


public function pmDetail($id)
{
    $id = decrypt($id);
    $pmHead = PmFormHead::where('id', $id)->first();
    $data = MstStrokeDies::where('id', $pmHead->dies_id)->first();

    // Retrieve pmDetail items and group them by category
    $pmDetail = PmFormDetail::where('id_header', $pmHead->id)
        ->with('dropdown')  // Ensure we load the dropdown relationship
        ->get()
        ->groupBy(function($detail) {
            return $detail->dropdown->category ?? 'Uncategorized';
        });

    return view('dies.detailPM', compact('pmHead', 'pmDetail', 'data'));
}


public function update(Request $request, $id)
{
    // Validate the incoming request
    $request->validate([
        'asset_no' => 'required|string|max:45',
        'part_name' => 'required|string|max:45',
        'code' => 'nullable|string|max:50',
        'part_no' => 'nullable|string|max:255',
        'process' => 'nullable|string|max:100',
        'std_stroke' => 'nullable|integer',
        'current_qty' => 'nullable|integer',
        'cutoff_date' => 'nullable|date',
        'classification' => 'nullable|string|max:45',
        'status' => 'nullable|string|max:45',
        'height' => 'nullable|numeric',
        'width' => 'nullable|numeric',
        'length' => 'nullable|numeric',
        'weight' => 'nullable|numeric',
        'remarks' => 'nullable|string',
    ]);

    // Find the asset record by ID
    $asset = MstStrokeDies::findOrFail($id);

    // Manually update each field
    $asset->asset_no = $request->input('asset_no');
    $asset->part_name = $request->input('part_name');
    $asset->code = $request->input('code');
    $asset->part_no = $request->input('part_no');
    $asset->process = $request->input('process');
    $asset->std_stroke = $request->input('std_stroke');
    $asset->current_qty = $request->input('current_qty');
    $asset->cutoff_date = $request->input('cutoff_date');
    $asset->classification = $request->input('classification');
    $asset->status = $request->input('status');
    $asset->height = $request->input('height');
    $asset->width = $request->input('width');
    $asset->length = $request->input('length');
    $asset->weight = $request->input('weight');
    $asset->remarks = $request->input('remarks');

    // Save the updated asset record
    $asset->save();

    // Get the `no_asset` value for redirection
    $no_asset = $asset->asset_no;

    // Redirect to the `apar.check.noasset` route with `no_asset` parameter
    return redirect()->route('apar.check.noasset', ['no_asset' => $no_asset])
                     ->with('status', 'Data updated successfully.');
}



}
