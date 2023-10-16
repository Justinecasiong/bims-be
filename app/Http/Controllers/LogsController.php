<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index()
    {
        $logs  = Logs::orderBy('created_at', 'DESC')->paginate(10);
        return response()->json($logs, 200);
    }
}
