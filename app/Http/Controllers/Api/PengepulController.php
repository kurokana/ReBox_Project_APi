<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Box;
use App\Models\Notification;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengepulController extends Controller
{
    // ================== MELIHAT DAFTAR KOTAK (PESANAN) ==================
    
    public function getAvailableBoxes()
    {
        // Get boxes that don't have accepted/completed transactions
        $boxes = Box::with(['items.category', 'user:id,name,email,phone,address'])
            ->whereDoesntHave('transactions', function ($query) {
                $query->whereIn('status', ['accepted', 'completed']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $boxes
        ]);
    }

    // ================== MENGELOLA ITEM DALAM KOTAK ==================
    
    public function getBoxDetails($id)
    {
        $box = Box::with(['items.category', 'user:id,name,email,phone,address'])
            ->find($id);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Kotak tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $box
        ]);
    }

    // ================== MELAKUKAN TRANSAKSI (JUAL SAMPAH) ==================
    
    public function createTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'box_id' => 'required|exists:boxes,id',
            'total_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $box = Box::find($request->box_id);

        // Check if box already has active transaction
        $existingTransaction = Transaction::where('box_id', $box->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existingTransaction) {
            return response()->json([
                'success' => false,
                'message' => 'Kotak sudah memiliki transaksi aktif'
            ], 400);
        }

        // Calculate fees (admin fee 10%, pengepul earnings 90%)
        $adminFee = $request->total_price * 0.10;
        $pengepulEarnings = $request->total_price * 0.90;

        $transaction = Transaction::create([
            'box_id' => $box->id,
            'pengguna_id' => $box->user_id,
            'pengepul_id' => $request->user()->id,
            'status' => 'pending',
            'total_price' => $request->total_price,
            'admin_fee' => $adminFee,
            'pengepul_earnings' => $pengepulEarnings,
            'notes' => $request->notes,
        ]);

        // Create notification for pengguna
        Notification::create([
            'user_id' => $box->user_id,
            'title' => 'Penawaran Baru',
            'message' => 'Pengepul menawarkan Rp ' . number_format($request->total_price, 0, ',', '.') . ' untuk kotak Anda',
            'type' => 'info',
            'data' => [
                'transaction_id' => $transaction->id,
                'box_id' => $box->id,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibuat',
            'data' => $transaction->load(['box.items.category', 'pengguna'])
        ], 201);
    }

    public function getMyTransactions()
    {
        $transactions = Transaction::with([
            'box.items.category',
            'pengguna:id,name,email,phone,address'
        ])
            ->where('pengepul_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function updateTransactionStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $transaction = Transaction::where('pengepul_id', auth()->id())
            ->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        if ($transaction->status !== 'accepted') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi harus dalam status accepted untuk diupdate'
            ], 400);
        }

        $transaction->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        // If completed, add earnings to pengepul balance
        if ($request->status === 'completed') {
            $transaction->pengepul->increment('balance', $transaction->pengepul_earnings);
        }

        // Create notification for pengguna
        $message = $request->status === 'completed'
            ? 'Transaksi telah selesai'
            : 'Transaksi telah dibatalkan';

        Notification::create([
            'user_id' => $transaction->pengguna_id,
            'title' => 'Status Transaksi',
            'message' => $message,
            'type' => $request->status === 'completed' ? 'success' : 'warning',
            'data' => [
                'transaction_id' => $transaction->id,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status transaksi berhasil diupdate',
            'data' => $transaction
        ]);
    }

    // ================== MENGAKSES BERANDA (DASHBOARD) ==================
    
    public function getDashboard()
    {
        $stats = [
            'total_transactions' => Transaction::where('pengepul_id', auth()->id())->count(),
            'pending' => Transaction::where('pengepul_id', auth()->id())->pending()->count(),
            'accepted' => Transaction::where('pengepul_id', auth()->id())->accepted()->count(),
            'completed' => Transaction::where('pengepul_id', auth()->id())->completed()->count(),
            'total_earnings' => Transaction::where('pengepul_id', auth()->id())->completed()->sum('pengepul_earnings'),
            'current_balance' => auth()->user()->balance,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
