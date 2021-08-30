<?php

namespace App\Http\Controllers;

use App\Models\Hub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Nette\Utils\Image;

class ImagesController extends Controller
{
    //'storage/'.Str::slug($profile->server->name).'/'.Str::slug(Auth::user()->email).'/'.Str::slug($profile->name).'/'.$profile->name.'.conf.png

    public function confload($id)
    {
        if (Str::contains($id, 'wire')) {

            return Response::download(storage_path('config/'.Str::substr($id, 4)));
            
        } else {
            $profile = Hub::with(['user', 'server'])->findOrFail($id);
            $conf = storage_path('serverslist/'.Str::slug($profile->server->name).'/'.Str::slug(Auth::user()->email).'/'.Str::slug($profile->name).'/'.$profile->name.'.conf');
            $header = ['Content-Type' => mime_content_type($conf)];
            return Response::download($conf, $header);
        }
         
    }

    public function confimage($id, $size = 40)
    {
        if ($id == 'qr') {

            $image = storage_path('config/qr.png');
    
            return Image::fromFile($image);

        } else {

            $profile = Hub::with(['user', 'server'])->findOrFail($id);

            $image = storage_path('serverslist/'.Str::slug($profile->server->name).'/'.Str::slug(Auth::user()->email).'/'.Str::slug($profile->name).'/'.$profile->name.'.conf.png');
    
            return Image::fromFile($image);

        }
        

    }

    

}
