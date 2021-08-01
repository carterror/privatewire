<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServerController extends Controller
{
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
            "ip" => "required",
            "port" => "required|numeric",
            "nat" => "required"
        ]);

        $archivo = public_path('serverslist/'.Str::slug($request->name));

        //exec($bin." serverop ".$request->name." status ./serverslist/".Str::slug($request->name)."/status", $r);

        exec($this->bin." addserver ".$request->name." ". $request->ip." ".$request->port, $r);

        if (!$r) {

            if (!File::exists($archivo)) {
                mkdir($archivo);
            }

                Server::create([
                    'name' => $request->name,
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
        exec($this->bin." serverop ".$server->name." ".$id." ./serverslist/".Str::slug($server->name)."/stdout.log", $r);
        //              1 serverop  2 wgX.conf        3 (start | stop | restart | status) 4 (./stdout.log | -)
    }

    public function serverop(Server $server, $id)
    {
        exec($this->bin." serverop ".$server->name." ".$id." ./serverslist/".Str::slug($server->name)."/stdout.log", $r);
        //              1 serverop  2 wgX.conf        3 (start | stop | restart | status) 4 (./stdout.log | -)
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


            exec($this->bin." delserver ".$server->name, $r);
            //           delserver     wgX.conf

            if (File::exists($name)) {
                File::deleteDirectory($name);
            }
            
            return back()->with(['type' => 'success'])->with(['message' => 'Server deleted']);

        } 
    }
}
