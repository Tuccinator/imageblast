<?php
namespace App\Http\Controllers;

use App\Group;
use App\GroupUser;
use Auth;

class GroupController extends Controller
{
    public function groups()
    {
        $groups = Group::orderBy('id', 'desc')->get();

        return view('group.groups', ['groups' => $groups]);
    }

    public function view($id)
    {
        $group = Group::find($id);

        if(is_null($group)) {
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
}
