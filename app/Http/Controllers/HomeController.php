<?php

namespace App\Http\Controllers;
use App\Models\MstStrokeDies;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
{
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
            'total_actual_production'
        )
        ->where('standard_stroke', '!=', 0) // Exclude records where standard_stroke is 0
        ->orderByRaw('total_actual_production > standard_stroke DESC') // Order by records that exceed standard stroke first
        ->orderByRaw('ABS(standard_stroke - total_actual_production) ASC') // Then order by closest to standard stroke
        ->limit(10)
        ->get();

    // Uncomment the dd() for debugging if needed
    // dd($data);

    return view('home.index', compact('data'));
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
