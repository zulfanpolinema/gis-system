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

                $file->storeAs('harvests', $name, 'public');

                return response()->json([
                    'name'          => $name,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HarvestImage  $harvestImage
     * @return \Illuminate\Http\Response
     */
    public function show(HarvestImage $harvestImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HarvestImage  $harvestImage
     * @return \Illuminate\Http\Response
     */
    public function edit(HarvestImage $harvestImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HarvestImage  $harvestImage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HarvestImage $harvestImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HarvestImage  $harvestImage
     * @return \Illuminate\Http\Response
     */
    public function destroy(HarvestImage $harvestImage)
    {
        //
    }
}
