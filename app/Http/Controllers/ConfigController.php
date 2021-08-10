<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.config.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function qr(Request $request)
    {
        $request->validate([
            'qr' => 'required|image',
        ]);

        $fileExt = trim($request->qr->getClientOriginalExtension());

        $upload_path = Config::get('filesystems.disks.config.root');

        $filename = 'qr.'.$fileExt;
    
        $foto = $upload_path.'/'.$filename;
        
        if(File::exists($foto)): File::delete($foto); endif;

        $request->qr->storeAs('/', $filename, 'config');

        return back()->with(['type' => 'success'])->with(['message' => 'Qr updated']);

    }
    
    
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function hash(Request $request)
    {
        Storage::disk('config')->put('hash', $request->hash);

        return back()->with(['type' => 'success'])->with(['message' => 'Hash updated']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function price(Request $request)
    {
        Storage::disk('config')->put('price', $request->price);

        //Storage::disk('config')->get('price');

        return back()->with(['type' => 'success'])->with(['message' => 'Price updated']);
    }
}
