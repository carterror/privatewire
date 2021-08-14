<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function getdownloads()
    {
        $downloads = Download::all();

        return view('pages.config.downloads', compact('downloads'));
    }

    public function postdownloads(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'so' => 'required',
        ]);
        $filename = null;
        
        if ($request->hasFile('app')) {
       
            $fileExt = trim($request->app->getClientOriginalExtension());
            $upload_path = Config::get('filesystems.disks.config.root');
            $name = Str::slug(str_replace($fileExt,'',$request->app->getClientOriginalName()));

            $filename= rand(1,999).'-'.$name.'.'.$fileExt;
            $final_file= $upload_path.'/'.$filename;

        }

        $download = Download::create([
            'name' => $request->name,
            'path' => $filename,
            'so' => $request->so,
            'code' => $request->code,
        ]);
        if ($request->hasFile('app')) {
        $request->app->storeAs('/', $filename, 'config');
        }

        return back()->with(['type' => 'success'])->with(['message' => 'Downloads updated']);
    }

    public function delete($id)
    {
        $download = Download::findorfail($id);

        $upload_path = Config::get('filesystems.disks.config.root');
        $final_file= $upload_path.'/'.$download->path;

        if ($download->delete()) {

            if (!is_null($download->path)) {
                if (File::exists($final_file)) {
                    unlink($final_file);
                }
            }
            return back()->with(['type' => 'success'])->with(['message' => 'Downloads deleted']);
            
        }

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
        
        //if(File::exists($foto.'.tmp')): File::delete($foto.'.tmp'); endif;

        $request->qr->storeAs('/', $filename.'.tmp', 'config');

        rename($foto.'.tmp', $foto);

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
        Storage::disk('config')->put('hash.tmp', $request->hash);
        rename(public_path('config/hash.tmp'), public_path('config/hash'));
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
        Storage::disk('config')->put('price.tmp', $request->price);
        rename(public_path('config/price.tmp'), public_path('config/price'));

        Storage::disk('config')->put('email.tmp', $request->email);
        rename(public_path('config/email.tmp'), public_path('config/email'));
        
        Storage::disk('config')->put('promo.tmp', $request->promo);
        rename(public_path('config/promo.tmp'), public_path('config/promo'));

        return back()->with(['type' => 'success'])->with(['message' => 'Config updated']);
    }
}
