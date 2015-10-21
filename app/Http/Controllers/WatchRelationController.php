<?php

namespace App\Http\Controllers;

use App\MyGroup;
use App\OtherGroup;
use App\WatchGroup;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class WatchRelationController extends Controller
{
    const NAME = 'watch_relations';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $res = MyGroup::with('otherGroups')->get();
        return view(self::NAME.'.index', [
            'items' => $res
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(self::NAME.'.create', [
            'error' => \Session::get('error'),
           'myGroups' => MyGroup::all(),
           'otherGroups' => OtherGroup::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $myGroupId= $request->input('myGroupId');
        $otherGroupId= $request->input('otherGroupId');
        /** @var MyGroup $m */
        $m = MyGroup::find($myGroupId);
        $m->otherGroups()->attach($otherGroupId);
        return redirect('/'.self::NAME);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $d = explode(',', $id);
        /** @var MyGroup $myGroup */
        $myGroup = MyGroup::find($d[0]);
        $myGroup->otherGroups()->detach($d[1]);

        return redirect('/'.self::NAME);
    }
}
