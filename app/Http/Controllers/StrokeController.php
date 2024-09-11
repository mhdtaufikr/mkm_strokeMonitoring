<?php

namespace App\Http\Controllers;

use App\Models\MstStrokeDies;
use DataTables;
use Illuminate\Http\Request;

class StrokeController extends Controller
{
    public function index()
    {
        return view('stroke.index');
    }

    public function getStrokeDiesData(Request $request)
    {
        if ($request->ajax()) {
            $data = MstStrokeDies::select('id', 'code', 'part_no', 'process', 'std_stroke','classification','current_qty','cutoff_date');
            return DataTables::of($data)
                ->addIndexColumn() // Adds a row number (index)
                ->toJson(); // Returns the data in JSON format
        }
    }

    public function edit($id)
    {
        $item = MstStrokeDies::find($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:50',
            'part_no' => 'required|string|max:255',
            'process' => 'required|string|max:100',
            'std_stroke' => 'required|integer',
            'current_qty' => 'required|integer',
            'classification' => 'required|string|max:45',
        ]);

        $item = MstStrokeDies::find($id);
        $item->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Record updated successfully!'
        ]);
    }


}


