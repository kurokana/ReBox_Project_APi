<?php

// app/Http/Controllers/Api/BoxController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Box;
use Illuminate\Http\Request;

class BoxController extends Controller
{
    public function index(Request $request)
    {
        $boxes = $request->user()->boxes()->with('items')->get();
        
        return response()->json([
            'success' => true,
            'data' => $boxes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $box = $request->user()->boxes()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Box berhasil dibuat',
            'data' => $box
        ], 201);
    }

    public function show(Box $box)
    {
        $this->authorize('view', $box);
        
        return response()->json([
            'success' => true,
            'data' => $box->load('items')
        ]);
    }

    public function update(Request $request, Box $box)
    {
        $this->authorize('update', $box);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $box->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Box berhasil diupdate',
            'data' => $box
        ]);
    }

    public function destroy(Box $box)
    {
        $this->authorize('delete', $box);
        $box->delete();

        return response()->json([
            'success' => true,
            'message' => 'Box berhasil dihapus'
        ]);
    }
}
