<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\Hub;
use App\Models\Server;
use App\Models\Tx;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('isadmin')->only('index');
        $this->middleware('verified');  
    }
    public $bin = "wgtool_netw ./../storage/app/net_log /etc/wgtool_netw/pubkey.pem";
    public $path = "/var/wgtool/";
    public $dns = "localhost"; // Hosting prueba
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $users = User::count();
        $hubs = Hub::count();
        $hubsac= Hub::where('status', 1)->count();
        $hubsav= Server::where('status', 1)->sum('hubs');

        $data = ['users' => $users, 'hubs' => $hubs, 'hubsac' => $hubsac, 'hubsav' => $hubsav];

        return view('home', $data);
    }

    public function client()
    {
        $profiles = Hub::with(['server'])->where('user_id', Auth::user()->id)->get();

        $locations = Server::where('hubs', '>', 0)->where('status', 1)->distinct('loc')->select('loc')->get();

        return view('client.index', compact('profiles', 'locations'));
    }

    function getPlatform($user_agent) {
    $plataformas = array(
        'Windows 10' => 'Windows NT 10.0+',
        'Windows 8.1' => 'Windows NT 6.3+',
        'Windows 8' => 'Windows NT 6.2+',
        'Windows 7' => 'Windows NT 6.1+',
        'Windows Vista' => 'Windows NT 6.0+',
        'Windows XP' => 'Windows NT 5.1+',
        'Windows 2003' => 'Windows NT 5.2+',
        'Windows' => 'Windows otros',
        'iPhone' => 'iPhone',
        'iPad' => 'iPad',
        'Mac OS X' => '(Mac OS X+)|(CFNetwork+)',
        'Mac otros' => 'Macintosh',
        'Android' => 'Android',
        'BlackBerry' => 'BlackBerry',
        'Linux' => 'Linux',
    );
    foreach($plataformas as $plataforma=>$pattern){
        if (preg_match('/(?i)'.$pattern.'/', $user_agent))
            return $plataforma;
    }
    return 'Otras';
    }

    public function download()
    {
        $downloads = Download::get();

        $windows = $downloads->where('so', 'windows');

        $linux = $downloads->where('so', 'linux');

        $mac = $downloads->where('so', 'mac');

        $android = $downloads->where('so', 'android');

        $so = $this->getPlatform($_SERVER['HTTP_USER_AGENT']);

        return view('client.download', compact('windows', 'linux', 'mac', 'android', 'so'));
    } 

    public function addfunds(Request $request, $wallet)
    {

        $request->validate([
            'tx' => 'required|unique:txs',
            ], 
            [
                'tx.unique' => 'This transaction hash has already been used',
            ]);   

        Tx::create([
            'email_user' => Auth::user()->email,
            'tx' => $request->tx,
            'wallet' => Crypt::decrypt($wallet),
        ]);

        return back()->with(['type' => 'success'])->with(['message' => 'As soon as your transaction is validated, the founds you sent will be available']);

    }

    public function profile(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'loc' => 'required',
        ]);

        $server = Server::where('loc', $request->loc)->where('hubs', '>', 0)->where('status', 1);
        
        if ($server->count()) {

            $server = $server->first();

            $archivo = storage_path('serverslist/'.Str::slug($server->name).'/'.Str::slug(Auth::user()->email));

            if (!File::exists($archivo)) {
                mkdir($archivo);
            }
    
            $archivo = storage_path('serverslist/'.Str::slug($server->name).'/'.Str::slug(Auth::user()->email).'/'.Str::slug($request->name));
    
            if (!File::exists($archivo)) {
                mkdir($archivo);
            }
    
            exec($this->bin." ".$this->dns." mkdir -p ".$this->path.Str::slug($server->name)."/".Str::slug(Auth::user()->email)."/".Str::slug($request->name), $r1);
    
            $host = " ".$this->dns." wgtool /etc/wireguard/";
    
            exec($this->bin.$host." adduser ".$server->name." ".$this->path.Str::slug($server->name)."/".Str::slug(Auth::user()->email)."/".Str::slug($request->name)."/ ".$request->name." 8.8.8.8", $r2);
                            //    adduser      wgX.conf         /dir-for-user-profile   bill             8.8.8.8  
            exec($this->bin.$host." useroff ".$server->name." ".$request->name, $r5);
                                                                                                                    
            $getfile = " ".$this->dns." build-in:getfile";
            // return dd($r);
            exec($this->bin.$getfile." ".$this->path.Str::slug($server->name)."/".Str::slug(Auth::user()->email)."/".Str::slug($request->name)."/".$request->name.".conf.png ./../storage/serverslist/".Str::slug($server->name)."/".Str::slug(Auth::user()->email)."/".Str::slug($request->name)."/".$request->name.".conf.png", $r3);
            exec($this->bin.$getfile." ".$this->path.Str::slug($server->name)."/".Str::slug(Auth::user()->email)."/".Str::slug($request->name)."/".$request->name.".conf.zip ./../storage/serverslist/".Str::slug($server->name)."/".Str::slug(Auth::user()->email)."/".Str::slug($request->name)."/".$request->name.".conf.zip", $r4);
            exec($this->bin.$getfile." ".$this->path.Str::slug($server->name)."/".Str::slug(Auth::user()->email)."/".Str::slug($request->name)."/".$request->name.".conf ./../storage/serverslist/".Str::slug($server->name)."/".Str::slug(Auth::user()->email)."/".Str::slug($request->name)."/".$request->name.".conf", $r4);

            if (!$r1 && !$r2 && !$r3 && !$r4 && !$r5) {

                $hub = Hub::create([
                    'name' => $request->name,
                    'server_id' => $server->id,
                    'user_id' => Auth::user()->id,
                    'dns' => '8.8.8.8'
                ]);

                $server->hubs--;
                $server->save();
    
                return back()->with(['type' => 'success'])->with(['message' => 'Profile created successfully']);

            }else {

                return back()->with(['type' => 'error'])->with(['message' => 'If you see this message gor long time, get in touch with tecnical support']);
            
            }
        } else {

            return back()->with(['type' => 'error'])->with(['message' => 'Profiles exhausted in that location']);
        }

    }

    public function active(Request $request, $id)
    {
        $request->validate([
            'mounts' => 'required',
        ]);
        $money = $request->mounts*Storage::disk('config')->get('price');
        $user = User::findorfail(Auth::user()->id);

        if ($money <= $user->ballance) {

            $hub = Hub::with(['server'])->findorfail($id);

            if (date('Y-m-d') < $hub->billing) {
                $date = Carbon::parse($hub->billing);
                $date = $date->addMonth($request->mounts); 
            } else {
                $date = Carbon::now();
                $date = $date->addMonth($request->mounts);
            }

            $hub->billing = $date;
            $hub->status = 1;

            $host = " ".$this->dns." wgtool /etc/wireguard/";

            exec($this->bin.$host." useron ".$hub->server->name." ".$hub->name, $r);

            if (!$r) {
                if ($hub->save()) {
                    $user->ballance -= $money;
                    if ($user->save()) {
                        return back()->with(['type' => 'success'])->with(['message' => 'Profile activated']);
                    }
                }
            }else {
                return back()->with(['type' => 'error'])->with(['message' => 'If you see this message gor long time, get in touch with tecnical support']);
            }

        } else {
            return back()->with(['type' => 'error'])->with(['message' => 'Insufficient founds']);
        }
            
    }

    public function delete($id)
    {
        $hub = Hub::find($id);

        $server = Server::find($hub->server_id);

        $user = User::find($hub->user_id);

        $name = storage_path('serverslist/'.Str::slug($server->name).'/'.Str::slug($user->email).'/'.Str::slug($hub->name));

        $host = " ".$this->dns." wgtool /etc/wireguard/";

        exec($this->bin.$host." useroff ".$server->name." ".$hub->name, $r5);

        exec($this->bin.$host." deluser ".$server->name." ".$hub->name, $r);
        //                      deluser      wgX.conf            bill
        
        if ($hub->delete()) {

            $server->hubs++;
            $server->save();

            if (File::exists($name)) {
                File::deleteDirectory($name);
            }
            
            return back()->with(['type' => 'success'])->with(['message' => 'Profile '.$hub->name.' deleted']);

        } 
    }

}
