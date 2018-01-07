<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Group;
use App\GroupUser;
use Auth;

class GroupController extends Controller
{
    public function groups()
    {
        return view('group.groups', ['auth' => Auth::check(), 'authId' => Auth::id()]);
    }

    public function view($id)
    {
        try {
            $group = Group::find($id);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect('/groups');
        }

        if(!Auth::check()) {
            $auth = false;
        } else {
            $groupAttendance = GroupUser::where('user_id', Auth::id())->where('group_id', $group->id)->first();

            $auth = is_null($groupAttendance) ? false : $groupAttendance->isAdmin();
        }

        return view('group.view', ['group' => $group, 'isAllowed' => $auth]);
    }

    public function options(Request $request, $id)
    {
        try {
            $group = Group::find($id);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect('/groups');
        }

        $attendance = $this->checkAttendance($group->id, Auth::id());

        if(!$attendance || !$attendance->isAdmin()) {
            return redirect('/groups');
        }

        return view('group.options', ['group' => $group]);
    }

    public function checkAttendance($groupId, $userId)
    {
        $groupAttendance = GroupUser::where('user_id', $userId)->where('group_id', $groupId)->first();

        if(is_null($groupAttendance)) {
            return false;
        }

        return $groupAttendance;
    }
}
