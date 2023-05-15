<?php

namespace App\Http\Controllers;

use App\Models\HarvestImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HarvestImageController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $file = $request->file('file');
            if ($file) {
                $name = uniqid() . '_' . trim($file->getClientOriginalName());

                $file_path = $file->storeAs('harvests', $name, 'public');

                return response()->json([
                    'name'          => $file_path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function show(HarvestImage $harvestImage)
    {
        //
    }

    public function edit(HarvestImage $harvestImage)
    {
        //
    }

    public function update(Request $request, HarvestImage $harvestImage)
    {
        //
    }

    public function destroy(HarvestImage $harvestImage)
    {
        //
    }
}
