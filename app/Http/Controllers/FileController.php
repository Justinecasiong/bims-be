<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Models\Logs;
use App\Models\ReportType;
use App\Models\BarangayOfficials;

class FileController extends Controller
{
    private const CHAIRPERSON = 'Chairperson';

    public function index(Request $request)
    {
        $brgy_official = BarangayOfficials::where('remember_token', $request->remember_token)->with('position')->first();
        $search = $request->search;
        $report = File::where('report_type_id', $request->id)->where(function ($query) use ($brgy_official, $search) {
            if ($search) {
                $query->where('file_name', 'like', "%$search%");
            }
            if($brgy_official->position->position_description != self::CHAIRPERSON){
                $query->where('official_id', $brgy_official->id);
            }
        })->with('barangay_official')->paginate(10);

        return response()->json($report, 200);
    }

    public function findReportType(Request $request)
    {
        return ReportType::where('id', $request->id)->first();
    }


    public function reportType(Request $request)
    {
        $search = $request->search;
        $report = File::where('official_id', $request->id)->where(function ($query) use ($search, $request) {
            if ($search) {
                $query->where('file_name', 'like', "%$search%");
            }
            if ($request) {
                $query->where('report_type', $request->report_type);
            }
        })->paginate(10);

        return response()->json($report, 200);
    }

    public function formSubmit(Request $request)
    {
        $fileName = $request->file->getClientOriginalName();
        $request->file->move(public_path('upload'), $fileName);
        $brgy_official = BarangayOfficials::where('remember_token', $request->remember_token)->first();

        $file = new File();
        $file->file_name = $fileName;
        $file->official_id = $brgy_official->id;
        $file->report_type_id = $request->report_type_id;
        $file->report_type = $request->report_type;
        $file->save();
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Created a file report.'
        ]);

        return response()->json(200);
    }

    public function download(Request $request)
    {
        return Response::download(public_path('upload/' . $request->fileName));
    }

    public function getReportTypes(Request $request)
    {
        return ReportType::all();
    }

    public function addReportType(Request $request)
    {
        return ReportType::create(['name' => $request->name]);
    }

    public function removeReportType(Request $request)
    {
        if(!File::where('report_type_id', $request->report_id)->exists()){
            return ReportType::where('id', $request->report_id)->delete();
        }
        return response()->json(['message' => 'Cannot Be Remove! There are reports under this report type'], 422);
    }

    public function destroy($id, Request $request)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Deleted a file report.'
        ]);
        return response()->json(File::destroy($id), 200);
    }
}
