<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $transactions = Transaction::with(['harvest', 'harvest.user'])
                ->when(auth()->user()->hasRole('Pengepul'), function ($query) {
                    $query->where('user_id', auth()->user()->id);
                })
                ->when(auth()->user()->hasRole('Petani'), function ($query) {
                    $query->whereRelation('harvest.user', 'id', auth()->user()->id);
                })
                ->latest()
                ->get();
            return DataTables::of($transactions)
                ->addColumn('pemilik', function ($item) {
                    return $item->harvest->user->name;
                })
                ->addColumn('category', function ($item) {
                    return $item->harvest->category->name;
                })
                ->editColumn('amount', function ($item) {
                    return $item->amount . ' Kg';
                })
                ->editColumn('total', function ($item) {
                    return 'Rp ' . number_format($item->total, 0, ',', '.');
                })
                ->addColumn('phonenumber', function ($item) {
                    if ($item->harvest->phonenumber) {
                        $phonenumber = $item->harvest->phonenumber;
                        if ($phonenumber[0] == "0") {
                            $phonenumber = substr($phonenumber, 1);
                        }

                        if ($phonenumber[0] == "8") {
                            $phonenumber = "62" . $phonenumber;
                        }
                        return '<a href="https://wa.me/' . $phonenumber . '" target="_blank">' . $item->harvest->phonenumber . '</a>';
                    } else {
                        return '-';
                    }
                })
                ->addColumn('status', function ($item) {
                    return config('data.transaction_status')[$item->status]['badge'];
                })
                ->addColumn('actions', function ($item) {
                    if (auth()->user()->hasRole('Petani')) {
                        if ($item->status == 1) {
                            return '
                                <a href="' . route('transactions.accept', $item->id) . '" class="btn btn-xs btn-default text-success mx-1 shadow" title="Setujui Penjualan">
                                    <i class="fa fa-lg fa-fw fa-check"></i>
                                </a>
                                <a href="' . route('transactions.cancel', $item->id) . '" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Batalkan Penjualan">
                                    <i class="fa fa-lg fa-fw fa-times"></i>
                                </a>
                                ';
                        } else if ($item->status == 2) {
                            return '
                                <a href="' . route('transactions.done', $item->id) . '" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Selesaikan Penjualan">
                                    <i class="fa fa-lg fa-fw fa-check"></i>
                                </a>
                                ';
                        } else {
                            return '-';
                        }
                    } else {
                        return '
                                <a href="' . route('transactions.edit', $item->id) . '" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                    <i class="fa fa-lg fa-fw fa-pen"></i>
                                </a>
                                ';
                    }
                })
                ->rawColumns(['phonenumber', 'status', 'actions'])
                ->addIndexColumn()
                ->make();
        }
        return view('admin.transactions.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'harvest_id' => 'required',
                'amount' => 'required',
                'total' => 'required',
            ]);

            Transaction::create([
                'user_id' => auth()->user()->id,
                'harvest_id' => $request->harvest_id,
                'amount' => $request->amount,
                'total' => str_replace('.', '', $request->total),
                'status' => 1,
            ]);

            return response()->json(['message' => 'Data berhasil disimpan!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function show(Transaction $transaction)
    {
        //
    }

    public function edit(Transaction $transaction)
    {
        //
    }

    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    public function destroy(Transaction $transaction)
    {
        //
    }

    public function accept($id)
    {
        $transaction = Transaction::findOrFail($id);
        if ($transaction->harvest->user->id != auth()->user()->id || !auth()->user()->hasRole('Admin')) {
            return redirect()->back()->with('error', 'Data panen bukan milik anda!');
        }
        if ($transaction->status == 1) {
            $transaction->update([
                'status' => 2,
            ]);
            $harvest = $transaction->harvest;
            $harvest->update([
                'total' => $harvest->total - $transaction->amount,
            ]);
            return redirect()->back()->with('success', 'Penjualan berhasil disetujui!');
        } else {
            return redirect()->back()->with('error', 'Penjualan tidak dalam status pengajuan!');
        }
    }

    public function cancel($id)
    {
        $transaction = Transaction::findOrFail($id);
        if ($transaction->harvest->user->id != auth()->user()->id || !auth()->user()->hasRole('Admin')) {
            return redirect()->back()->with('error', 'Data panen bukan milik anda!');
        }
        if ($transaction->status == 1) {
            $transaction->update([
                'status' => 4,
            ]);
            return redirect()->back()->with('success', 'Penjualan berhasil dibatalkan!');
        } else {
            return redirect()->back()->with('error', 'Penjualan tidak dalam status pengajuan!');
        }
    }

    public function done($id)
    {
        $transaction = Transaction::findOrFail($id);
        if ($transaction->harvest->user->id != auth()->user()->id || !auth()->user()->hasRole('Admin')) {
            return redirect()->back()->with('error', 'Data panen bukan milik anda!');
        }
        if ($transaction->status == 2) {
            $transaction->update([
                'status' => 3,
            ]);
            return redirect()->back()->with('success', 'Penjualan berhasil diselesaikan!');
        } else {
            return redirect()->back()->with('error', 'Penjualan tidak dalam persetujuan!');
        }
    }
}
