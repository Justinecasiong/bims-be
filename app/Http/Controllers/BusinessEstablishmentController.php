<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Logs;
use App\Models\BusinessEstablishment;

class BusinessEstablishmentController extends Controller
{
    public function businessEstablishments(Request $request)
    {
        return response()->json(BusinessEstablishment::search($request->search)->paginate(10), 200);
    }

    public function newBusinessEstablishment(Request $request)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Added a business establshiment record.'
        ]);
        return response()->json(BusinessEstablishment::create($request->except('remember_token')), 200);
    }
    
    public function updateBusinessEstablishment(Request $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Updated a business establshiment record.'
        ]);
        $bsEstablishment = BusinessEstablishment::find($id);
        $bsEstablishment->update($request->except('remember_token'));
        return response()->json($bsEstablishment, 200);
    }

    public function deleteBusinessEstablishment(Request $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Deleted a business establshiment record.'
        ]);
        return response()->json(BusinessEstablishment::destroy($id), 200);
    }
}