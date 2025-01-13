<?php

namespace App\Http\Controllers;
use App\Models\MstStrokeDies;
use App\Models\MtcOrder;
use App\Models\TaskList;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
{
    $criticalData = DB::table('view_stroke_comparison')
    ->select(
        'stroke_id',
        'stroke_code',
        'stroke_part_no',
        'stroke_process',
        'inventory_part_no',
        'inventory_name',
        'standard_stroke',
        'total_actual_production',
        'reminder_stroke'
    )
    ->where('classification', 'Critical') // Include only "Critical" classification
    ->where('standard_stroke', '!=', 0) // Exclude records where standard_stroke is 0
    ->where('total_actual_production', '>', 0) // Exclude records where total_actual_production is 0
    ->orderByRaw('total_actual_production > standard_stroke DESC') // Sort by whether it exceeds standard stroke
    ->orderByRaw('ABS(standard_stroke - total_actual_production) ASC') // Then sort by closest to standard stroke
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
            'total_actual_production',
            'reminder_stroke'
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
            'total_actual_production',
            'reminder_stroke'
        )
        ->where('classification', 'Normal')
        ->where('standard_stroke', '!=', 0)
        ->orderByRaw('total_actual_production > standard_stroke DESC')
        ->orderByRaw('ABS(standard_stroke - total_actual_production) ASC')
        ->limit(10)
        ->get();


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
        ->where('total_actual_production', '>', 0) // Exclude records where total_actual_production is 0
        ->orderByRaw('ABS(total_actual_production - reminder_stroke) ASC') // 1. Closest to reminder stroke
        ->orderByRaw('total_actual_production > standard_stroke DESC') // 2. Exceeding standard stroke comes first
        ->orderByRaw('ABS(total_actual_production - standard_stroke) ASC') // 3. Closest to standard stroke for ties
        ->limit(10)
        ->get();


    $currentDate = Carbon::now()->toDateString();
    $items = MtcOrder::with([
        'dies',
        'repair',
        'dies.inventory' // Include inventory relationship based on part_no
    ])
    ->whereNull('status') // Select rows where status is NULL
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
// Fetch all names from the dropdowns table where category is 'Names'
$names = DB::table('dropdowns')->where('category', 'Names')->pluck('name_value');

// Map names to tasks, adding default values if no task is found
$tasklists = $names->map(function ($name) {
    // Fetch the latest task for the name
    $task = TaskList::where('name', $name)->latest('created_at')->first();

    if ($task) {
        // Return the task if it exists
        return $task;
    }

    // Return default values if no task is found
    return (object) [
        'name' => $name,
        'job' => 'No Job Assigned',
        'description' => 'No Description Available',
        'start_date' => null,
        'end_date' => null,
        'status' => 'No Status',
    ];
});


// Fetch distinct part names for dropdown
$distinctPartNames = MstStrokeDies::select('part_name')
    ->distinct()
    ->orderBy('part_name', 'asc')
    ->pluck('part_name');

return view('home.index', compact('criticalData', 'hardWorkData', 'normalData', 'data', 'items', 'distinctPartNames', 'tasklists'));
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

public function dashboard()
{
    $year = now()->year; // Ambil tahun saat ini

    $topRepairs = DB::table('repairs')
    ->join('mst_strokedies', 'repairs.id_dies', '=', 'mst_strokedies.id')
    ->select(
        'mst_strokedies.code',          // Include code
        DB::raw('COUNT(repairs.id) as total_repairs') // Total repairs count
    )
    ->groupBy('mst_strokedies.code')   // Group by code only
    ->orderByDesc('total_repairs')
    ->limit(10)
    ->get();
    // **Query: Top 10 MTTR**
    $topMTTR = DB::table('repair_summary_accumulated')
    ->select('code', DB::raw('MAX(mttr_value) as mttr_value'), DB::raw('SUM(problem_count) as problem_count'), DB::raw('SUM(total_repair_time) as total_repair_time'))
    ->groupBy('code') // Group by code to avoid duplicates
    ->orderByDesc('mttr_value') // Sort by highest MTTR
    ->limit(10) // Limit to top 10
    ->get();


    // **Return ke View**
    return view('home.dashboard', [
        'year' => $year,              // Tahun saat ini
        'topRepairs' => $topRepairs,  // Data Dies yang paling banyak diperbaiki
        'topMTTR' => $topMTTR,        // Data Dies dengan MTTR tertinggi
    ]);
}

}
