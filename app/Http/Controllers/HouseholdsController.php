<?php

namespace App\Http\Controllers;

use App\Http\Requests\HouseholdsRequest;
use App\Models\Households;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Logs;
use App\Models\HouseholdHead;
use Illuminate\Support\Facades\Hash;
use App\Models\BarangayOfficials;
use App\Models\HouseholdHeadMember;
use App\Models\Environment;
use App\Models\ChildInformation;
use App\Models\MotherInformation;
use App\Models\Residents;
use App\Models\CovidStatus;
use Carbon\Carbon;

class HouseholdsController extends Controller
{
    public function householdHeads()
    {
        return response()->json(Households::with("zones")->paginate(10), 200);
    }
    // householdHeads
    public function index(Request $request)
    {
        return response()->json(
            HouseholdHead::search($request->search)
            ->with(["zones", "familyMembers"])
            ->orderBy('created_at', 'asc')
            ->paginate(10), 200);
    }

    public function householdHead(Request $request)
    {
        return response()->json(HouseholdHead::search($request->search)->with(["zones"])->orderBy('created_at', 'asc')->get(), 200);
    }

    public function householdMembers(Request $request)
    {
        return response()->json(HouseholdHeadMember::search($request->search)->with(["zones"])->orderBy('created_at', 'asc')->get(), 200);
    }
    
    public function householdEnvs()
    {
        return response()->json(Environment::with("householdHead")->paginate(10), 200);
    }

    public function childInformations()
    {
        return response()->json(ChildInformation::with("household")->paginate(10), 200);
    }

    public function motherInformations()
    {
        return response()->json(MotherInformation::with(["household", "householdHead"])->paginate(10), 200);
    }

    public function store(HouseholdsRequest $request)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Added a household head.'
        ]);
        $householdHead = HouseholdHead::create($request->except('remember_token'));
        $resident_id = $this->createUser($householdHead);
        $householdHead->update(['resident_id' => $resident_id]);
        if($request->relationship == 'Mother'){
            $this->addAsMotherDirect('household_head_id',  $householdHead->id);
        }
        Environment::firstOrCreate([
            'household_head_id' => $householdHead->id
        ]);
        return response()->json(200);
    }


    
    public function newHouseholdMember(HouseholdsRequest $request)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Added a household member.'
        ]);
        
        $householdMember = HouseholdHeadMember::create($request->except('remember_token'));
        $resident_id = $this->createUser($householdMember);
        $householdMember->update(['resident_id' => $resident_id]);
        if($request->relationship == 'Mother'){
            $this->addAsMotherDirect('household_head_member_id',  $householdMember->id);
        }
        $age = Carbon::parse($householdMember->birthdate)->age;
        if($age <= 7){
            ChildInformation::create([
                'household_head_member_id' => $householdMember->id
            ]);
        }
        return response()->json(200);
    }

    private function addAsMotherDirect($id_field, $id)
    {
        $mother = MotherInformation::firstOrCreate([
            $id_field => $id
        ]);

    }

    public function addAsMother(Request $request)
    {
        
        $id_field = $request->is_head ? 'household_head_id' : 'household_head_member_id';
        $mother = MotherInformation::firstOrCreate([
            $id_field => $request->id
        ]);
        if($mother){
            return response()->json(200);
        }
        return response()->json(422);

    }

    public function updateHouseholdHeadRecord(HouseholdsRequest $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Updated a household number.'
        ]);
        $household = HouseholdHead::find($id);
        $household->update($request->except('remember_token'));
        if($request->relationship == 'Mother'){
            $this->addAsMotherDirect('household_head_id',  $household->id);
        }
        return response()->json($household, 200);
    }

    public function updateHouseholdMemberRecord(HouseholdsRequest $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Updated a household number.'
        ]);
        $household = HouseholdHeadMember::find($id);
        $household->update($request->except(['remember_token', 'household_head_id']));
        if($request->relationship == 'Mother'){
            $this->addAsMotherDirect('household_head_member_id',  $household->id);
        }
        return response()->json($household, 200);
    }

    public function updateHouseholdEnvRecord(Request $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Updated a household environment record.'
        ]);
        $envRecord = Environment::find($id);
        $envRecord->update($request->except(['remember_token', 'household_head_id']));
        return response()->json($envRecord, 200);
    }

    public function updateChildInformation(Request $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Updated a child health record.'
        ]);
        $record = ChildInformation::find($id);
        $record->update($request->except(['remember_token', 'household_head_member_id']));
        return response()->json($record, 200);
    }

    public function getResidentIdFromMember(Request $request)
    {

        $data = $request->is_head ? HouseholdHead::find($request->id) : HouseholdHeadMember::find($request->id);
        $resident = Residents::where('zone_id', $data->zone_id)
            ->where('first_name', $data->first_name)
            ->where('middle_name', $data->middle_name)
            ->where('last_name', $data->last_name)
            ->where('birthdate', $data->birthdate)
            ->first();
        
        $data->update(['resident_id' => $resident ? $resident->id : $this->createUser($data)]);
        return response()->json($data, 200);
    }

    public function updateMotherInformation(Request $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Updated a mother record.'
        ]);
        $record = MotherInformation::find($id);
        $record->update($request->except(['remember_token', 'household_head_member_id']));
        return response()->json($record, 200);
    }

    public function deleteHouseholdMemberRecord(Request $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Updated a household number.'
        ]);
        $household = HouseholdHeadMember::find($id);
        $fullname = $household->first_name.$household->middle_name.$household->last_name;
        User::where('name', $fullname)->update('status', 'non-active');
        $household->delete();
        return response()->json($household, 200);
    }

    public function destroy($id, Request $request)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Deleted a household number.'
        ]);
        return response()->json(Households::destroy($id), 200);
    }

    private function createUser($member)
    {
        $resident = $this->createResidentFromMember($member);

        $covid = new CovidStatus;
        $covid->resident_id = $resident->id;
        $covid->vaccination_type = "Unvaccinated";
        $covid->dose_num = 0;
        $covid->booster_type = 'Unvaccinated';
        $covid->reason = "None";
        $covid->save();

        $fullname = $member->first_name.' '.$member->middle_name.' '.$member->last_name;
        $user = new User;
        $user->name = str_replace(' ', '', $fullname);
        $user->password = Hash::make(str_replace(' ', '', $fullname));
        $user->email = str_replace(' ', '', $fullname) . '' . str_replace('-', '', $member->birthdate) . '' . "@gmail.com";
        $user->permission = 'resident';
        $user->remember_token = $resident->remember_token;
        $user->save();

        return $resident->id;
    }

    private function createResidentFromMember($member)
    {
        return Residents::create([
            'zone_id'     => $member->zone_id,
            'first_name' => $member->first_name,
            'middle_name' => $member->middle_name,
            'last_name' => $member->last_name,
            'place_of_birth' => '',
            'age' => $member->age,
            'civil_status' => $member->civil_status,
            'birthdate' => $member->birthdate,
            'sex' => $member->sex,
            'voter_status' => $member->sex,
            'identified_as' => '',
            'contact_num' => '',
            'pwd_status' => $member->pwd_status,
            'status' => "Approved",
            'address' => '',
            'remember_token' => Hash::make(uniqid())
        ]);
    }
}