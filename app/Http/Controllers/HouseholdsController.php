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

class HouseholdsController extends Controller
{
    public function householdHeads()
    {
        return response()->json(Households::with("zones")->paginate(10), 200);
    }
    // householdHeads
    public function index(Request $request)
    {
        return response()->json(HouseholdHead::search($request->search)->with(["zones", "familyMembers"])->paginate(10), 200);
    }

    public function householdHead(Request $request)
    {
        return response()->json(HouseholdHead::search($request->search)->with(["zones"])->get(), 200);
    }

    public function householdMembers(Request $request)
    {
        return response()->json(HouseholdHeadMember::search($request->search)->with(["zones"])->get(), 200);
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
        return response()->json(MotherInformation::with("household")->paginate(10), 200);
    }

    public function store(Request $request)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Added a household head.'
        ]);
        $householdHead = HouseholdHead::create($request->except('remember_token'));
        $this->createUser($request, $householdHead);
        Environment::create([
            'household_head_id' => $householdHead->id
        ]);
        return response()->json(200);
    }
    
    public function newHouseholdMember(Request $request)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Added a household member.'
        ]);
        
        $householdMember = HouseholdHeadMember::create($request->except('remember_token'));
        $this->createUser($request, $householdMember);
        if($householdMember->age <= 7){
            ChildInformation::create([
                'household_head_member_id' => $householdMember->id
            ]);
        }
        if($householdMember->age >= 15 && $householdMember->age <= 49){
            MotherInformation::create([
                'household_head_member_id' => $householdMember->id
            ]);
        }
        return response()->json(200);
    }

    public function updateHouseholdHeadRecord(Request $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Updated a household number.'
        ]);
        $household = HouseholdHead::find($id);
        $household->update($request->except('remember_token'));
        return response()->json($household, 200);
    }

    public function updateHouseholdMemberRecord(Request $request, $id)
    {
        $users = User::where('remember_token', $request->remember_token)->first();
        Logs::create([
            'user_id' => $users->id,
            'user' => $users->name,
            'action' => 'Updated a household number.'
        ]);
        $household = HouseholdHeadMember::find($id);
        $household->update($request->except(['remember_token', 'household_head_id']));
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

    private function createUser($request, $member)
    {
        $resident = $this->createResidentFromMember($member);

        $covid = new CovidStatus;
        $covid->resident_id = $resident->id;
        $covid->vaccination_type = "Unvaccinated";
        $covid->dose_num = 0;
        $covid->booster_type = 'Unvaccinated';
        $covid->reason = "None";
        $covid->save();

        $fullname = $request->first_name.' '.$request->middle_name.' '.$request->last_name;
        $user = new User;
        $user->name = str_replace(' ', '', $fullname);
        $user->password = Hash::make(str_replace(' ', '', $fullname));
        $user->email = str_replace(' ', '', $fullname) . '' . str_replace('-', '', $request->birthdate) . '' . "@gmail.com";
        $user->permission = 'resident';
        $user->remember_token = $resident->remember_token;
        $user->save();

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