<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $servers = Server::paginate(2);
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
            "port" => "required|numeric",
            "nat" => "required"
        ]);
        
        $archivo = public_path('serverslist/'.Str::slug($request->name));
        
        $command = public_path('wgtool/wgtool');
        $out="ads";

        exec($command, $out, $r);

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
    public function destroy(Server $server)
    {

        $name = public_path('serverslist/'.Str::slug($server->name));

        if ($server->delete()) {

            if (File::exists($name)) {
                File::deleteDirectory($name);
            }
            
            return back()->with(['type' => 'success'])->with(['message' => 'Server deleted']);

        } 
    }
}
