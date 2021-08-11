@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>{{ __('Quick stats') }}</h1>
@stop

@section('content')
<div class="container">
    {{-- <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Quick stats') }}</div>

                <div class="card-body">

                </div>
            </div>
        </div>
    </div> --}}
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header text-center">{{ __('Users') }}</div>

                <div class="card-body text-center" style="font-size: 5rem; padding: 0px;">
                    {{$users}}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header text-center">{{ __('Hubs') }}</div>

                <div class="card-body text-center" style="font-size: 5rem; padding: 0px;">
                    {{$hubs}}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header text-center">{{ __('Hubs Active') }}</div>

                <div class="card-body text-center" style="font-size: 5rem; padding: 0px;">
                    {{$hubsac}}                  
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header text-center">{{ __('Hubs Avialable') }}</div>

                <div class="card-body text-center" style="font-size: 5rem; padding: 0px;">
                    {{$hubsav}}  
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
@if(Session::has('message'))
<script>
      Swal.fire({
        position: 'top-end',
        icon: '{{ Session::get("type") }}',
        title: '{{ Session::get("message") }}',
        showConfirmButton: false,
        timer: 2000
      });
</script>
@endif 
@stop