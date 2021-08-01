@extends('adminlte::page')

@section('title', 'Servers')

@section('content_header')
    <h1>Servers</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header row">
                    
                    <div class="col-md-8">
                        {{ __('Servers') }}
                    </div>
                    
                    <div class="col-md-4">
                        <a href="{{route('servers.create')}}" type="button" class="btn btn-block bg-gradient-danger">New Server</a>
                    </div>
                    
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>NAT Iface</th>
                          <th>Address IP</th>
                          <th>Port</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>

                        @foreach ($servers as $server)

                          <tr>
                            <td>{{$server->name}}</td>
                            <td>{{$server->nat}}</td>
                            <td>{{$server->ip}}</td>
                            <td>{{$server->port}}</td>
                            <td>
                              <a href="{{route('servers.show', $server)}}">
                                  <span class="badge bg-danger">
                                  <i class="fas fa-fw fa-square"></i>
                                  </span>
                                </a>
                          </td>
                            <td>  <form action="{{route('servers.destroy', $server)}}" method="POST">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-fw fa-trash"></i>
                                </button>
                              </form>
                              </td>
                          </tr>

                        @endforeach

                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer">
                    {{$servers->links()}}
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