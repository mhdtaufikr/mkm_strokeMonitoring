<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskList;
use Illuminate\Support\Facades\DB;
use App\Models\MtcOrder;

class TaskController extends Controller
{
    public function index()
    {
        // Fetch all task list data
        $tasklists = TaskList::all();

        // Fetch all names from the dropdowns table where category is 'Names'
        $names = DB::table('dropdowns')->where('category', 'Names')->pluck('name_value');

        // Fetch MTC Order data for dropdown (if user selects "Repair")
        $currentDate = now()->format('Y-m-d');
        $repairItems = MtcOrder::with(['dies', 'repair', 'dies.inventory'])
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

        // Fetch PM List data for dropdown (if user selects "PM List")
        $pmListItems = DB::table('view_stroke_comparison')
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

        // Pass data to the view
        return view('task.index', compact('tasklists', 'names', 'repairItems', 'pmListItems'));
    }
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'job' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|string|in:Open,Close',
        ]);

        // Simpan data ke tabel tasklists
        TaskList::create([
            'name' => $request->name ?? 'Unknown', // Nama default jika tidak diisi
            'job' => $request->job,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('status', 'Tasklist added successfully.');
    }

    public function update(Request $request, $id)
{
    // Validasi input
    $request->validate([
        'name' => 'required|string|max:255',
        'job' => 'required|string',
        'description' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'status' => 'required|string|in:Open,Close',
    ]);

    // Cari task berdasarkan ID
    $task = TaskList::findOrFail($id);

    // Update data
    $task->update([
        'name' => $request->name,
        'job' => $request->job,
        'description' => $request->description,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'status' => $request->status,
    ]);

    // Redirect kembali dengan pesan sukses
    return redirect()->back()->with('status', 'Tasklist updated successfully.');
}

public function destroy($id)
{
    // Cari task berdasarkan ID
    $task = TaskList::findOrFail($id);

    // Hapus task
    $task->delete();

    // Redirect kembali dengan pesan sukses
    return redirect()->back()->with('status', 'Tasklist deleted successfully.');
}


}
