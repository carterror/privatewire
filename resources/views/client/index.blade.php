@extends('layout')

@section('title', 'Welcome')

@section('content')
        <!-- About-->

        <section class="about-section text-center" id="about">
            <div style="margin-top: -180px;"><a href="#about" style="padding: 10px; color: #fff; font-size: 50px;"><i class="fas fa-chevron-down"></i></a></div>
            <div class="container">
              <div class="row justify-content-between">
                <div class="col-md-4">
                  <h2 style="color: #fff;">{{Auth::user()->email}} <span class="badge bg-secondary">${{Auth::user()->ballance}}</span></h2>
                </div>
                <div class="col-md-4">
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="@if(is_null(Auth::user()->wallet)) #walletModal @else #fundsModal @endif">Add Founds</button>
                </div>
              </div>
            </div>
          </section>
        <section class="proyect-section text-center" id="user" style="padding: 10px;">
            <div class="container">
                <div class="card">
                    <div class="card-header">
                        <div class="row justify-content-between">
                            <div class="col-md-4">
                                <h1>Profiles</h1>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#profileModal">New Profile</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                            @foreach ($profiles as $profile)
                            <div class="accordion accordion-flush" id="accordionFlush{{$profile->id}}">
                                <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingOne">
                                    <button class="accordion-button collapsed @if ($profile->status) bg-success @else bg-danger @endif text-white" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne{{$profile->id}}" aria-expanded="false" aria-controls="flush-collapseOne{{$profile->id}}">
                                    <h2><i class="fas fa-server"></i> {{$profile->name}}</h2>
                                    </button>
                                </h2>
                                <div id="flush-collapseOne{{$profile->id}}" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlush{{$profile->id}}">
                                    <div class="accordion-body">
                                        <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
                                            <div class="col">
                                              <div class="card mb-4 rounded-3 shadow-sm">
                                                <div class="card-header py-3">
                                                  <h4 class="my-0 fw-normal">Info</h4>
                                                </div>
                                                <div class="card-body">
                                                  <h1 class="card-title pricing-card-title">${{Storage::disk('config')->get('price')}}<small class="text-muted fw-light">/mo</small></h1>
                                                  <ul class="list-unstyled mt-3 mb-4">
                                                    <li><h3>DNS: </h3></li>
                                                    <li><h5>{{$profile->dns}}</h5></li>
                                                    <li><h3>Expire</h3></li>
                                                    <li><h5>{{($profile->billing->format('Y') > 2010)? $profile->billing->format('Y-m-d') : '-'}}</h5></li>
                                                    <li><p>In {{($profile->billing->format('Y') > 2010)? $profile->billing->diffForHumans() : '-'}}</p></li>
                                                    <li><h3>Location</h3></li>
                                                    <li><h5>{{$profile->server->loc}}</h5></li>
                                                  </ul>
                                                  <h1>
                                                    @if (!$profile->status)
                                                      <span class="badge bg-danger w-100">Disabled</span>
                                                    @else
                                                      <span class="badge bg-primary w-100">Activated</span>
                                                    @endif
                                                    
                                                  </h1>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="col">
                                              <div class="card mb-4 rounded-3 shadow-sm">
                                                <div class="card-header py-3">
                                                  <h4 class="my-0 fw-normal">Configurations</h4>
                                                </div>
                                                <div class="card-body">
                                                  <img src="{{route('confimage', $profile->id)}}" class="card-img-top" alt="...">
                                                  <a href="{{route('confload', $profile->id)}}" class="w-100 btn btn-lg btn-primary">Download Config</a>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="col">
                                              <div class="card mb-4 rounded-3 shadow-sm @if(Auth::user()->ballance < Storage::disk('config')->get('price')) border-danger @else border-primary  @endif">
                                                <div class="card-header py-3 text-white @if(Auth::user()->ballance < Storage::disk('config')->get('price')) bg-danger border-danger @else bg-primary border-primary  @endif">
                                                  <h4 class="my-0 fw-normal">Actions</h4>
                                                </div>
                                                <div class="card-body">
                                                  <form action="{{route('active', $profile->id)}}" method="POST">
                                                    @csrf
                                                  <ul class="list-unstyled mt-3 mb-4">
                                                    <li>Unmetered traffic</li>
                                                    <li>Unlimited speed</li>
                                                    <li>No traces</li>
                                                    <li>Absolutly encrypted</li>
                                                  </ul>
                                                  <h1 class="card-title pricing-card-title">${{Storage::disk('config')->get('price')}}<small class="text-muted fw-light">/Month</small></h1>
                                                  <label for="form-label">Choose how many month activate profile</label>
                                                  @php
                                                      $mounths = Auth::user()->ballance/Storage::disk('config')->get('price');
                                                  @endphp
                                                  <select class="form-select form-select-lg mb-3 @if ($profile->status) disabled @endif" @if (Auth::user()->ballance < Storage::disk('config')->get('price')) disabled @endif name="mounts">
                                                    <option value="1" selected>Choose how many month</option>
                                                    @for ($i = 1; $i <= $mounths; $i++)
                                                      <option value="{{$i}}">{{$i}} @if($i>1) Months @else Month @endif</option>
                                                    @endfor
                                                  </select>
                                                  
                                                  <button type="submit" class="w-100 btn btn-lg btn-primary @if (Auth::user()->ballance < Storage::disk('config')->get('price')) disabled @endif" @if (Auth::user()->ballance < Storage::disk('config')->get('price')) disabled @endif>Activate</button>
                                                  <a href="{{route('profile.delete', $profile->id)}}" class="w-100 btn btn-lg btn-danger delete" style="margin-top: 10px;">Delete</a>
                                                  </form>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                    </div>
                                </div>
                                </div>
                              </div>
                            @endforeach
                    </div>
                  </div>
                
            </div>
            </section>
  <!-- Modal -->
  <div class="modal fade" id="profileModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="profileModalLabel">New Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{route('profile')}}" method="POST">
          @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label for="name" class="form-label">Name Profile</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Juan" required />
            </div>
            <div class="mb-3">
              <label for="loc" class="form-label">Available Locations</label>
              <select class="form-select" aria-label="La Habana" id="loc" name="loc" required>
                @foreach ($locations as $location)
                  <option value="{{$location->loc}}">{{$location->loc}}</option>
                @endforeach
              </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
        </form>
      </div>
    </div>
  </div>
  <!-- addfunds -->
  <div class="modal fade" id="fundsModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="fundsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="fundsModalLabel">Add Founds "USDT (TRC-20)"</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        @php
            $hash = Storage::disk('config')->get('hash');
        @endphp
        <form action="{{route('addfunds', Crypt::encrypt($hash))}}" method="POST">
          @csrf
        <div class="modal-body">
              <img src="{{route('confimage', 'qr')}}" class="card-img-top" alt="..." style="padding: 10px;">
              <div class="input-group mb-3">
                <span class="input-group-text bg-secondary" style="font-size: 30px; padding: 5px 15px; color: #fff;"><i class="fas fa-qrcode"></i></span>
                <input type="text" value="{{$hash}}" class="form-control" id="copied" aria-label="" style="border: none; font-size: 25px;" disabled>
                <span class="input-group-text btn btn-secondary btn-sm" onclick="setClipboardCard()" style="font-size: 30px; padding: 5px 15px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy"><i class="fas fa-clipboard"></i></span>
              </div>
            <div class="mb-3">
                <label for="tx" class="form-label">Transaction Hash</label>
                <input type="text" class="form-control" id="tx" name="tx" placeholder="5qwe68r4qwx48q648rxq8..." required />
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        </form>
      </div>
    </div>
  </div>
@stop