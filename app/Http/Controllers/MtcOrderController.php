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

    return view('order.index', compact('items', 'distinctPartNames'));
}



    public function getCodeProcess(Request $request)
{
    $partName = $request->query('part_name');

    // Fetch distinct `code` and `process` based on `part_name`
    $codeProcessItems = MstStrokeDies::where('part_name', $partName)
        ->select('id', 'code', 'process')
        ->distinct()
        ->get();

    return response()->json($codeProcessItems);
}



public function store(Request $request)
{
    $request->validate([
        'orders.*.part_name' => 'required',
        'orders.*.code_process' => 'required',
        'orders.*.problem' => 'required',
        'orders.*.date' => 'required|date',
        'orders.*.img' => 'nullable|image',
    ]);

    $ordersData = []; // Array to hold order data for email

    foreach ($request->orders as $index => $order) {
        // Retrieve the related `MstStrokeDies` record based on selected `code_process`
        $dies = MstStrokeDies::findOrFail($order['code_process']);

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
            'img' => $imgPath,
            'date' => $order['date'],
        ]);

        // Collect order data for email
        $ordersData[] = [
            'part_name' => $dies->part_name,
            'code_process' => "{$dies->code} - {$dies->process}",
            'problem' => $order['problem'],
            'date' => $order['date'],
            'img' => $imgPath,
            'id_dies' => $mtcOrder->id_dies,
            'order_id' => $mtcOrder->id,
        ];
    }

    // Send email notification
    Mail::to('muhammad.taufik@ptmkm.co.id')->send(new MaintenanceOrderNotification($ordersData));

    return redirect()->back()->with('status', 'Maintenance orders added successfully.');
}

}
