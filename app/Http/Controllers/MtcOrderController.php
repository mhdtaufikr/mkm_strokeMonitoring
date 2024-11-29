<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MtcOrder;
use App\Models\MstStrokeDies;
use App\Mail\MaintenanceOrderNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class MtcOrderController extends Controller
{
    public function index()
{
    $currentDate = Carbon::now()->toDateString();
    $items = MtcOrder::with(['dies', 'repair'])
        ->orderByRaw("
            CASE
                WHEN date >= ? THEN 0
                WHEN status IS NULL THEN 1
                ELSE 2
            END,
            ABS(DATEDIFF(date, ?))
        ", [$currentDate, $currentDate])
        ->orderBy('date', 'asc')
        ->get();
    $distinctPartNames = MstStrokeDies::select('part_name')->distinct()->orderBy('part_name', 'asc')->pluck('part_name');
    $dies = MstStrokeDies::get();

$distinctCodes = MstStrokeDies::select('code')->distinct()->pluck('code');


    return view('order.index', compact('items', 'distinctPartNames','dies','distinctCodes'));
}



public function getProcessByCode(Request $request)
{
    $code = $request->query('code');

    // Fetch distinct processes based on the code
    $processItems = MstStrokeDies::where('code', $code)
        ->select('process')
        ->distinct()
        ->get();

    return response()->json($processItems);
}



public function store(Request $request)
{

    // Debugging Request Data
    // dd($request->all());

    // Validate incoming request data
    $request->validate([
        'orders.*.code' => 'required',
        'orders.*.process' => 'required',
        'orders.*.problem' => 'required',
        'orders.*.date' => 'required|date',
        'orders.*.pic' => 'required|string',
        'orders.*.img' => 'nullable|image',
    ]);

    $ordersData = []; // Array to hold order data for email

    foreach ($request->orders as $index => $order) {

        // Retrieve the related `MstStrokeDies` record based on selected `code` and `process`
        $dies = MstStrokeDies::where('code', $order['code'])
            ->where('process', $order['process'])
            ->first();

        if (!$dies) {
            return redirect()->back()->withErrors([
                "orders.$index.code" => "Invalid code or process combination.",
            ]);
        }

        // Handle the image upload if it exists
        $imgPath = null;
        if (isset($order['img']) && $order['img']->isValid()) {
            $file = $order['img'];
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('image/mtc_order');
            $file->move($destinationPath, $fileName);
            $imgPath = 'image/mtc_order/' . $fileName;
        }

        // Create a new maintenance order
        $mtcOrder = MtcOrder::create([
            'id_dies' => $dies->id,
            'problem' => $order['problem'],
            'pic' => $order['pic'],
            'img' => $imgPath,
            'date' => $order['date'],
        ]);

        // Collect order data for email
        $ordersData[] = [
            'part_name' => $dies->part_name,
            'code' => $order['code'],
            'code_process' => "{$dies->code} - {$dies->process}",
            'process' => $order['process'],
            'problem' => $order['problem'],
            'date' => $order['date'],
            'pic' => $order['pic'],
            'img' => $imgPath,
            'id_dies' => $mtcOrder->id_dies,
            'order_id' => $mtcOrder->id,
        ];
    }

    // Send email notification (optional)
    if (!empty($ordersData)) {
        Mail::to('prasetyo@ptmkm.co.id')->send(new MaintenanceOrderNotification($ordersData));
    }

    return redirect()->back()->with('status', 'Maintenance orders added successfully.');
}

public function storeScan(Request $request)
{
    $request->validate([
        'asset_no' => 'required|string|exists:mst_strokedies,asset_no',
        'date' => 'required|date',
        'problem' => 'required|string',
        'pic' => 'required|string',
        'img' => 'nullable|image',
    ]);

    $imgPath = null;
    if ($request->hasFile('img') && $request->file('img')->isValid()) {
        $file = $request->file('img');
        $fileName = uniqid() . '_' . $file->getClientOriginalName();
        $file->move(public_path('image/mtc_order'), $fileName);
        $imgPath = 'image/mtc_order/' . $fileName;
    }

    $dies = MstStrokeDies::where('asset_no', $request->asset_no)->firstOrFail();

    $mtcOrder = MtcOrder::create([
        'id_dies' => $dies->id,
        'problem' => $request->problem,
        'pic' => $request->pic,
        'img' => $imgPath,
        'date' => $request->date,
    ]);

    // Collect order data for email
    $ordersData[] = [
        'part_name' => $dies->part_name,
        'code' => $dies->code,
        'code_process' => "{$dies->code} - {$dies->process}",
        'process' => $dies->process,
        'problem' => $request->problem,
        'date' => $request->date,
        'pic' => $request->pic,
        'img' => $imgPath,
        'id_dies' => $mtcOrder->id_dies,
        'order_id' => $mtcOrder->id,
    ];

    // Send email notification (optional)
    if (!empty($ordersData)) {
        Mail::to('muhammad.taufik@ptmkm.co.id')->send(new MaintenanceOrderNotification($ordersData));
    }

    return redirect()->back()->with('status', 'Maintenance order submitted successfully.');
}

public function destroy($id)
{
    try {
        $order = MtcOrder::findOrFail($id); // Find the order by ID
        $order->delete(); // Delete the order

       return redirect()->back()->with('status', 'Mtc Order deleted successfully.');
    } catch (Exception $e) {
       return redirect()->back()->with('Failed', 'Failed to delete Mtc Order.');
    }
}


}
