<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch the top 10 records closest to the standard stroke and exclude records where standard_stroke is 0
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
            ->orderByRaw('ABS(standard_stroke - total_actual_production) ASC') // Order by closest to standard stroke
            ->limit(10)
            ->get();

        return view('home.index', compact('data'));
    }


}
