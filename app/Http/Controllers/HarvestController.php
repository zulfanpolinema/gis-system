<?php

namespace App\Http\Controllers;

use App\Models\Harvest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Laravolt\Indonesia\Models\Village;
use Yajra\DataTables\Facades\DataTables;

class HarvestController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Admin|Petani', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        if (request()->ajax()) {
            $harvests = Harvest::with('user')->get();
            return DataTables::of($harvests)
                ->addColumn('category', function ($item) {
                    return $item->category->name;
                })
                ->addColumn('gambar', function ($item) {
                    $images = '';
                    foreach ($item->images as $key => $image) {
                        $images .= '<div class="carousel-item ' . ($key == 0 ? "active" : "") . '">
                                        <img class="d-block w-75 h-75 mx-auto" src="' . $image->pict . '" alt="' . $item->id . ' slide">
                                    </div>';
                    }
                    return $images != '' ? '<div id="carousel-' . $item->id . '" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">' .
                        $images
                        . '</div>
                                <a class="carousel-control-prev" href="#carousel-' . $item->id . '" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carousel-' . $item->id . '" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>' : 'Tidak ada gambar';
                })
                ->addColumn('pemilik', function ($item) {
                    return $item->user->name;
                })
                ->editColumn('total', function ($item) {
                    return number_format(intval($item->total), 0, ',', '.');
                })
                ->editColumn('price', function ($item) {
                    return 'Ecer : Rp ' . number_format(intval($item->price), 0, ',', '.')
                    . '<br> Grosir : Rp ' . number_format(intval($item->retail_price), 0, ',', '.');
                })
                ->editColumn('address', function ($item) {
                    return $item->full_address;
                })
                ->addColumn('coordinate', function ($item) {
                    return '<a class="text-primary" id="showLocation" data-id="' . $item->id . '"  data-toggle="modal" data-target="#showLocationModal">' . $item->latitude . ', ' . $item->longitude . '</a>';
                })
                ->editColumn('phonenumber', function ($item) {
                    if ($item->phonenumber) {
                        $phonenumber = $item->phonenumber;
                        if ($phonenumber[0] == "0") {
                            $phonenumber = substr($phonenumber, 1);
                        }

                        if ($phonenumber[0] == "8") {
                            $phonenumber = "62" . $phonenumber;
                        }
                        return '<a href="https://wa.me/' . $phonenumber . '" target="_blank">' . $item->phonenumber . '</a>';
                    } else {
                        return '-';
                    }
                })
                ->addColumn('actions', function ($item) {
                    $buttons = '
                            <button class="btn btn-xs btn-default text-success mx-1 shadow" title="Order" id="addTransactionButton" data-id="' . $item->id . '" data-toggle="modal" data-target="#addTransactionModal" data-price="'. $item->price .'" data-retail_minimum="'. $item->retail_minimum .'" data-retail_price="'. $item->retail_price .'">
                                <i class="fa fa-lg fa-fw fa-shopping-cart"></i>
                            </button>
                            ';
                    if (auth()->user()->hasRole('Admin') || $item->user->id == auth()->user()->id) {
                        $buttons .= '
                            <a href="' . route('harvests.edit', $item->id) . '" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </a>
                            <button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete" id="deleteButton" data-id="' . $item->id . '">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                            </nobr>
                        ';
                    }
                    return $buttons;
                })
                ->rawColumns(['coordinate', 'gambar', 'price', 'phonenumber', 'actions'])
                ->addIndexColumn()
                ->make();
        }
        return view('admin.harvests.index');
    }

    public function create(Request $request)
    {
        if (request()->ajax()) {
            try {
                if ($request->village_id) {
                    $village = Village::find($request->village_id);
                    return response()->json($village, 200);
                }
            } catch (\Throwable $th) {
                return response()->json(['message' => $th->getMessage()], 500);
            }
        }
        return view('admin.harvests.create');
    }

    public function store(Request $request)
    {
        $this->middleware('role:Admin|Petani');
        DB::beginTransaction();
        try {
            if (Auth::user()->hasRole('Admin')) {
                $request->validate(['user_id' => 'required']);
            }
            $request->validate([
                'category_id'       => 'required',
                'village_id'        => 'required',
                'total'             => 'required',
                'price'             => 'required',
                'address'           => 'required',
                'latitude'          => 'required',
                'longitude'         => 'required',
                'phonenumber'       => 'required',
                'retail_price'      => 'required',
                'retail_minimum'    => 'required',
                'harvest_month'     => 'required',
            ]);

            $harvest = Harvest::create([
                'user_id'   => $request->user_id ?? Auth::user()->id,
                'category_id'   => $request->category_id,
                'indonesia_village_id'  => $request->village_id,
                'total' => $request->total,
                'price' => $request->price,
                'address'   => $request->address,
                'longitude' => $request->longitude,
                'latitude'  => $request->latitude,
                'phonenumber'   => $request->phonenumber,
                'retail_price'  => $request->retail_price,
                'retail_minimum'  => $request->retail_minimum,
                'harvest_month'  => $request->harvest_month,
            ]);

            foreach ($request->input('gambar', []) as $path) {
                $harvest->images()->create([
                    'path' => $path
                ]);
            }

            DB::commit();
            Session::flash('success', 'Data berhasil disimpan!');
            return response()->json(['message' => route('harvests.index')], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function show($id)
    {
        if (request()->ajax()) {
            $harvest = Harvest::select('latitude', 'longitude')->find($id);
            return response()->json($harvest, 200);
        }
    }

    public function edit($id)
    {
        $harvest = Harvest::find($id);
        if (!auth()->user()->hasRole('Admin') && auth()->user()->id != $harvest->user->id) {
            Session::flash('error', 'Data panen ini bukan milik anda!');
            return redirect()->back();
        }
        return view('admin.harvests.create', compact('harvest'));
    }

    public function update(Request $request, $id)
    {
        $this->middleware('role:Admin|Petani');
        DB::beginTransaction();
        try {
            $harvest = Harvest::find($id);
            $harvest->update([
                'user_id'               => $request->user_id ?? $harvest->user_id,
                'category_id'           => $request->category_id ?? $harvest->category_id,
                'indonesia_village_id'  => $request->village_id ?? $harvest->indonesia_village_id,
                'total'                 => $request->total ?? $harvest->total,
                'price'                 => $request->price ?? $harvest->price,
                'address'               => $request->address ?? $harvest->address,
                'longitude'             => $request->longitude ?? $harvest->longitude,
                'latitude'              => $request->latitude ?? $harvest->latitude,
                'phonenumber'           => $request->phonenumber ?? $harvest->latitude,
                'retail_price'          => $request->retail_price ?? $harvest->retail_price,
                'retail_minimum'        => $request->retail_minimum ?? $harvest->retail_minimum,
                'harvest_month'         => $request->harvest_month ?? $harvest->harvest_month,
            ]);

            DB::commit();
            Session::flash('success', 'Data berhasil disimpan!');
            return response()->json(['message' => route('harvests.index')], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $harvest = Harvest::findOrFail($id);
            if (!auth()->user()->hasRole('Admin') && auth()->user()->id != $harvest->user->id) {
                return response()->json(['message' => 'Data panen ini bukan milik anda!'], 500);
            }
            foreach ($harvest->images as $image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
            $harvest->delete();
            DB::commit();
            return response()->json(['message' => 'Data berhasil dihapus!'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
