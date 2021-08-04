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

    public $bin = "wgtool /etc/wireguard/";
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
            "ip" => "required|ip",
            "port" => "required|numeric|unique:servers",
            "nat" => "required",
            "range" => "required|unique:servers"
        ]);

        if (!Str::endsWith($request->name, '.conf')) {
            return back()->with(['type' => 'error'])->with(['message' => 'The name must end in ".conf"']);
        }

        $archivo = public_path('serverslist/'.Str::slug($request->name));

        exec($this->bin." addserver ".$request->name." ".$request->range." ".$request->port." ".$request->ip, $r);

        exec($this->bin." serverrule ".$request->name." ".$request->range." ".$request->nat." add", $r2);
        //                serverrule      wgX.conf            10.0.0.1/24         eth0       (add | del)

        if (!$r && !$r2) {

            if (!File::exists($archivo)) {
                mkdir($archivo);
            }

                Server::create([
                    'name' => $request->name,
                    'range' => $request->range,
                    'ip' => $request->ip,
                    'nat' => $request->nat,
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
        exec($this->bin." getlog ".$server->name." ./serverslist/".Str::slug($server->name)."/server.log", $r);
        //                getlog    wgX.conf        /etc/somedir/logfile

        $filename = public_path('serverslist/'.Str::slug($server->name).'/server.log');

        return view('pages.server.status',compact('filename', 'server'));
    }

    public function serverop(Server $server, $id)
    {
        // exec("/usr/bin/wgtool /etc/wireguard/"." serverop wg1.conf start ./stdout.log", $r);
        // return dd($r);
        exec($this->bin." serverop ".$server->name." ".$id." ./serverslist/".Str::slug($server->name)."/stdout.log", $r);
              //        1 serverop  2 wgX.conf        3 (start | stop | restart | status) 4 (./stdout.log | -)

        $filename = public_path('serverslist/'.Str::slug($server->name).'/stdout.log');

        if ($id=="status") {
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
    public function destroy(Server $server)
    {

        $name = public_path('serverslist/'.Str::slug($server->name));

        if ($server->delete()) {

            $this->serverop($server, 'stop');

            exec($this->bin." serverrule ".$server->name." ".$server->range." ".$server->nat." del", $r);
            //              serverrule      wgX.conf          10.0.0.1/24         eth0        (add | del)
            exec($this->bin." delserver ".$server->name, $r);
            //                delserver     wgX.conf

            if (File::exists($name)) {
                File::deleteDirectory($name);
            }
            
            return back()->with(['type' => 'success'])->with(['message' => 'Server deleted']);

        } 
    }
}
