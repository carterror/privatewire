<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Hub;

class Expire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expire:hub';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ha expirado el Hub';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $bin = "wgtool_netw ./net_log /etc/wgtool_netw/pubkey.pem";
        $hubs = Hub::with(['server'])->where('status', '1')->get();

        foreach ($hubs as $hub) {
            
            if ($hub->billing < date('Y-m-d')) {

                $h = Hub::find($hub->id);
                $h->status = 0;

                if ($h->save()) {

                    $dns = "localhost"; //prueba

                    $host = " ".$dns." wgtool /etc/wireguard/";

                    exec($bin.$host." useroff ".$h->server->name." ".$h->name, $r);           
                    //          useroff      wgX.conf            bill

                    $texto = "[".date('Y-m-d H:i:s')."]: Expire hub - ".$h->server->name." -> ".$h->name;

                    Storage::append("expire.log", $texto);

                }

            }
        }
    }
}
