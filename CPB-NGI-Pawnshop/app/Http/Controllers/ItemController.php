<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Safe;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of items.
     */
    public function index(Request $request)
    {
        $query = Item::with('category')->where('item_status', '!=', 'removed');

        // 🔍 Search (name or item_code)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('item_code', 'like', '%' . $request->search . '%');
            });
        }

        // 📂 Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 📊 Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'available') {
                $query->where('is_available', true);
            } elseif ($request->status === 'loaned') {
                $query->where('is_available', false);
            }
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        // 🔥 IMPORTANT (for dropdown)
        $categories = Category::all();

        return view('items.index', compact('items', 'categories'));
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        $categories = Category::all();
        $safes = Safe::where('status', 'active')->get();
        return view('items.create', compact('categories', 'safes'));
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'safe_id' => 'nullable|exists:safes,id',
            'appraised_value' => 'required|numeric|min:0',
            'condition' => 'required|in:excellent,good,fair,poor',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Generate item code
        $validated['item_code'] = 'ITEM-' . now()->format('YmdHis') . '-' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        Item::create($validated);

        return redirect()->route('items.index')->with('success', 'Item added successfully!');
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item)
    {
        $item->load('category', 'safe', 'transactions');
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        $categories = Category::all();
        $safes = Safe::where('status', 'active')->get();
        return view('items.edit', compact('item', 'categories', 'safes'));
    }

    /**
     * Update the specified item in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'safe_id' => 'nullable|exists:safes,id',
            'appraised_value' => 'required|numeric|min:0',
            'condition' => 'required|in:excellent,good,fair,poor',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_available' => 'nullable|boolean',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Item updated successfully!');
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully!');
    }

    /**
     * Get unique item names by category for autocomplete.
     */
    public function getNamesByCategory($categoryId)
    {
        $names = Item::where('category_id', $categoryId)
            ->distinct()
            ->pluck('name');
            
        return response()->json($names);
    }

    /**
     * Request removal of an item.
     */
    public function requestVoid(Request $request, Item $item)
    {
        $hasPendingApproval = \App\Models\Approval::where('model_type', Item::class)
            ->where('model_id', $item->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingApproval) {
            return redirect()->route('items.index')->with('error', 'This item already has a pending removal request.');
        }

        if (auth()->user()->isTeller()) {
            $notes = $request->input('approval_notes');
            if (empty($notes)) {
                return back()->with('error', 'A reason is required to request a removal.');
            }

            \App\Models\Approval::create([
                'user_id' => auth()->id(),
                'action' => 'remove_item',
                'model_type' => Item::class,
                'model_id' => $item->id,
                'payload' => null,
                'status' => 'pending',
                'notes' => $notes,
            ]);
            
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'remove_request',
                'model_type' => 'Item',
                'model_id' => $item->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'description' => "Requested removal of item {$item->name}. Reason: {$notes}",
            ]);

            return redirect()->route('items.index')->with('info', 'Removal requested for manager approval.');
        }

        if (auth()->user()->isManager() || auth()->user()->isAdmin()) {
            $notes = $request->input('approval_notes');
            if (empty($notes)) {
                return back()->with('error', 'A reason is required to remove an item.');
            }

            $item->update(['item_status' => 'removed', 'is_available' => false]);
            
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'remove_item',
                'model_type' => 'Item',
                'model_id' => $item->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'description' => "Removed item {$item->name}. Reason: {$notes}",
            ]);

            return redirect()->route('items.index')->with('success', 'Item removed successfully!');
        }

        return redirect()->route('items.index')->with('error', 'Unauthorized action.');
    }
}
