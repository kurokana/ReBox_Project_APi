<?php

// app/Http/Controllers/Api/AuthController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'bio' => 'sometimes|string|max:500',
        ]);

        $user->update($request->only(['name', 'phone', 'bio']));

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diupdate',
            'data' => $user
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        // Hapus avatar lama jika ada
        if ($user->avatar && file_exists(public_path($user->avatar))) {
            unlink(public_path($user->avatar));
        }

        // Upload avatar baru
        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/avatars'), $filename);

        // Update user avatar
        $avatarPath = 'uploads/avatars/' . $filename;
        $user->update(['avatar' => $avatarPath]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar berhasil diupdate',
            'data' => [
                'user' => $user,
                'avatar_url' => url($avatarPath)
            ]
        ]);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'avatar_url' => $user->avatar ? url($user->avatar) : null
            ]
        ]);
    }

    // ============= PENGGUNA TRANSACTION METHODS =============
    
    public function getPenggunaTransactions(Request $request)
    {
        $transactions = Transaction::with([
            'box.items.category',
            'pengepul:id,name,email,phone'
        ])
            ->where('pengguna_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function acceptTransaction(Request $request, $id)
    {
        $transaction = Transaction::where('pengguna_id', $request->user()->id)
            ->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        if ($transaction->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi harus dalam status pending'
            ], 400);
        }

        $transaction->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Create notification for pengepul
        Notification::create([
            'user_id' => $transaction->pengepul_id,
            'title' => 'Transaksi Diterima',
            'message' => 'Pengguna menerima penawaran Anda',
            'type' => 'success',
            'data' => [
                'transaction_id' => $transaction->id,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diterima',
            'data' => $transaction
        ]);
    }

    public function rejectTransaction(Request $request, $id)
    {
        $transaction = Transaction::where('pengguna_id', $request->user()->id)
            ->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        if ($transaction->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi harus dalam status pending'
            ], 400);
        }

        $transaction->update([
            'status' => 'rejected',
        ]);

        // Create notification for pengepul
        Notification::create([
            'user_id' => $transaction->pengepul_id,
            'title' => 'Transaksi Ditolak',
            'message' => 'Pengguna menolak penawaran Anda',
            'type' => 'warning',
            'data' => [
                'transaction_id' => $transaction->id,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil ditolak',
            'data' => $transaction
        ]);
    }

    // ============= NOTIFICATION METHODS =============
    
    public function getMyNotifications(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function markNotificationAsRead(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
            ->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai dibaca',
            'data' => $notification
        ]);
    }
}
