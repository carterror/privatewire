@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    <h1>Hubs</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header row">
                    
                    <div class="col-md-8">
                        {{ __('Hubs') }} de <b>{{$user->email}}</b>
                    </div>
                    
                    <div class="col-md-4">
                        <a href="{{route('hubs.edit', $user)}}" type="button" class="btn btn-block bg-gradient-danger">New Hub</a>
                    </div>
                    
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Server VPN</th>
                          <th>Server DNS</th>
                          <th colspan="2">Expire</th>
                          <th>Status</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($hubs as $hub)

                          <tr>
                            <td>{{$hub->name}}</td>
                            <td>{{$hub->server->name}}</td>
                            <td>{{$hub->dns}}</td>
                            <form action="{{route('billing', $hub)}}" method="POST">
                              @csrf
                              <td>
                                <input class="form-control" type="date" value="{{$hub->billing->format('Y-m-d')}}" name="billing" style="min-width: 50px !important;">
                              </td><td>
                                <button type="submit" class="form-control btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Change">
                                  <i class="fas fa-fw fa-check"></i>
                                </button>
                              </td>
                            </form>
                            <td>
                                <a href="{{route('hubs.show', $hub)}}" data-toggle="tooltip" data-placement="top" title="Change">
                                  @if (!$hub->status)
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
                              <a href="{{route('hubs.delete', $hub->id)}}" class="delete btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="Delete">
                                <i class="fas fa-fw fa-trash"></i>
                                </a>
                            </td>
                          </tr>

                        @endforeach

                      </tbody>
                    </table>
                  </div>
                  <div class="card-footer">
                    {{$hubs->links('vendor.adminlte.pagination.bootstrap-4')}}
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
<script>

</script>
@stop