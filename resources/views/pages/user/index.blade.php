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
                          <th colspan="2">Balance</th>
                          <th>Created date</th>
                          <th colspan="2">Hubs</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($users as $user)

                          <tr>
                            <td>{{$user->email}}</td>
                            <form action="{{route('users.ballance', $user->id)}}" method="POST">
                              @csrf
                              <td>
                                <input class="form-control" type="text" value="{{$user->ballance}}" name="ballance" style="min-width: 50px !important;">
                              </td><td>
                                <button type="submit" class="form-control btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" title="Add founds">
                                  <i class="fas fa-fw fa-plus"></i>
                                </button>
                              </td>
                            </form>
                            <td>{{$user->created_at}}</td>
                            <td>{{$hubs->where('user_id', $user->id)->count()}}</td>
                            <td>                              
                              <a href="{{route('users.show', $user)}}" data-toggle="tooltip" data-placement="top" title="Hubs">
                              <span class="btn btn-sm btn-info">
                              <i class="fas fa-fw fa-file"></i>
                              </span>
                            </a></td>
                            <td>
                              <a href="{{route('users.delete', $user->id)}}" class="delete btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="Delete">
                                <i class="fas fa-fw fa-trash"></i>
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
@stop