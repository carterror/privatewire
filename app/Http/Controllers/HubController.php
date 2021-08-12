<?php

namespace App\Http\Controllers;

use App\Models\Hub;
use App\Models\Server;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class HubController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('isadmin');
    }

    public $bin = "wgtool_netw ./net_log /etc/wgtool_netw/pubkey.pem";
    public $path = "/var/wgtool/";
    public $dns = "localhost"; // Hosting prueba
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hubs = Hub::where('type', 0)->with(['server', 'user'])->paginate(9);

        return view('pages.hub.index', compact('hubs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$date = date('Y-m-d H:i:s', strtotime('-'.$f->store->timefactura.' Minutes'));
    }

    public function billing(Request $request, Hub $hub)
    {
        $hub->billing = $request->billing;

        if ($hub->save()) {
            return back()->with(['type' => 'success'])->with(['message' => 'Expire update']);;
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Hub $hub)
    {
        $server = Server::find($hub->server_id);
        if($hub->status){
            $hub->status = 0;
            $msg= 'Disable';
            $action = 'useroff';
        }else {
            $hub->status = 1;
            $msg= 'Active';
            $action = 'useron';
        }

        $host = " ".$this->dns." wgtool /etc/wireguard/";

        exec($this->bin.$host." ".$action." ".$server->name." ".$hub->name, $r);
        //                         useron      wgX.conf            bill

        if ($hub->save()) {
            return back()->with(['type' => 'info'])->with(['message' => $hub->name.', status, '.$msg]);
        } 

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($user)
    {
        $servers = Server::where('hubs', '>', 0)->where('status', 1)->distinct('loc')->select('loc')->get();

        $user = User::find($user);

        return view('pages.hub.create', compact('servers', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user)
    {
        $request->validate([
            "name" => "required|unique:hubs",
            "dns" => "required",
            "server_id" => "required",
        ]);
        
        $server = Server::find($request->server_id);

        $user = User::find($user);

        $archivo = public_path('serverslist/'.Str::slug($server->name).'/'.Str::slug($user->email));

        if (!File::exists($archivo)) {
            mkdir($archivo);
        }

        $archivo = public_path('serverslist/'.Str::slug($server->name).'/'.Str::slug($user->email).'/'.Str::slug($request->name));

        if (!File::exists($archivo)) {
            mkdir($archivo);
        }

        exec($this->bin." ".$this->dns." mkdir -p ".$this->path.Str::slug($server->name)."/".Str::slug($user->email)."/".Str::slug($request->name), $r);

        $host = " ".$this->dns." wgtool /etc/wireguard/";

        exec($this->bin.$host." adduser ".$server->name." ".$this->path.Str::slug($server->name)."/".Str::slug($user->email)."/".Str::slug($request->name)."/ ".$request->name." ".$request->dns, $r);
                        //    adduser      wgX.conf         /dir-for-user-profile                                                                        bill             8.8.8.8  
        
        exec($this->bin.$host." useroff ".$server->name." ".$request->name, $r5);
        
        $getfile = " ".$this->dns." build-in:getfile";
        // return dd($r);
        exec($this->bin.$getfile." ".$this->path.Str::slug($server->name)."/".Str::slug($user->email)."/".Str::slug($request->name)."/".$request->name.".conf.png ./serverslist/".Str::slug($server->name)."/".Str::slug($user->email)."/".Str::slug($request->name)."/".$request->name.".conf.png", $r);
        exec($this->bin.$getfile." ".$this->path.Str::slug($server->name)."/".Str::slug($user->email)."/".Str::slug($request->name)."/".$request->name.".conf.zip ./serverslist/".Str::slug($server->name)."/".Str::slug($user->email)."/".Str::slug($request->name)."/".$request->name.".conf.zip", $r);

        if (!$r) {

            $hub = Hub::create([
                'name' => $request->name,
                'server_id' => $request->server_id,
                'user_id' => $user->id,
                'dns' => $request->dns
            ]);

            $server->hubs--;
            $server->save();

            return redirect()->route('users.show', $user)->with(['type' => 'success'])->with(['message' => 'Hub created']);

        }else{
            return back()->with(['type' => 'error'])->with(['message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $hub = Hub::find($id);

        $server = Server::find($hub->server_id);

        $user = User::find($hub->user_id);

        $name = public_path('serverslist/'.Str::slug($server->name).'/'.Str::slug($user->email).'/'.Str::slug($hub->name));

        $host = " ".$this->dns." wgtool /etc/wireguard/";

        exec($this->bin.$host." useroff ".$server->name." ".$hub->name, $r);

        exec($this->bin.$host." deluser ".$server->name." ".$hub->name, $r);
        //                      deluser      wgX.conf            bill
        
        if ($hub->delete()) {

            $server->hubs++;
            $server->save();

            if (File::exists($name)) {
                File::deleteDirectory($name);
            }
            
            return back()->with(['type' => 'success'])->with(['message' => 'Hubs '.$hub->name.' deleted']);

        } 
    }
}
