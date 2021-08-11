@extends('adminlte::page')

@section('title', 'Password')

@section('content_header')
    <h1>Change Password</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card card-danger">
            <div class="card-header">
              <h3 class="card-title">Change Password</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{route('users.update')}}" method="POST">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <label for="passo">Password Old</label>
                  <input type="password" class="form-control" id="passo" name="passo">
                </div>
                <div class="form-group">
                  <label for="pass">Password</label>
                  <input type="password" class="form-control" id="pass" name="pass">
                </div>
                <div class="form-group">
                  <label for="passv">Password Verification</label>
                  <input type="password" class="form-control" id="passv" name="passv">
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


