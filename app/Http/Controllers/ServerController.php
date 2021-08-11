<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServerController extends Controller
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
        $servers = Server::paginate(9);
        return view('pages.server.index', compact('servers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.server.create');
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
            "name" => "required|unique:servers",
            "ip" => "required",
            "port" => "required|numeric|unique:servers",
            "nat" => "required",
            "loc" => "required",
            "range" => "required|unique:servers"
        ]);

        if (!Str::endsWith($request->name, '.conf')) {
            return back()->with(['type' => 'error'])->with(['message' => 'The name must end in ".conf"']);
        }

        $range = substr($request->range, -2);
        if(Str::contains($range, '/')):
            $range = substr($range, -1);
        endif;

        if ($range > 32 || $range < 0) {
            return back()->with(['type' => 'error'])->with(['message' => 'The range ip, is incorrect']);
        }

        $ips = pow(2 ,(32-$range))-2;

        $archivo = public_path('serverslist/'.Str::slug($request->name));

        $this->dns=" localhost";

        $host = $this->dns." wgtool /etc/wireguard/";
       // exec( "wgtool_netw ./net_log /etc/wgtool_netw/pubkey.pem localhost wgtool /etc/wireguard/ serverrule carter234.conf 10.0.11.14/29 eth0 add", $r);
       // return $r;
        exec($this->bin.$host." addserver ".$request->name." ".$request->range." ".$request->port." ".$request->ip, $r);
        
        //                      addserver     wgX.conf           10.0.0.1/24             12345           1.1.1.1
        exec($this->bin.$host." serverrule ".$request->name." ".$request->range." ".$request->nat." add", $r2);
        //                serverrule                                            wgX.conf            10.0.0.1/24         eth0       (add | del)

        if (!$r && !$r2) {

            if (!File::exists($archivo)) {
                mkdir($archivo);
            }

                Server::create([
                    'name' => $request->name,
                    'range' => $request->range,
                    'ip' => $request->ip,
                    'nat' => $request->nat,
                    'hubs' => $ips,
                    'loc' => $request->loc,
                    'port' => $request->port
                ]);

            return redirect()->route('servers.index')->with(['type' => 'success'])->with(['message' => 'Server created']);

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
    public function show(Server $server)
    {

        exec($this->bin." ".$this->dns." mkdir -p ".$this->path.Str::slug($server->name), $r);

        $host = " ".$this->dns." wgtool /etc/wireguard/";

        exec($this->bin.$host." getlog ".$server->name." ".$this->path.Str::slug($server->name)."/server.log", $r);

        $getfile = " ".$this->dns." build-in:getfile";

        exec($this->bin.$getfile." ".$this->path.Str::slug($server->name)."/server.log ./serverslist/".Str::slug($server->name)."/server.log", $r);
        
        $filename = public_path('serverslist/'.Str::slug($server->name).'/server.log');

        $server = $server->name;

        return view('pages.server.status',compact('filename', 'server'));
    }

    public function netlog()
    {
        $filename = public_path('net_log');
        if (!File::exists($filename)) {
            return back()->with(['type' => 'error'])->with(['message' => 'Network log not exist']);
        }else {
            $server = 'Network';
            return view('pages.server.status', compact('filename', 'server')); 
        } 
        
    }

    public function expire()
    {
        $filename = storage_path('app/expire.log');
        if (!File::exists($filename)) {
            return back()->with(['type' => 'error'])->with(['message' => 'Network log not exist']);
        }else {
            $server = 'Network';
            return view('pages.server.status', compact('filename', 'server')); 
        } 
        
    }

    public function serverop(Server $server, $id)
    {

        exec($this->bin." ".$this->dns." mkdir -p ".$this->path.Str::slug($server->name), $r);

        $host = " ".$this->dns." wgtool /etc/wireguard/";

        exec($this->bin.$host." serverop ".$server->name." ".$id." ".$this->path.Str::slug($server->name)."/stdout.log", $r);
                    //        1 serverop  2 wgX.conf        3 (start | stop | restart | status) 4 (./stdout.log | -)
        $getfile = " ".$this->dns." build-in:getfile";
        // return dd($r);
        exec($this->bin.$getfile." ".$this->path.Str::slug($server->name)."/stdout.log ./serverslist/".Str::slug($server->name)."/stdout.log", $r);

        $filename = public_path('serverslist/'.Str::slug($server->name).'/stdout.log');

        if ($id=="status") {
            $server = $server->name;
            return view('pages.server.status',compact('filename', 'server'));
        } else {
            return back()->with(['type' => 'info'])->with(['message' => 'Server '.$server->name.' '.$id]);
        }  

    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $server = Server::find($id);
        if($server->status){
            $server->status = 0;
            $msg= 'Disable';
        }else {
            $server->status = 1;
            $msg= 'Active';
        }

        if ($server->save()) {
            return back()->with(['type' => 'info'])->with(['message' => $server->name.', status, '.$msg]);
        } 
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
    public function delete($id)
    {
        $server = Server::find($id);

        $name = public_path('serverslist/'.Str::slug($server->name));

        if ($server->delete()) {

            $this->serverop($server, 'stop');

            $host = " ".$this->dns." wgtool /etc/wireguard/";

            exec($this->bin.$host." serverrule ".$server->name." ".$server->range." ".$server->nat." del", $r);
            //              serverrule      wgX.conf          10.0.0.1/24         eth0        (add | del)
            exec($this->bin.$host." delserver ".$server->name, $r);
            //                delserver     wgX.conf

            if (File::exists($name)) {
                File::deleteDirectory($name);
            }
            
            return back()->with(['type' => 'success'])->with(['message' => 'Server deleted']);

        } 
    }
}
