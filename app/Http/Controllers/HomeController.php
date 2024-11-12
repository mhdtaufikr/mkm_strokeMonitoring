<?php

namespace App\Http\Controllers;
use App\Models\MstStrokeDies;
use App\Models\MtcOrder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
{
    // Fetch the top 10 records for each classification where total_actual_production exceeds standard_stroke, then sort by closest to the standard stroke
    $criticalData = DB::table('view_stroke_comparison')
        ->select(
            'stroke_id',
            'stroke_code',
            'stroke_part_no',
            'stroke_process',
            'inventory_part_no',
            'inventory_name',
            'standard_stroke',
            'total_actual_production'
        )
        ->where('classification', 'Critical')
        ->where('standard_stroke', '!=', 0)
        ->orderByRaw('total_actual_production > standard_stroke DESC')
        ->orderByRaw('ABS(standard_stroke - total_actual_production) ASC')
        ->limit(10)
        ->get();

    $hardWorkData = DB::table('view_stroke_comparison')
        ->select(
            'stroke_id',
            'stroke_code',
            'stroke_part_no',
            'stroke_process',
            'inventory_part_no',
            'inventory_name',
            'standard_stroke',
            'total_actual_production'
        )
        ->where('classification', 'Hard Work')
        ->where('standard_stroke', '!=', 0)
        ->orderByRaw('total_actual_production > standard_stroke DESC')
        ->orderByRaw('ABS(standard_stroke - total_actual_production) ASC')
        ->limit(10)
        ->get();

    $normalData = DB::table('view_stroke_comparison')
        ->select(
            'stroke_id',
            'stroke_code',
            'stroke_part_no',
            'stroke_process',
            'inventory_part_no',
            'inventory_name',
            'standard_stroke',
            'total_actual_production'
        )
        ->where('classification', 'Normal')
        ->where('standard_stroke', '!=', 0)
        ->orderByRaw('total_actual_production > standard_stroke DESC')
        ->orderByRaw('ABS(standard_stroke - total_actual_production) ASC')
        ->limit(10)
        ->get();


         // Fetch the top 10 records where total_actual_production exceeds standard_stroke first, then sort by closest to the standard stroke
    $data = DB::table('view_stroke_comparison')
    ->select(
        'stroke_id',
        'stroke_code',
        'stroke_part_no',
        'stroke_process',
        'inventory_part_no',
        'inventory_name',
        'standard_stroke',
        'total_actual_production',
        'classification',
        'reminder_stroke'
    )
    ->where('standard_stroke', '!=', 0) // Exclude records where standard_stroke is 0
    ->orderByRaw('total_actual_production > standard_stroke DESC') // Order by records that exceed standard stroke first
    ->orderByRaw('ABS(standard_stroke - total_actual_production) ASC') // Then order by closest to standard stroke
    ->limit(10)
    ->get();
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

    return view('home.index', compact('criticalData', 'hardWorkData', 'normalData','data','items','distinctPartNames'));
}


public function resetQty($strokeId)
{
    // Find the stroke die entry by its ID
    $stroke = MstStrokeDies::findOrFail($strokeId);

    // Reset the current quantity to 0
    $stroke->current_qty = 0;
    $stroke->cutoff_date = now(); // Set the cutoff date to current timestamp
    $stroke->save();

    // Redirect back with a success message
    return redirect()->route('home')->with('success', 'Quantity reset successfully.');
}



}
