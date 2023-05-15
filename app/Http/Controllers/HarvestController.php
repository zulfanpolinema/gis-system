<?php

namespace App\Http\Controllers;

use App\Models\Harvest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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
                ->addColumn('gambar', function ($item) {
                    $images = '';
                    foreach ($item->images as $key => $image) {
                        $images .= '<div class="carousel-item '. ($key == 0 ? "active" : "") .'">
                                        <img class="d-block w-75 h-75 mx-auto" src="' . $image->pict . '" alt="'. $item->id .' slide">
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
                            </div>': 'Tidak ada gambar';
                })
                ->addColumn('pemilik', function ($item) {
                    return $item->user->name;
                })
                ->editColumn('total', function ($item) {
                    return number_format(intval($item->total), 0, ',', '.');
                })
                ->editColumn('price', function ($item) {
                    return 'Rp ' . number_format(intval($item->price), 0, ',', '.');
                })
                ->editColumn('address', function ($item) {
                    return $item->full_address;
                })
                ->addColumn('coordinate', function ($item) {
                    return 'lat: ' . $item->latitude . ', long:' . $item->longitude;
                })
                ->editColumn('status', function ($item) {
                    return '-';
                })
                ->addColumn('actions', function () {
                    return '-';
                })
                ->rawColumns(['gambar', 'status', 'actions'])
                ->addIndexColumn()
                ->make();
        }
        return view('admin.harvests.index');
    }

    public function create(Request $request)
    {
        $this->middleware('role:Admin|Petani');
        if (request()->ajax()) {
            try {
                if ($request->village_id) {
                    $village = \Indonesia::findVillage($request->village_id);
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
                'category_id'   => 'required',
                'village_id'    => 'required',
                'total'         => 'required',
                'price'         => 'required',
                'address'       => 'required',
                'latitude'      => 'required',
                'longitude'     => 'required',
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
                'status'    => 1,
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

    public function show(Harvest $harvest)
    {
        //
    }

    public function edit(Harvest $harvest)
    {
    }

    public function update(Request $request, Harvest $harvest)
    {
    }

    public function destroy(Harvest $harvest)
    {
    }
}
