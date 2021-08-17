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
        $profile = Hub::with(['user', 'server'])->findOrFail($id);

        return Response::download(storage_path('serverslist/'.Str::slug($profile->server->name).'/'.Str::slug(Auth::user()->email).'/'.Str::slug($profile->name).'/'.$profile->name.'.conf'));
    }

    public function confimage($id, $size = 40)
    {
        $profile = Hub::with(['user', 'server'])->findOrFail($id);

        $image = storage_path('serverslist/'.Str::slug($profile->server->name).'/'.Str::slug(Auth::user()->email).'/'.Str::slug($profile->name).'/'.$profile->name.'.conf.png');

        return Image::fromFile($image);
    }

    

}
