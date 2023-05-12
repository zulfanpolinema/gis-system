<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $roles = Role::all();
            return DataTables::of($roles)
                ->editColumn('created_at', function ($item) {
                    return $item->created_at->diffForHumans();
                })
                ->addColumn('permissions', function ($item) {
                    return $item->permissions->pluck('name')->implode(', ');
                })
                ->addColumn('actions', function ($item) {
                    return
                        '
                            <nobr>
                            <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit" id="editButton" data-id="' . $item->id . '" data-toggle="modal" data-target="#editRoleModal">
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
        $permissions = Permission::all();
        return view('admin.roles.index', compact('permissions'));
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
                'name'  => 'required|unique:roles,name',
            ], [
                'name.required' => 'Masukkan nama role!',
                'name.unique'   => 'Nama role sudah digunakan!',
            ]);
            $role = Role::create([
                'name'  => $request->name,
            ]);

            if ($request->permissions) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();
            return response()->json([
                'message'   => 'Role berhasil disimpan!'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'message'   => $th->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);
            return json_encode($role);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            $role->update([
                'name'  => $request->name ?? $role->name,
            ]);
            $role->syncPermissions($request->permissions);
            DB::commit();
            return response()->json(['message' => 'Data Role berhasil diupdate'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Role::findOrFail($id)->delete();
            DB::commit();
            return response()->json(['message' => 'Data berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
