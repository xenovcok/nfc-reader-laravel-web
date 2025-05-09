<?php

namespace App\Http\Controllers;

use App\Models\WrisbandMaster;
use Illuminate\Http\Request;

class WrisbandMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tag_name' => 'required|string',
            'tag_id' => 'required|string',
        ]);

        // Generate uuid
        $uuid = \Illuminate\Support\Str::uuid()->toString();

        // Use alias same as name (can be customized)
        $alias = $validated['tag_name'];

        // Check if tag_id (code) already exists
        if (WrisbandMaster::where('code', $validated['tag_id'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tag already scanned.'
            ], 409);
        }

        $wrisband = WrisbandMaster::create([
            'uuid' => $uuid,
            'name' => $validated['tag_name'],
            'alias' => $alias,
            'code' => $validated['tag_id'],
        ]);
        return response()->json(['success' => true, 'data' => $wrisband], 201);
    }

    // API: Get next CBS name
    public function nextName()
    {
        $latest = WrisbandMaster::orderByDesc('name')->where('name', 'like', 'CBS%')->first();
        if ($latest && preg_match('/CBS(\d+)/', $latest->name, $m)) {
            $nextNum = intval($m[1]) + 1;
        } else {
            $nextNum = 1;
        }
        $nextName = 'CBS' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
        return response()->json(['next_name' => $nextName]);
    }

    /**
     * Display the specified resource.
     */
    public function show(WrisbandMaster $wrisbandMaster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WrisbandMaster $wrisbandMaster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WrisbandMaster $wrisbandMaster)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WrisbandMaster $wrisbandMaster)
    {
        //
    }
}
