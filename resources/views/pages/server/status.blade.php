@extends('adminlte::page')

@section('title', 'Servers')

@section('content_header')
    <h1>Server status</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header row">
                    
                    <div class="col-md-8">
                        {!! __('Server <b>'.$server.'</b> status') !!}
                    </div>
                    
                </div>

                <div class="card-body p-10">
                    <code>
                        @foreach (file($filename) as $item)
                            <p class="@if(Str::contains($item, ['Active', 'address'])) bg-gradient-success @endif" style="padding-left: 5px; margin-bottom: 5px;">{{ $item }}</p>
                        @endforeach
                    </code>
                </div>
                  <div class="card-footer">
                  </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')

@stop