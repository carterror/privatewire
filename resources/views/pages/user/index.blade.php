@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    <h1>Users</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header row">
                    
                    <div class="col-md-8">
                        {{ __('Users') }}
                    </div>
                    
                    <div class="col-md-4">
                        <a href="{{route('users.create')}}" type="button" class="btn btn-block bg-gradient-danger">New User</a>
                    </div>
                    
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                      <thead>
                        <tr>
                          <th>Email</th>
                          <th>Server VPN</th>
                          <th>Server DNS</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($users as $user)

                          <tr>
                            <td>{{$user->email}}</td>
                            <td>{{$user->server->name}}</td>
                            <td>{{$user->dns}}</td>
                            <td>

                                <a href="{{route('users.show', $user)}}">
                                  @if (!$user->status)
                                    <span class="badge bg-danger">
                                    <i class="fas fa-fw fa-square"></i> Desabilitado
                                    </span>
                                  @else
                                    <span class="badge bg-success">
                                      <i class="fas fa-fw fa-check-square"></i> Halibitado
                                    </span>
                                  @endif
                                  </a>
                            </td>
                          </tr>

                        @endforeach

                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer">
                    {{$users->links()}}
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