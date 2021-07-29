<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('type', 0)->with(['server'])->paginate(2);

        return view('pages.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $servers = Server::all();
        return view('pages.user.create', compact('servers'));
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
            "name" => "required|unique:users",
            "pass" => "required",
            "passv" => "required|same:pass",
            "dns" => "required|ip",
            "server_id" => "required"
        ]);

        $command=public_path('wgtool/wgtool');
        $out="ads";

        exec($command, $out, $r);

        if (!$r) {
            
            User::create([
                'email' => $request->name,
                'password' => Hash::make($request->pass),
                'server_id' => $request->server_id,
                'dns' => $request->dns
            ]);

            $server = Server::find($request->server_id);

            $archivo = public_path('serverslist/'.Str::slug($server->name).'/'.Str::slug($request->name));

            if (!File::exists($archivo)) {
                mkdir($archivo);
            }

            return redirect()->route('users.index')->with(['type' => 'success'])->with(['message' => 'User created']);

        }else{
            return back()->with(['type' => 'error'])->with(['message' => 'Error']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if($user->status){
            $user->status = 0;
            $msg= 'Disable';
        }else {
            $user->status = 1;
            $msg= 'Active';
        }

        if ($user->save()) {
            return back()->with(['type' => 'error'])->with(['message' => $user->email.', status, '.$msg]);
        } 

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
    public function destroy($id)
    {
        //
    }
}
