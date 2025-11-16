<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Display a listing of items for a specific box
     */
    public function index(Request $request)
    {
        $query = Item::with(['box', 'category']);

        // Filter by box_id if provided
        if ($request->has('box_id')) {
            $query->where('box_id', $request->box_id);
        }

        // Filter by category_id if provided
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $query->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Store a newly created item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'box_id' => 'required|exists:boxes,id',
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verify user owns the box
        $box = Box::findOrFail($validated['box_id']);
        if ($box->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to add items to this box'
            ], 403);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
            $validated['image'] = $imagePath;
        }

        $item = Item::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item created successfully',
            'data' => $item->load(['box', 'category'])
        ], 201);
    }

    /**
     * Display the specified item
     */
    public function show($id)
    {
        $item = Item::with(['box', 'category'])->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Update the specified item
     */
    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }

        // Verify user owns the box
        if ($item->box->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this item'
            ], 403);
        }

        $validated = $request->validate([
            'box_id' => 'sometimes|exists:boxes,id',
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $imagePath = $request->file('image')->store('items', 'public');
            $validated['image'] = $imagePath;
        }

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
            'data' => $item->load(['box', 'category'])
        ]);
    }

    /**
     * Remove the specified item
     */
    public function destroy(Request $request, $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }

        // Verify user owns the box
        if ($item->box->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this item'
            ], 403);
        }

        // Delete image if exists
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully'
        ]);
    }
}
