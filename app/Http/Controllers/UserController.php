<?php

namespace App\Http\Controllers;

use App\User;
use App\Apartment;
use DataTables;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        $apartments = Apartment::pluck('name', 'id');
        return view('Admin.User.view',compact('users','apartments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:20|unique:users',
            'email' => 'required|string|email|max:190|unique:users',
        ]);

        $user = User::create($request->all());
        $user->radreplies()->create([
            'attribute' => 'Tunnel-Type',
            'value' => '13'
        ]);
        $user->radreplies()->create([
            'attribute' => 'Tunnel-Medium-Type',
            'value' => '6'
        ]);
        return response('success');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'apartment_id' => 'required',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:20',
            'email' => 'required|string|email|max:190',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->all());

        if (!$user->radreplies()->where('attribute', 'Tunnel-Type')->first()) {
            $user->radreplies()->create([
                'attribute' => 'Tunnel-Type',
                'value' => '13'
            ]);
        }
        if (!$user->radreplies()->where('attribute', 'Tunnel-Medium-Type')->first()) {
            $user->radreplies()->create([
                'attribute' => 'Tunnel-Medium-Type',
                'value' => '6'
            ]);
        }

        return response('success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        User::destroy($request->id);

        return response('Success');
    }

    public function getDataTable()
    {
        $users = User::all();

        return DataTables::of($users)
            ->addColumn('edit', function ($user) {
                return '<button type="button" class="edit btn btn-sm btn-primary" data-apartment-id="' . $user->apartment_id . '" data-name="' . $user->name . '" data-username="' . $user->username . '" data-email="' . $user->email . '" data-id="' . $user->id . '">Edit</button>';
            })
            ->addColumn('delete', function ($user) {
                return '<button type="button" class="delete btn btn-sm btn-danger" data-delete-id="' . $user->id . '" data-token="' . csrf_token() . '" >Delete</button>';
            })
            ->rawColumns(['edit', 'delete'])
            ->make(true);
    }
}
