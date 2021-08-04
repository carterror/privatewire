<?php

namespace App\Http\Controllers;

use App\Models\Hub;
use App\Models\Server;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('isadmin');
    }

    public $bin = "wgtool /etc/wireguard/"; 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('type', 0)->paginate(9);

        return view('pages.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "email" => "required|unique:users",
            "pass" => "required",
            "passv" => "required|same:pass"
        ]);
        
            User::create([
                'email' => $request->email,
                'password' => Hash::make($request->pass),
                'email_verified_at' => now(),
            ]);

            return redirect()->route('users.index')->with(['type' => 'success'])->with(['message' => 'User created']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $hubs = Hub::where('user_id', $user->id)->paginate(9);

        return view('pages.user.show', compact('hubs', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('pages.user.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        $request->validate([
            'passo' => 'required|min:8',
            'pass' => 'required|min:8',
            'passv' => 'required|same:pass'
        ]);

            if (Hash::check($request->passo, $user->password)):
                $user->password = Hash::make($request->pass);
            else: 
                return back()->with(['type' => 'error'])->with(['message' => 'Password old incorrect']);
            endif;

            if($user->save()):
                return redirect()->route('home')->with(['type' => 'success'])->with(['message' => 'Password Update']);
            endif;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {

        if ($user->delete()) {

            return back()->with(['type' => 'success'])->with(['message' => 'User '.$user->email.' deleted']);

        } 
    }
}
