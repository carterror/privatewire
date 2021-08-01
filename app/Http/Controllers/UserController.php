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

    public $bin = "wgtool /etc/wireguard/"; 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('type', 0)->with(['server'])->paginate(9);

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
            "email" => "required|unique:users",
            "pass" => "required",
            "passv" => "required|same:pass",
            "dns" => "required|ip",
            "server_id" => "required"
        ]);

        
        $server = Server::find($request->server_id);

        $archivo = public_path('serverslist/'.Str::slug($server->name).'/'.Str::slug($request->email));

        if (!File::exists($archivo)) {
            mkdir($archivo);
        }
        

        //return $bin." adduser ".Str::slug($server->name)." /home/vagrant/www/privatewire/public/serverslist/".Str::slug($server->name)."/".Str::slug($request->name)."/ ".$request->name." ".$request->dns;
        exec($this->bin." adduser ".$server->name." ./serverslist/".Str::slug($server->name)."/".Str::slug($request->email)."/ ".$request->email." ".$request->dns, $r);
                //  adduser      wgX.conf      /dir-for-user-profile                                                                                bill                8.8.8.8        
       
        if (!$r) {
            
            User::create([
                'email' => $request->email,
                'password' => Hash::make($request->pass),
                'server_id' => $request->server_id,
                'dns' => $request->dns
            ]);

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
        $server = Server::find($user->server_id);
        if($user->status){
            $user->status = 0;
            $msg= 'Disable';
            $action = 'useroff';
        }else {
            $user->status = 1;
            $msg= 'Active';
            $action = 'useron';
        }

        exec($this->bin." ".$action." ".$server->name." ".$user->email, $r);
        //                   useron      wgX.conf            bill

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
    public function destroy(User $user)
    {
        $server = Server::find($user->server_id);

        $name = public_path('serverslist/'.Str::slug($server->name).'/'.Str::slug($user->email));


        exec($this->bin." deluser ".$server->name." ".$user->email, $r);
        //           deluser      wgX.conf              bill
        
        if ($user->delete()) {

            if (File::exists($name)) {
                File::deleteDirectory($name);
            }
            
            return back()->with(['type' => 'success'])->with(['message' => 'User '.$user->email.' deleted']);

        } 
    }
}
