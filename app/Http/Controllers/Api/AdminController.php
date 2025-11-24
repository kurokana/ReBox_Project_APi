<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // ================== MENGELOLA KATEGORI ==================
    
    public function getCategories()
    {
        $categories = Category::withCount('items')->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function createCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = Category::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dibuat',
            'data' => $category
        ], 201);
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $category->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diupdate',
            'data' => $category
        ]);
    }

    public function deleteCategory($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        // Check if category has items
        if ($category->items()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki item'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    }

    // ================== MELIHAT NOTIFIKASI ==================
    
    public function getAllNotifications()
    {
        $notifications = Notification::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function createNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id', // If null, send to all
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error',
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // If user_id is null, send to all users
        if (!isset($data['user_id'])) {
            $users = User::all();
            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'type' => $data['type'],
                    'data' => $data['data'] ?? null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dikirim ke semua user',
                'count' => $users->count()
            ], 201);
        }

        $notification = Notification::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dibuat',
            'data' => $notification
        ], 201);
    }

    // ================== MELIHAT RIWAYAT PESANAN ==================
    
    public function getAllTransactions()
    {
        $transactions = Transaction::with([
            'box.items.category',
            'pengguna:id,name,email,phone',
            'pengepul:id,name,email,phone'
        ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function getTransactionStats()
    {
        $stats = [
            'total_transactions' => Transaction::count(),
            'pending' => Transaction::pending()->count(),
            'accepted' => Transaction::accepted()->count(),
            'completed' => Transaction::completed()->count(),
            'total_revenue' => Transaction::completed()->sum('total_price'),
            'total_admin_fee' => Transaction::completed()->sum('admin_fee'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    // ================== MENERIMA PEMBAYARAN/PENCAIRAN ==================
    
    public function getPengepulPayments()
    {
        $pengepuls = User::where('role', 'pengepul')
            ->withSum('collectorTransactions as total_earnings', 'pengepul_earnings')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pengepuls
        ]);
    }

    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pengepul_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $pengepul = User::find($request->pengepul_id);

        if (!$pengepul->isPengepul()) {
            return response()->json([
                'success' => false,
                'message' => 'User bukan pengepul'
            ], 400);
        }

        if ($pengepul->balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        // Deduct balance
        $pengepul->decrement('balance', $request->amount);

        // Create notification
        Notification::create([
            'user_id' => $pengepul->id,
            'title' => 'Pembayaran Berhasil',
            'message' => 'Pembayaran sebesar Rp ' . number_format($request->amount, 0, ',', '.') . ' telah diproses',
            'type' => 'success',
            'data' => [
                'amount' => $request->amount,
                'notes' => $request->notes,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diproses',
            'data' => [
                'pengepul' => $pengepul,
                'amount' => $request->amount,
            ]
        ]);
    }
}
