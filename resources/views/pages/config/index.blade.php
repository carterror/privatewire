@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
    <h1>Settings</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
              <form action="{{route('settings.price')}}" method="post">
                @csrf
                <div class="card-header">{{ __('Config') }}</div>
                <div class="row card-body">
                  <div class="form-group col-md-6">
                    <label for="price">Price / Mo</label>
                    <input type="text" class="form-control" value="{{Storage::disk('config')->get('price')}}" id="price" name="price" placeholder="2" required/>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="price">Promo</label>
                    <input type="text" class="form-control" value="{{Storage::disk('config')->get('promo')}}" id="promo" name="promo" placeholder="If 0 there is no active promotion" required/>
                  </div>
                  <div class="form-group col-md-12">
                    <label for="price">Email</label>
                    <input type="email" class="form-control" value="{{Storage::disk('config')->get('email')}}" id="email" name="email" placeholder="ejemplo@gmai.com" required/>
                  </div>
                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-danger">Save change</button>
                </div>
              </form>
            </div>
            <div class="card">
              <form action="{{route('settings.hash')}}" method="post">
                @csrf
              <div class="card-header">{{ __('Wallet') }}</div>
              <div class="card-body">
                <div class="form-group">
                  {{-- <label for="hash">Hash Transaction</label> --}}
                  <input type="text" class="form-control" id="hash" name="hash" value="{{Storage::disk('config')->get('hash')}}" placeholder="5qwe68r4qwx48q648rxq8...">
                </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-danger">Save change</button>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <form action="{{route('settings.qr')}}" method="post" enctype="multipart/form-data">
              @csrf
              <div class="card-header">{{ __('QR Code') }}</div>
              <img src="{{route('confimage', 'qr')}}" class="card-img-top" alt="..." style="padding: 20px;">
              <div class="card-body">
                    <div class="form-group">
                      {{-- <label for="exampleInputFile"></label> --}}
                      <div class="input-group">
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" id="exampleInputFile" name="qr">
                          <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                        </div>
                        <div class="input-group-append">
                          <span class="input-group-text" id="">QR Code</span>
                        </div>
                      </div>
                  </div>
              </div>
              <div class="card-footer">
                <button type="submit" class="btn btn-danger">Save change</button>
              </div>
            </form>
          </div>
      </div>
    </div>
</div>
@stop

@section('css')
    <style>
      input[type='file']:hover{
        cursor: pointer;
      }
    </style>
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')

@stop