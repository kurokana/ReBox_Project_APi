<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WasteSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WasteSaleController extends Controller
{
    /**
     * Display a listing of waste sales
     */
    public function index(Request $request)
    {
        $query = WasteSale::with('user');

        // Filter by user if not admin
        if (!$request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $wasteSales = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $wasteSales,
        ]);
    }

    /**
     * Store a newly created waste sale
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'waste_type' => 'required|string|in:plastik,kertas,logam,kaca,organik,elektronik',
            'weight' => 'required|numeric|min:0.1',
            'price_per_kg' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['user_id'] = $request->user()->id;
            $data['total_price'] = $data['weight'] * $data['price_per_kg'];

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $path = $photo->storeAs('waste_sales', $filename, 'public');
                $data['photo_path'] = $path;
            }

            $wasteSale = WasteSale::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Penjualan sampah berhasil dibuat',
                'data' => $wasteSale->load('user'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat penjualan sampah',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified waste sale
     */
    public function show(Request $request, $id)
    {
        $wasteSale = WasteSale::with('user')->find($id);

        if (!$wasteSale) {
            return response()->json([
                'success' => false,
                'message' => 'Penjualan sampah tidak ditemukan',
            ], 404);
        }

        // Check authorization
        if (!$request->user()->isAdmin() && $wasteSale->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $wasteSale,
        ]);
    }

    /**
     * Update waste sale status (admin only)
     */
    public function updateStatus(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat mengubah status',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,rejected,completed',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $wasteSale = WasteSale::find($id);

        if (!$wasteSale) {
            return response()->json([
                'success' => false,
                'message' => 'Penjualan sampah tidak ditemukan',
            ], 404);
        }

        $wasteSale->status = $request->status;
        $wasteSale->admin_notes = $request->admin_notes;
        
        if ($request->status === 'approved') {
            $wasteSale->approved_at = now();
        }

        $wasteSale->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'data' => $wasteSale->load('user'),
        ]);
    }

    /**
     * Remove the specified waste sale
     */
    public function destroy(Request $request, $id)
    {
        $wasteSale = WasteSale::find($id);

        if (!$wasteSale) {
            return response()->json([
                'success' => false,
                'message' => 'Penjualan sampah tidak ditemukan',
            ], 404);
        }

        // Check authorization
        if (!$request->user()->isAdmin() && $wasteSale->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Only allow deletion if status is pending
        if ($wasteSale->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya penjualan dengan status pending yang dapat dihapus',
            ], 403);
        }

        // Delete photo if exists
        if ($wasteSale->photo_path) {
            Storage::disk('public')->delete($wasteSale->photo_path);
        }

        $wasteSale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penjualan sampah berhasil dihapus',
        ]);
    }

    /**
     * Get waste sale statistics (admin only)
     */
    public function statistics(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $stats = [
            'total_sales' => WasteSale::count(),
            'pending_sales' => WasteSale::where('status', 'pending')->count(),
            'approved_sales' => WasteSale::where('status', 'approved')->count(),
            'completed_sales' => WasteSale::where('status', 'completed')->count(),
            'total_weight' => WasteSale::where('status', 'completed')->sum('weight'),
            'total_revenue' => WasteSale::where('status', 'completed')->sum('total_price'),
            'by_waste_type' => WasteSale::selectRaw('waste_type, COUNT(*) as count, SUM(weight) as total_weight, SUM(total_price) as total_price')
                ->where('status', 'completed')
                ->groupBy('waste_type')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
