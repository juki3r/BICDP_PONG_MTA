<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\Blotter;
use App\Models\Concern;
use App\Models\Certificate;
use App\Models\MobileUser;
use App\Models\Ordinance;
use App\Models\Incident;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // ================= BDRRMO =================
        if ($user->role === 'bdrrmo_admin') {

            $barangay = $user->barangay;

            abort_unless($barangay, 403, 'Unauthorized');

            $residentQuery = Resident::where('barangay', $barangay);
            $incidentQuery = Incident::where('barangay', $barangay);
            $blotterQuery = Blotter::where('barangay', $barangay);
            $concernQuery = Concern::where('barangay', $barangay);
            $certificateQuery = Certificate::where('barangay', $barangay);
            $appUserQuery = MobileUser::where('barangay', $barangay);
            $ordinanceQuery = Ordinance::where('barangay', $barangay);

            return response()->json([
                "role" => $user->role,

                "residents" => $residentQuery->count(),
                "voters" => (clone $residentQuery)->where('is_voter', 1)->count(),
                "male" => (clone $residentQuery)->where('gender', 'Male')->count(),
                "female" => (clone $residentQuery)->where('gender', 'Female')->count(),

                "blotters" => $blotterQuery->count(),
                "concerns" => $concernQuery->count(),
                "certificates" => $certificateQuery->count(),

                "app_users" => $appUserQuery->count(),
                "ordinances" => $ordinanceQuery->count(),
                "incidents" => $incidentQuery->count(),

                "incident_trend" => $incidentQuery
                    ->selectRaw("DATE(created_at) as date, COUNT(*) as total")
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),

                "live_incidents" => Incident::where('barangay', $barangay)
                    ->whereNotIn('status', ['resolved', 'declined'])
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get([
                        'id',
                        'type',
                        'location',
                        'gps_location',
                        'description',
                        'status',
                        'incident_datetime',
                        'created_at'
                    ]),

                "gender_distribution" => [
                    [
                        "name" => "Male",
                        "value" => (clone $residentQuery)->where('gender', 'Male')->count()
                    ],
                    [
                        "name" => "Female",
                        "value" => (clone $residentQuery)->where('gender', 'Female')->count()
                    ],
                ],

                "age_distribution" => [
                    [
                        "range" => "0-17",
                        "count" => (clone $residentQuery)->whereBetween('age', [0, 17])->count()
                    ],
                    [
                        "range" => "18-30",
                        "count" => (clone $residentQuery)->whereBetween('age', [18, 30])->count()
                    ],
                    [
                        "range" => "31-59",
                        "count" => (clone $residentQuery)->whereBetween('age', [31, 59])->count()
                    ],
                    [
                        "range" => "60+",
                        "count" => (clone $residentQuery)->where('age', '>=', 60)->count()
                    ],
                ],
            ]);
        }

        // ================= MDRRMO =================
        if ($user->role === 'mdrrmo_admin') {
            $municipality = $user->municipality;

            abort_unless($municipality, 403, 'Unauthorized');

            $residentQuery = Resident::where('city_municipality', $municipality);
            $incidentQuery = Incident::where('municipality', $municipality);
            // $blotterQuery = Blotter::where('municipality', $municipality);
            // $concernQuery = Concern::where('municipality', $municipality);
            // $certificateQuery = Certificate::where('municipality', $municipality);
            $appUserQuery = MobileUser::where('municipality', $municipality);
            // $ordinanceQuery = Ordinance::where('municipality', $municipality);

            return response()->json([

                "role" => $user->role,
                "residents" => $residentQuery->count(),
                "voters" => (clone $residentQuery)->where('is_voter', 1)->count(),
                "male" => (clone $residentQuery)->where('gender', 'Male')->count(),
                "female" => (clone $residentQuery)->where('gender', 'Female')->count(),

                // "blotters" => $blotterQuery->count(),
                // "concerns" => $concernQuery->count(),
                // "certificates" => $certificateQuery->count(),

                "app_users" => $appUserQuery->count(),
                // "ordinances" => $ordinanceQuery->count(),
                "incidents" => $incidentQuery->count(),

                "incident_trend" => $incidentQuery
                    ->selectRaw("DATE(created_at) as date, COUNT(*) as total")
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),

                "live_incidents" => Incident::where('municipality', $municipality)
                    ->whereNotIn('status', ['resolved', 'declined'])
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get([
                        'id',
                        'type',
                        'location',
                        'gps_location',
                        'description',
                        'status',
                        'incident_datetime',
                        'created_at'
                    ]),


                "gender_distribution" => [
                    [
                        "name" => "Male",
                        "value" => (clone $residentQuery)->where('gender', 'Male')->count()
                    ],
                    [
                        "name" => "Female",
                        "value" => (clone $residentQuery)->where('gender', 'Female')->count()
                    ],
                ],

                "age_distribution" => [
                    [
                        "range" => "0-17",
                        "count" => (clone $residentQuery)->whereBetween('age', [0, 17])->count()
                    ],
                    [
                        "range" => "18-30",
                        "count" => (clone $residentQuery)->whereBetween('age', [18, 30])->count()
                    ],
                    [
                        "range" => "31-59",
                        "count" => (clone $residentQuery)->whereBetween('age', [31, 59])->count()
                    ],
                    [
                        "range" => "60+",
                        "count" => (clone $residentQuery)->where('age', '>=', 60)->count()
                    ],
                ],

            ]);
        }

        return response()->json([
            // "role" => $user->role ?? null,
            "error" => "Unauthorized role"
        ], 403);
    }



    // public function index(Request $request)
    // {
    //     $user = auth()->user();

    //     abort_unless(
    //         $user &&
    //             (
    //                 ($user->role === 'bdrrmo_admin' && $user->barangay) ||
    //                 ($user->role === 'mdrrmo_admin' && $user->municipality)
    //             ),
    //         403,
    //         'Unauthorized'
    //     );

    //     $barangay = $user->barangay;

    //     // ================= SCOPED QUERIES =================
    //     $residentQuery = Resident::where('barangay', $barangay);
    //     $incidentQuery = Incident::where('barangay', $barangay);
    //     $blotterQuery = Blotter::where('barangay', $barangay);
    //     $concernQuery = Concern::where('barangay', $barangay);
    //     $certificateQuery = Certificate::where('barangay', $barangay);
    //     $appUserQuery = MobileUser::where('barangay', $barangay);
    //     $ordinanceQuery = Ordinance::where('barangay', $barangay);

    //     return response()->json([
    //         "role" => $user->role,

    //         // ================= STATS =================
    //         "residents" => $residentQuery->count(),
    //         "voters" => (clone $residentQuery)->where('is_voter', 1)->count(),
    //         "male" => (clone $residentQuery)->where('gender', 'Male')->count(),
    //         "female" => (clone $residentQuery)->where('gender', 'Female')->count(),

    //         "blotters" => $blotterQuery->count(),
    //         "concerns" => $concernQuery->count(),
    //         "certificates" => $certificateQuery->count(),

    //         "app_users" => $appUserQuery->count(),
    //         "ordinances" => $ordinanceQuery->count(),
    //         "incidents" => $incidentQuery->count(),

    //         // ================= CHARTS =================
    //         "incident_trend" => $incidentQuery
    //             ->selectRaw("DATE(created_at) as date, COUNT(*) as total")
    //             ->groupBy('date')
    //             ->orderBy('date')
    //             ->get(),

    //         "gender_distribution" => [
    //             [
    //                 "name" => "Male",
    //                 "value" => (clone $residentQuery)->where('gender', 'Male')->count()
    //             ],
    //             [
    //                 "name" => "Female",
    //                 "value" => (clone $residentQuery)->where('gender', 'Female')->count()
    //             ],
    //         ],

    //         "age_distribution" => [
    //             [
    //                 "range" => "0-17",
    //                 "count" => (clone $residentQuery)->whereBetween('age', [0, 17])->count()
    //             ],
    //             [
    //                 "range" => "18-30",
    //                 "count" => (clone $residentQuery)->whereBetween('age', [18, 30])->count()
    //             ],
    //             [
    //                 "range" => "31-59",
    //                 "count" => (clone $residentQuery)->whereBetween('age', [31, 59])->count()
    //             ],
    //             [
    //                 "range" => "60+",
    //                 "count" => (clone $residentQuery)->where('age', '>=', 60)->count()
    //             ],
    //         ],

    //         "live_incidents" => Incident::query()
    //             ->when($user->role === "bdrrmo_admin", function ($q) use ($barangay) {
    //                 $q->where('barangay', $barangay);
    //             })
    //             ->orderByDesc('created_at')
    //             ->limit(10)
    //             ->get([
    //                 'id',
    //                 'type',
    //                 'location',
    //                 'status',
    //                 'incident_datetime',
    //                 'created_at'
    //             ]),

    //     ]);
    // }

    public function mdrrmo(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // ================= BDRRMO =================
        if ($user->role === 'bdrrmo_admin') {

            $barangay = $user->barangay;

            abort_unless($barangay, 403, 'Unauthorized');

            $residentQuery = Resident::where('barangay', $barangay);
            $incidentQuery = Incident::where('barangay', $barangay);
            $blotterQuery = Blotter::where('barangay', $barangay);
            $concernQuery = Concern::where('barangay', $barangay);
            $certificateQuery = Certificate::where('barangay', $barangay);
            $appUserQuery = MobileUser::where('barangay', $barangay);
            $ordinanceQuery = Ordinance::where('barangay', $barangay);

            return response()->json([
                "role" => $user->role,

                "residents" => $residentQuery->count(),
                "voters" => (clone $residentQuery)->where('is_voter', 1)->count(),
                "male" => (clone $residentQuery)->where('gender', 'Male')->count(),
                "female" => (clone $residentQuery)->where('gender', 'Female')->count(),

                "blotters" => $blotterQuery->count(),
                "concerns" => $concernQuery->count(),
                "certificates" => $certificateQuery->count(),

                "app_users" => $appUserQuery->count(),
                "ordinances" => $ordinanceQuery->count(),
                "incidents" => $incidentQuery->count(),

                "incident_trend" => $incidentQuery
                    ->selectRaw("DATE(created_at) as date, COUNT(*) as total")
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),

                "live_incidents" => Incident::where('barangay', $barangay)
                    ->whereNotIn('status', ['resolved', 'declined'])
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get([
                        'id',
                        'type',
                        'location',
                        'gps_location',
                        'description',
                        'status',
                        'incident_datetime',
                        'created_at'
                    ]),

                "gender_distribution" => [
                    [
                        "name" => "Male",
                        "value" => (clone $residentQuery)->where('gender', 'Male')->count()
                    ],
                    [
                        "name" => "Female",
                        "value" => (clone $residentQuery)->where('gender', 'Female')->count()
                    ],
                ],

                "age_distribution" => [
                    [
                        "range" => "0-17",
                        "count" => (clone $residentQuery)->whereBetween('age', [0, 17])->count()
                    ],
                    [
                        "range" => "18-30",
                        "count" => (clone $residentQuery)->whereBetween('age', [18, 30])->count()
                    ],
                    [
                        "range" => "31-59",
                        "count" => (clone $residentQuery)->whereBetween('age', [31, 59])->count()
                    ],
                    [
                        "range" => "60+",
                        "count" => (clone $residentQuery)->where('age', '>=', 60)->count()
                    ],
                ],
            ]);
        }

        // ================= MDRRMO =================
        if ($user->role === 'mdrrmo_admin') {
            $municipality = $user->municipality;

            abort_unless($municipality, 403, 'Unauthorized');

            $residentQuery = Resident::where('city_municipality', $municipality);
            $incidentQuery = Incident::where('municipality', $municipality);
            // $blotterQuery = Blotter::where('municipality', $municipality);
            // $concernQuery = Concern::where('municipality', $municipality);
            // $certificateQuery = Certificate::where('municipality', $municipality);
            $appUserQuery = MobileUser::where('municipality', $municipality);
            // $ordinanceQuery = Ordinance::where('municipality', $municipality);

            return response()->json([

                "role" => $user->role,
                "residents" => $residentQuery->count(),
                "voters" => (clone $residentQuery)->where('is_voter', 1)->count(),
                "male" => (clone $residentQuery)->where('gender', 'Male')->count(),
                "female" => (clone $residentQuery)->where('gender', 'Female')->count(),

                // "blotters" => $blotterQuery->count(),
                // "concerns" => $concernQuery->count(),
                // "certificates" => $certificateQuery->count(),

                "app_users" => $appUserQuery->count(),
                // "ordinances" => $ordinanceQuery->count(),
                "incidents" => $incidentQuery->count(),

                "incident_trend" => $incidentQuery
                    ->selectRaw("DATE(created_at) as date, COUNT(*) as total")
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),

                "live_incidents" => Incident::where('municipality', $municipality)
                    ->whereNotIn('status', ['resolved', 'declined'])
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get([
                        'id',
                        'type',
                        'location',
                        'gps_location',
                        'description',
                        'status',
                        'incident_datetime',
                        'created_at'
                    ]),


                "gender_distribution" => [
                    [
                        "name" => "Male",
                        "value" => (clone $residentQuery)->where('gender', 'Male')->count()
                    ],
                    [
                        "name" => "Female",
                        "value" => (clone $residentQuery)->where('gender', 'Female')->count()
                    ],
                ],

                "age_distribution" => [
                    [
                        "range" => "0-17",
                        "count" => (clone $residentQuery)->whereBetween('age', [0, 17])->count()
                    ],
                    [
                        "range" => "18-30",
                        "count" => (clone $residentQuery)->whereBetween('age', [18, 30])->count()
                    ],
                    [
                        "range" => "31-59",
                        "count" => (clone $residentQuery)->whereBetween('age', [31, 59])->count()
                    ],
                    [
                        "range" => "60+",
                        "count" => (clone $residentQuery)->where('age', '>=', 60)->count()
                    ],
                ],

            ]);
        }

        return response()->json([
            // "role" => $user->role ?? null,
            "error" => "Unauthorized role"
        ], 403);
    }
}
