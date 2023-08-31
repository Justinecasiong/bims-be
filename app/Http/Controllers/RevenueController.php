<?php

namespace App\Http\Controllers;

use App\Http\Requests\RevenueRequest;
use App\Models\Revenue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Logs;


class RevenueController extends Controller
{
    public function getRevenue(Request $request)
    {
        $startDate = $request->startDate ? Carbon::createFromFormat('Y-m-d', $request->startDate)->startOfDay() : '';
        $endDate = $request->endDate ?  Carbon::createFromFormat('Y-m-d', $request->endDate)->endOfDay() : '';
        $revenue = Revenue::with('residents')
            ->where(function ($query) use($startDate, $endDate) {
                if($startDate ){
                    $query->whereDate('date', ">=", $startDate);
                }
                if($endDate){
                    $query->where('date', "<=", $endDate);
                }
            })
            ->orderBy('created_at', 'ASC')->paginate(10);
        return response()->json($revenue, 200);
    }

    public function index(Request $request  )
    {
        $startDate = $request->startDate ? Carbon::createFromFormat('Y-m-d', $request->startDate)->startOfDay() : '';
        $endDate = $request->endDate ?  Carbon::createFromFormat('Y-m-d', $request->endDate)->endOfDay() : '';
        return response()->json(
            Revenue::with('residents')
            ->where(function ($query) use($startDate, $endDate) {
                if($startDate ){
                    $query->whereDate('date', ">=", $startDate);
                }
                if($endDate){
                    $query->where('date', "<=", $endDate);
                }
            })
            ->orderBy('created_at', 'ASC')
            ->get(), 200);
    }

    public function store(RevenueRequest $request)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Added a revenue.'
        ]);
        return response()->json(Revenue::create($request->except('remember_token')), 200);
    }

    public function getTotalRevenue(Request $request)
    {
        $revenue = Revenue::whereYear('created_at', $request->year)->get();
        $barangay_clearance = $revenue->where('details', 'Barangay Clearance')->sum('amount');
        $barangay_certificate =  $revenue->where('details',  '==', 'Barangay Certificate')->sum('amount');
        $business_certificate = $revenue->where('details', 'Business Permit')->sum('amount');
        $summon_letter = $revenue->where('details', 'Summon Letter')->sum('amount');
        return response()->json([
            'revenue' => $revenue,
            'barangay_clearance' =>  $barangay_clearance,
            'barangay_certificate' =>  $barangay_certificate,
            'business_certificate' =>  $business_certificate,
            'summon_letter' =>  $summon_letter
        ], 200);
    }

    public function getTotalRevenueByMonth()
    {
        $array = array();
        $months = [
            "January",
            "Febuary",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ];
        $count = 0;
        for ($i = 1; $i <= 12; $i++) {
            $months[$count] = Revenue::whereMonth('created_at', (string)$i)->get();
            $months[$count]->where('details', 'Business Permit')->sum('amount');

            $barangay_clearance = $months[$count]->where('details', 'Barangay Clearance')->sum('amount');
            $barangay_certificate =  $months[$count]->where('details',  '==', 'Barangay Certificate')->sum('amount');
            $business_certificate = $months[$count]->where('details', 'Business Permit')->sum('amount');
            $summon_letter = $months[$count]->where('details', 'Summon Letter')->sum('amount');
            $array[$count] =  [
                'barangay_clearance' =>  $barangay_clearance,
                'barangay_certificate' =>  $barangay_certificate,
                'business_certificate' =>  $business_certificate,
                'summon_letter' =>  $summon_letter
            ];
            $count++;
        }
        return response()->json([
            'revenue' => $array,

        ], 200);
    }
}
