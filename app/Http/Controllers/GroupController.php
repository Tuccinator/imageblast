<?php
namespace App\Http\Controllers;

class GroupController extends Controller
{
    public function groups()
    {
        return view('group.groups');
    }
}
