<?php
namespace App\Http\Controllers;

use App\Group;

class GroupController extends Controller
{
    public function groups()
    {
        $groups = Group::orderBy('id', 'desc')->get();

        return view('group.groups', ['groups' => $groups]);
    }
}
