@extends('adminlte::page')

@section('title', 'Servers')

@section('content_header')
    <h1>Create User</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card card-danger">
            <div class="card-header">
              <h3 class="card-title">Create User</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{route('users.store')}}" method="POST">
              @csrf
              <div class="card-body">
                <div class="form-group">
                  <label for="email">Name | Email</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="name@gmail.com">
                </div>
                <div class="form-group">
                  <label for="pass">Password</label>
                  <input type="password" class="form-control" id="pass" name="pass">
                </div>
                <div class="form-group">
                  <label for="passv">Password Verification</label>
                  <input type="password" class="form-control" id="passv" name="passv">
                </div>
                <div class="form-group">
                  <label for="dns">Server DNS</label>
                  <input type="text" class="form-control" id="dns" name="dns" placeholder="8.8.8.8">
                </div>
                <div class="form-group">
                  <label>Server VPN</label>
                  <select class="form-control" name="server_id">

                    @foreach ($servers as $server)

                      <option value="{{$server->id}}">{{$server->name}}</option>

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
  @if ($errors->any())
    <script>
          Swal.fire({
          position: 'top-end',
          icon: 'error',
          title: '{{$errors->first()}}',
          showConfirmButton: false,
          timer: 2000
          });
    </script>
  @endif

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


