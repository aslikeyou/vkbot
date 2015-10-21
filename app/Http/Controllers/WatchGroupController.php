<?php

namespace App\Http\Controllers;

use App\OtherGroup;
use getjump\Vk\Core;
use getjump\Vk\Exception\Error;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class WatchGroupController extends Controller
{
    function __construct(Request $request)
    {
        if($request->route() === null) {
            return ;
        }
        $token = \Cache::get('token');
        $vk = Core::getInstance()->apiVersion('5.5');
        $vk->setToken($token);
        $request->route()->setParameter('vk', $vk);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('watchgroup.index', [
            'items' => OtherGroup::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('watchgroup.create', [
            'error' => \Session::get('error')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Core $vk)
    {
        try {
            $vk->request('groups.getById', [
                'group_ids' => $request->input('id')
            ])->each(function($k, $item) {
                $m = new OtherGroup();
                $m->id = $item->id;
                $m->name = $item->name;
                $m->screen_name = $item->screen_name;
                $m->save();
            });
            return redirect('watch_groups/');
        } catch(Error $e) {
//            dd($e->getMessage());
            return redirect('watch_groups/create')->with('error', $e->getMessage());
        }

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
        /** @var OtherGroup $a */
        $a = OtherGroup::find($id);
        $a->delete();
        return redirect('watch_groups/');
    }
}
