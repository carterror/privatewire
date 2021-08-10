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
                          <th>Hubs</th>
                          <th>Status</th>
                          <th>Actions</th>
                          <th>Log</th>
                          <th>Delete</th>
                        </tr>
                      </thead>
                      <tbody>

                        @foreach ($servers as $server)

                          <tr>
                            <td>{{$server->name}}</td>
                            <td>{{$server->nat}}</td>
                            <td>{{$server->ip}}</td>
                            <td>{{$server->port}}</td>
                            <td>{{$server->hubs}}</td>
                            <td>
                              <a href="{{route('servers.edit', $server->id)}}">
                                @if (!$server->status)
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
                            <td>
                              <a href="{{route('serverop', [$server, 'start'])}}" data-toggle="tooltip" data-placement="top" title="Start">
                                  <span class="btn btn-sm btn-success">
                                  <i class="fas fa-fw fa-play-circle"></i>
                                  </span>
                                </a>

                              <a href="{{route('serverop', [$server, 'restart'])}}" data-toggle="tooltip" data-placement="top" title="Restart">
                                  <span class="btn btn-sm btn-warning">
                                  <i class="fas fa-fw fa-eject"></i>
                                  </span>
                                </a>

                              <a href="{{route('serverop', [$server, 'stop'])}}" data-toggle="tooltip" data-placement="top" title="Stop">
                                  <span class="btn btn-sm btn-danger">
                                  <i class="fas fa-fw fa-stop"></i>
                                  </span>
                                </a>
                              </td>
                              <td>
                              <a href="{{route('serverop', [$server, 'status'])}}" data-toggle="tooltip" data-placement="top" title="Status">
                                  <span class="btn btn-sm btn-secondary">
                                  <i class="fas fa-fw fa-history"></i>
                                  </span>
                                </a>

                              <a href="{{route('servers.show', $server)}}" data-toggle="tooltip" data-placement="top" title="Get Log">
                                  <span class="btn btn-sm btn-secondary">
                                    <i class="fas fa-fw fa-file"></i>
                                  </span>
                                </a>
                            </td>
                            <td>
                              <a href="{{route('servers.delete', $server->id)}}" class="delete btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="Delete">
                                <i class="fas fa-fw fa-trash"></i>
                              </a>
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

@stop