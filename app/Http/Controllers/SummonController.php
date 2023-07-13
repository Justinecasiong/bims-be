<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Logs;
use App\Models\SummonRecord;

class SummonController extends Controller
{
    public function summonRecords()
    {
        return response()->json(SummonRecord::paginate(10), 200);
    }

    public function newSummonRecord(Request $request)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Added a summon record.'
        ]);
        return response()->json(SummonRecord::create($request->except('remember_token')), 200);
    }
    
    public function updateSummonRecord(Request $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Updated a summon record.'
        ]);
        $summonRecord = SummonRecord::find($id);
        $summonRecord->update($request->except('remember_token'));
        return response()->json($summonRecord, 200);
    }

    public function deleteSummonRecord(Request $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Deleted a summon record.'
        ]);
        return response()->json(SummonRecord::destroy($id), 200);
    }
}