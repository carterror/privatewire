@extends('adminlte::page')

@section('title', 'Servers')

@section('content_header')
    <h1>Create Hub</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card card-danger">
            <div class="card-header">
              <h3 class="card-title">Create Hub of <b>{{$user->email}}</b></h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{route('hubs.update', $user->id)}}" method="POST">
              @csrf
              @method('PUT')
              <div class="card-body">
                <div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" class="form-control" id="name" value="{{old('name')}}" name="name" placeholder="name">
                </div>
                <div class="form-group">
                  <label for="dns">Server DNS</label>
                  <input type="text" class="form-control" id="dns" value="{{old('dns')}}" name="dns" placeholder="8.8.8.8">
                </div>
                <div class="form-group">
                  <label>Server VPN</label>
                  <select class="form-control" name="server_id">

                    @foreach ($servers as $server)

                      <option value="{{$server->id}}">{{$server->loc}}</option>

                    @endforeach

                  </select>
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


