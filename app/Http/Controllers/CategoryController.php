<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $categories = Category::get();
            return DataTables::of($categories)
                ->editColumn('created_at', function ($item) {
                    return $item->created_at->diffForHumans();
                })
                ->addColumn('actions', function ($item) {
                    return
                        '
                            <nobr>
                            <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit" id="editButton" data-id="' . $item->id . '" data-toggle="modal" data-target="#editKategoriModal">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>
                            <button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete" id="deleteButton" data-id="' . $item->id . '">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                            </nobr>
                        ';
                })
                ->rawColumns(['actions'])
                ->addIndexColumn()
                ->make();
        }
        return view('admin.categories.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name'  => 'required|unique:categories,name'
            ], [
                'name.required' => 'Nama tidak boleh kosong!',
                'name.unique'   => 'Nama sudah digunakan!',
            ]);
            Category::create([
                'name'  => $request->name,
            ]);
            DB::commit();
            return response()->json(['message' => 'Data berhasil disimpan!'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function show(Category $category)
    {
        //
    }

    public function edit($id)
    {
        try {
            $category = Category::findOrFail($id);
            return response()->json($category, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $category = Category::findOrFail($id);
            $category->update([
                'name' => $request->name ?? $category->name,
            ]);
            DB::commit();
            return response()->json(['message' => 'Data berhasil diupdate!'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Category::findOrFail($id)->delete();
            DB::commit();
            return response()->json(['message' => 'Data berhasil dihapus!'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
