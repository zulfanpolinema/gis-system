<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $users = User::all();
            return DataTables::of($users)
                ->editColumn('created_at', function ($item) {
                    return $item->created_at->diffForHumans();
                })
                ->editColumn('email_verified_at', function ($item) {
                    return $item->email_verified_at ? '<i class="fa fa-lg fa-fw fa-check text-primary"></i>' : '<i class="fa fa-lg fa-fw fa-times text-danger"></i>';
                })
                ->addColumn('role', function ($item) {
                    return $item->getRoleNames()->implode(', ');
                })
                ->addColumn('actions', function ($item) {
                    return
                        '
                            <nobr>
                            <button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit" id="editButton" data-id="' . $item->id . '" data-toggle="modal" data-target="#editUserModal">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </button>
                            <button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete" id="deleteButton" data-id="' . $item->id . '">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>
                            </nobr>
                        ';
                })
                ->rawColumns(['actions', 'email_verified_at'])
                ->addIndexColumn()
                ->make();
        }
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            $request->validate([
                'name'          => 'required',
                'email'         => 'required|unique:users,email|email',
                'password'      => 'required|min:8',
                'role'          => 'required',
            ], [
                'name.required'         => 'Masukkan nama lengkap!',
                'password.required'     => 'Masukkan password!',
                'password.min'          => 'Password minimal 8 karakter!',
                'role.required'         => 'Pilih role!',
                'email.required'        => 'Masukkan email!',
                'email.email'           => 'Masukkan alamat email valid!',
                'email.unique'          => 'Email sudah digunakan!',
            ]);

            $users = User::create([
                'name'          => $request->name,
                'password'      => bcrypt($request->password),
                'email'         => $request->email,
            ]);

            $users->syncRoles($request->role);
            DB::commit();
            return response()->json([
                'message'   => 'User berhasil disimpan!'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'message'   => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $role = User::with('roles')->findOrFail($id);
            return json_encode($role);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->update([
                'name'          => $request->name ?? $user->name,
                'username'      => $request->username ?? $user->username,
                'password'      => $request->password ? bcrypt($request->password) : $user->password,
                'phonenumber'   => $request->phonenumber ?? $user->phonenumber,
                'email'         => $request->email ?? $user->email,
            ]);
            if ($request->role != $user->roles->first()->id) {
                $user->syncRoles($request->role);
            }
            DB::commit();
            return response()->json(['message' => 'Data user berhasil diupdate'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            User::findOrFail($id)->delete();
            DB::commit();
            return response()->json(['message' => 'Data berhasil dihapus'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
