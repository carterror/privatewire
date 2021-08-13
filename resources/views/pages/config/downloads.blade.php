@extends('adminlte::page')

@section('title', 'Downloads')

@section('content_header')
    <h1>Downloads</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
              <form action="{{route('settings.postdownloads')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-header">{{ __('Config') }}</div>
                <div class="row card-body">
                  <div class="form-group col-md-12">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" value="{{old('name')}}" id="name" name="name" placeholder="Wireguard x64" required/>
                  </div>
                  <div class="form-group col-md-12">
                    <label>S.O</label>
                    <select class="form-control" name="so">
  
                      <option value="windows">Windows</option>
                      <option value="linux">Linux</option>
                      <option value="mac">Mac</option>
                      <option value="android">Android</option>
  
                    </select>
                  </div>
                  <div class="form-group col-md-12">
                      <label for="code">Code</label>
                    <textarea class="form-control" id="code" name="code" style="height: 100px" >{{old('code')}}</textarea>

                  </div> 
                  <div class="form-group col-md-12">
                    <label for="exampleInputFile">Apllication</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="exampleInputFile" name="app">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                      <div class="input-group-append">
                        <span class="input-group-text" id="">APP</span>
                      </div>
                    </div>
                </div>
                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-danger">Save change</button>
                </div>
              </form>
            </div>
        </div>
        <div class="col-md-8">
        <div class="card">
          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>S.O</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($downloads as $download)

                    <tr>
                      <td>{{$download->name}}</td>
                      <td>{{$download->so}}</td>
                      <td> 
                        <a href="{{route('settings.delete', $download->id)}}" class="delete btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="Delete">
                          <i class="fas fa-fw fa-trash"></i>
                        </a>
                      </td>
                    </tr>

                  @endforeach

                </tbody>
              </table>
          </div>
          <div class="card-footer"></div>
      </div>
    </div>
    </div>
</div>
@stop

@section('css')
    <style>
      input[type='file']:hover{
        cursor: pointer;
      }
    </style>
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')

@stop

