<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendAdminNotificationJob;
use App\Models\EvacuationCenter;
use Illuminate\Http\Request;

class EvacuationCenterController extends Controller
{
    // ================= LIST =================
    public function index(Request $request)
    {
        $user = auth()->user();

        // MDRRMO
        if ($user->role === 'mdrrmo_admin') {

            $query = EvacuationCenter::where(
                'municipality',
                $user->municipality
            );

            if ($request->filled('search')) {
                $query->where(
                    'barangay',
                    'like',
                    '%' . $request->search . '%'
                );
            }

            return response()->json([
                'type' => 'mdrrmo',
                'data' => $query
                    ->selectRaw("
                    barangay,
                    COUNT(*) as centers,
                    SUM(current_occupancy) as occupants,
                    SUM(capacity) as capacity
                ")
                    ->groupBy('barangay')
                    ->orderBy('barangay')
                    ->get()
            ]);
        }

        // BDRRMO
        $query = EvacuationCenter::where(
            'barangay',
            $user->barangay
        );

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('event_type', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'type' => 'bdrrmo',
            'data' => $query
                ->orderBy('name')
                ->get()
        ]);
    }


    // ================= LIST BY BARANGAY =================
    public function barangayCenters($barangay)
    {
        $user = auth()->user();

        $centers = EvacuationCenter::where(
            'municipality',
            $user->municipality
        )
            ->where('barangay', $barangay)
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $centers
        ]);
    }







    // ================= STORE =================
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255', //complied
            'location' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'current_occupancy' => 'nullable|integer|min:0',

            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',

            'event_type' => 'nullable|string|max:255',


            'status' => 'nullable|in:Standby,Open,Full,Closed',
            'facilities' => 'nullable|array',
        ]);

        $validated['barangay'] = $user->barangay;
        $validated['municipality'] = $user->municipality;
        $validated['created_by'] = $user->id;

        $center = EvacuationCenter::create($validated);

        SendAdminNotificationJob::dispatch(
            'resident',
            [
                'title' => "Barangay {$user->barangay}",
                'body' => "Barangay {$user->barangay} created evacuation center information!",
                'sms' => "[AlertoPH ALERT]\n Barangay {$user->barangay} posted evacuation center information!\n",
                'request_id' => $user->id,
                'url' => '/centers'
            ],
            $user->barangay
        );

        return response()->json([
            'message' => 'Evacuation center created successfully',
            'data' => $center
        ], 201);
    }

    // ================= STORE MDRRMO=================
    public function store_mdrrmo(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255', //complied
            'barangay' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'current_occupancy' => 'nullable|integer|min:0',

            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',

            'event_type' => 'nullable|string|max:255',


            'status' => 'nullable|in:Standby,Open,Full,Closed',
            'facilities' => 'nullable|array',
        ]);

        $validated['municipality'] = $user->municipality;
        $validated['created_by'] = $user->id;

        $center = EvacuationCenter::create($validated);

        SendAdminNotificationJob::dispatch(
            'resident',
            [
                'title' => "Barangay {$user->barangay}",
                'body' => "Barangay {$user->barangay} created evacuation center information!",
                'sms' => "[AlertoPH ALERT]\n Barangay {$user->barangay} posted evacuation center information!\n",
                'request_id' => $user->id,
                'url' => '/centers'
            ],
            $user->barangay
        );

        return response()->json([
            'message' => 'Evacuation center created successfully',
            'data' => $center
        ], 201);
    }

    // ================= SHOW =================
    public function show($id)
    {
        $user = auth()->user();

        $center = EvacuationCenter::where('barangay', $user->barangay)
            ->findOrFail($id);

        return response()->json($center);
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        $center = EvacuationCenter::where('barangay', $user->barangay)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255', //complied
            'location' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'current_occupancy' => 'nullable|integer|min:0',

            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',

            'event_type' => 'nullable|string|max:255',


            'status' => 'nullable|in:Standby,Open,Full,Closed',
            'facilities' => 'nullable|array',
        ]);

        $center->update($validated);

        SendAdminNotificationJob::dispatch(
            'resident',
            [
                'title' => "Barangay {$user->barangay}",
                'body' => "Barangay {$user->barangay} update evacuation center information!",
                'sms' => "[AlertoPH ALERT]\n Barangay {$user->barangay} update evacuation center information!\n",
                'request_id' => $user->id,
                'url' => '/centers'
            ],
            $user->barangay
        );


        return response()->json([
            'message' => 'Evacuation center updated successfully',
            'data' => $center
        ]);
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        $user = auth()->user();

        $center = EvacuationCenter::where('barangay', $user->barangay)
            ->findOrFail($id);

        $center->delete();

        return response()->json([
            'message' => 'Evacuation center deleted successfully'
        ]);
    }
}
