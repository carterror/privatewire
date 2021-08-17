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
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fundsModal">Add Founds</button>
                </div>
              </div>
            </div>
          </section>
        <section class="proyect-section text-center" id="user" style="padding: 10px;">
            <div class="container">
                <div class="card">
                    <div class="card-header">
                      <h1>Downloads</h1>
                    </div>
                    <div class="card-body">
                            <div class="accordion accordion-flush" id="accordionFlushw">
                                <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingOnew">
                                    <button class="accordion-button collapsed bg-info text-white" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOnew" aria-expanded="@if (Str::contains($so, 'Windows')) true  @else false @endif" aria-controls="flush-collapseOnew">
                                    <h2><i class="fab fa-windows"></i></span> Windows</h2>
                                    </button>
                                </h2>
                                <div id="flush-collapseOnew" class="accordion-collapse collapse @if (Str::contains($so, 'Windows')) show  @else @endif" aria-labelledby="flush-headingOnew" data-bs-parent="#accordionFlushw">
                                    <div class="accordion-body">
                                        <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
                                          @foreach ($windows as $w)
                                            <div class="col">
                                              <div class="card mb-4 rounded-3 shadow-sm">
                                                <div class="card-header py-3">
                                                  <h4 class="my-0 fw-normal">{{$w->name}}</h4>
                                                </div>
                                                <div class="card-body row">
                                                  @if (!is_null($w->code))
                                                    <input class="col s12" id="copy{{$w->id}}" style="margin: 5px; padding: 15px; border-radius: 5px; text-align: justify; color: #fff; border: 2px solid rgb(70, 70, 70); background-color: rgb(90, 90, 90);" value="{{$w->code}}" />
                                                    <button class="w-100 btn btn-lg btn-info" onclick="setClipboardd({{$w->id}})">Copy Now</button>
                                                  @else
                                                    <a href="{{route('confload', 'wire'.$w->path)}}" class="w-100 btn btn-lg btn-info">Download Now</a>
                                                  @endif
                                                </div>
                                              </div>
                                            </div>
                                            @endforeach
                                          </div>
                                    </div>
                                </div>
                                </div>
                              </div>

                              <div class="accordion accordion-flush" id="accordionFlushl">
                                <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingOnel">
                                    <button class="accordion-button collapsed bg-info text-white" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOnel" aria-expanded="@if (Str::contains($so, 'Linux') == 0) true  @else false @endif" aria-controls="flush-collapseOnel">
                                    <h2><i class="fab fa-linux"></i></span> Linux</h2>
                                    </button>
                                </h2>
                                <div id="flush-collapseOnel" class="accordion-collapse collapse @if (Str::contains($so, 'Linux')) show  @else @endif" aria-labelledby="flush-headingOnel" data-bs-parent="#accordionFlushl">
                                    <div class="accordion-body">
                                        <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
                                          @foreach ($linux as $w)
                                            <div class="col">
                                              <div class="card mb-4 rounded-3 shadow-sm">
                                                <div class="card-header py-3">
                                                  <h4 class="my-0 fw-normal">{{$w->name}}</h4>
                                                </div>
                                                <div class="card-body row">
                                                  @if (!is_null($w->code))
                                                    <input class="col s12" id="copy{{$w->id}}" style="margin: 5px; padding: 15px; border-radius: 5px; text-align: justify; color: #fff; border: 2px solid rgb(70, 70, 70); background-color: rgb(90, 90, 90);" value="{{$w->code}}" />
                                                    <button class="w-100 btn btn-lg btn-info" onclick="setClipboardd({{$w->id}})">Copy Now</button>
                                                  @else
                                                    <a href="{{route('confload', 'wire'.$w->path)}}" class="w-100 btn btn-lg btn-info">Download Now</a>
                                                  @endif
                                                </div>
                                              </div>
                                            </div>
                                            @endforeach
                                          </div>
                                    </div>
                                </div>
                                </div>
                              </div>

                              <div class="accordion accordion-flush" id="accordionFlushm">
                                <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingOnem">
                                    <button class="accordion-button collapsed bg-info text-white" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOnem" aria-expanded="@if (Str::contains($so, 'Mac') || Str::contains($so, 'iPhone') || Str::contains($so, 'iPad')) true  @else false @endif" aria-controls="flush-collapseOnem">
                                    <h2><i class="fab fa-apple"></i></span> Mac</h2>
                                    </button>
                                </h2>
                                <div id="flush-collapseOnem" class="accordion-collapse collapse @if (Str::contains($so, 'Mac') || Str::contains($so, 'iPhone') || Str::contains($so, 'iPad')) show  @else @endif" aria-labelledby="flush-headingOnem" data-bs-parent="#accordionFlushm">
                                    <div class="accordion-body">
                                        <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
                                          @foreach ($mac as $w)
                                            <div class="col">
                                              <div class="card mb-4 rounded-3 shadow-sm">
                                                <div class="card-header py-3">
                                                  <h4 class="my-0 fw-normal">{{$w->name}}</h4>
                                                </div>
                                                <div class="card-body row">
                                                  @if (!is_null($w->code))
                                                    <input class="col s12" id="copy{{$w->id}}" style="margin: 5px; padding: 15px; border-radius: 5px; text-align: justify; color: #fff; border: 2px solid rgb(70, 70, 70); background-color: rgb(90, 90, 90);" value="{{$w->code}}" />
                                                    <button class="w-100 btn btn-lg btn-info" onclick="setClipboardd({{$w->id}})">Copy Now</button>
                                                  @else
                                                    <a href="{{route('confload', 'wire'.$w->path)}}" class="w-100 btn btn-lg btn-info">Download Now</a>
                                                  @endif
                                                </div>
                                              </div>
                                            </div>
                                            @endforeach
                                          </div>
                                    </div>
                                </div>
                                </div>
                              </div>

                              <div class="accordion accordion-flush" id="accordionFlusha">
                                <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingOnea">
                                    <button class="accordion-button collapsed bg-info text-white" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOnea" aria-expanded="@if (Str::contains($so, 'Android')) true  @else false @endif" aria-controls="flush-collapseOnea">
                                    <h2><i class="fab fa-android"></i></span> Android</h2>
                                    </button>
                                </h2>
                                <div id="flush-collapseOnea" class="accordion-collapse collapse @if (Str::contains($so, 'Android')) show  @else @endif" aria-labelledby="flush-headingOnea" data-bs-parent="#accordionFlusha">
                                    <div class="accordion-body">
                                        <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
                                          @foreach ($android as $w)
                                            <div class="col">
                                              <div class="card mb-4 rounded-3 shadow-sm">
                                                <div class="card-header py-3">
                                                  <h4 class="my-0 fw-normal">{{$w->name}}</h4>
                                                </div>
                                                <div class="card-body row">
                                                  @if (!is_null($w->code))
                                                    <input class="col s12" style="margin: 5px; padding: 15px; border-radius: 5px; text-align: justify; color: #fff; border: 2px solid rgb(70, 70, 70); background-color: rgb(90, 90, 90);" value="{{$w->code}}" />
                                                    <button href="{{asset('config/'.$w->path)}}" class="w-100 btn btn-lg btn-info">Download Now</button>
                                                  @else
                                                    <a href="{{route('confload', 'wire'.$w->path)}}" class="w-100 btn btn-lg btn-info">Download Now</a>
                                                  @endif
                                                </div>
                                              </div>
                                            </div>
                                            @endforeach
                                          </div>
                                    </div>
                                </div>
                                </div>
                              </div>
                            
                    </div>
                  </div>
                
            </div>
            </section>
@stop