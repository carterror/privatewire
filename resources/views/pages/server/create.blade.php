@extends('adminlte::page')

@section('title', 'Servers')

@section('content_header')
    <h1>Create Servers</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card card-danger">
            <div class="card-header">
              <h3 class="card-title">Create Servers</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{route('servers.store')}}" method="POST">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="server">
                </div>
                <div class="form-group">
                  <label for="range">Address IP</label>
                  <input type="text" class="form-control" id="range" name="range" placeholder="192.168.0.1/24">
                </div>
                <div class="form-group">
                  <label for="ip">Public IP</label>
                  <input type="text" class="form-control" id="ip" name="ip" placeholder="192.168.0.1">
                </div>
                <div class="form-group">
                  <label for="port">Port</label>
                  <input type="number" class="form-control" id="port" name="port" placeholder="3333">
                </div>
                <div class="form-group">
                  <label for="nat">NAT Iface</label>
                  <input type="text" class="form-control" id="nat" name="nat" placeholder="Eth0">
                </div>
                <div class="form-group">
                  <label for="loc">Location</label>
                  <input type="text" class="form-control" id="loc" name="loc" placeholder="New Jersey">
                </div>
              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <button type="submit" class="btn btn-danger">Create</button>
              </div>
            </form>
          </div>
    </div>
</div>
@stop

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')

@stop


