<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
        <title>@yield('title_prefix', config('adminlte.title_prefix', ''))
            @yield('title', config('adminlte.title', 'AdminLTE 3'))
            @yield('title_postfix', config('adminlte.title_postfix', ''))</title>
        <!-- Font Awesome icons (free version)-->
        <script src="{{ asset('vendor/bootstrap/js/all.js') }}"></script>
        <!-- Google fonts-->
        <!-- <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" /> -->
        <!-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" /> -->
        <!-- Core theme CSS (includes Bootstrap)-->
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/styles.css') }}">
        <style>
            .signup-section {
                padding: 10rem 0;
                background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.5) 75%, #000 100%), url("{{ asset('img/bg-signup.jpg') }}");
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: scroll;
                background-size: cover;
                }
                .masthead {
                position: relative;
                width: 100%;
                height: auto;
                min-height: 35rem;
                padding: 15rem 0;
                background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.7) 75%, #000 100%), url("{{ asset('img/bg-masthead.jpg') }}");
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: scroll;
                background-size: cover;
                }
        </style>
    </head>
    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="#page-top">Private|WIRE</a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    Menu
                    <i class="fas fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive"  >
                    <ul class="navbar-nav ms-auto">
                        @if (Route::has('login'))
                            @auth
                                <li class="nav-item"><a class="nav-link" href="{{route('dashboard')}}">Home</a></li>
                                @if (Auth::user()->type)
                                <li class="nav-item"><a class="nav-link" href="{{route('admin')}}">Manage</a></li>
                                @else
                                <li class="nav-item"><a class="nav-link" href="{{route('client')}}">Manage</a></li>
                                @endif
                                <li class="nav-item"><a class="nav-link" href="{{route('download')}}">Download</a></li>
                                <li class="nav-item"><a class="nav-link" href="javascript;" data-bs-toggle="modal" data-bs-target="#passModal">Change Password</a></li>
                                <li class="nav-item"><a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" >Log out</a></li>
                                <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{Auth::user()->email}}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                                  <li><a class="dropdown-item" href="{{route('dashboard')}}">Home</a></li>
                                    @if (Auth::user()->type)
                                    <li><a class="dropdown-item" href="{{route('admin')}}">Manage</a></li>
                                    @else
                                    <li><a class="dropdown-item" href="{{route('client')}}">Manage</a></li>
                                    @endif
                                    <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#passModal" >Change Password</button></li>
                                    {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#passModal">New Profile</button> --}}
                                  <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" >Log out</a></li>
                                </ul>
                              </li>
                              <form id="logout-form" action="{{ route(config('adminlte.logout_url', 'logout')) }}" method="POST" style="display: none;">
                                @if(config('adminlte.logout_method'))
                                    {{ method_field(config('adminlte.logout_method')) }}
                                @endif
                                @csrf
                              </form>
                            @else
                                <li class="nav-item"><a class="nav-link" href="#about"><b>About</b></a></li>
                                <li class="nav-item"><a class="nav-link" href="#projects"><b>Projects</b></a></li>
                                <li class="nav-item"><a class="nav-link" href="#signup"><b>Contact</b></a></li>
                                <li class="nav-item"><a class="nav-link" href="{{route('login')}}"><b>Login</b></a></li>
                            @endauth
                        @endif
                        
                        
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Masthead-->
        <header class="masthead">
            <div class="container px-4 px-lg-5 d-flex h-100 align-items-center justify-content-center">
                <div class="d-flex justify-content-center">
                    <div class="text-center">
                        <h1 class="mx-auto my-0 text-uppercase" style="font-size: 1.8rem;">Private|WIRE</h1>
                        <h2 class="text-white-50 mx-auto mt-2 mb-5">WireGuard® is an extremely simple yet fast and modern VPN that utilizes state-of-the-art cryptography</h2>
                        <a class="btn btn-primary" href="@auth {{request()->routeIs('dashboard') ? route('client').'#about' : '#about'}} @else {{route('register')}} @endauth" style="color: #fff; font-size: 1.5rem; padding: 10px;">BUY<p style="color: #fff; font-size: 0.6rem">pay in usdt(trc-20)</p></a>
                    </div>
                </div>
            </div>
        </header>

        @yield('content')

        <!-- Signup-->
        <section class="signup-section" id="signup">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5">
                    <div class="col-md-10 col-lg-8 mx-auto text-center">
                        <i class="far fa-paper-plane fa-2x mb-2 text-white"></i>
                        <h2 class="text-white mb-5">Contact Us!</h2>
                        <form class="form-signup" action="{{route('contact')}}" method="POST">
                            @csrf
                            <!-- Email address input-->
                            <div class="row">
                                <div class="col-md-6" style="margin-top: 15px;"><input class="form-control" value="{{old('name')}}" name="name" type="text" placeholder="Enter name..."  required/></div>
                                @if (Route::has('login'))
                                @auth
                                <div class="col-md-6" style="margin-top: 15px;"><input class="form-control" name="email" type="email" value="{{Auth::user()->email}}" required/></div>
                                @else
                                <div class="col-md-6" style="margin-top: 15px;"><input class="form-control" value="{{old('email')}}" name="email" type="email" placeholder="Enter email address..." required/></div>
                                @endauth
                                @endif
                            </div> 
                            <div class="row" style="margin-top: 15px;">  
                                <div class="col-md-12">
                                    <div class="form-floating">
                                    <textarea class="form-control" id="commencts" value="{{old('commencts')}}" name="commentc" style="height: 100px" required></textarea>
                                    <label for="commencts">COMMENTS</label>
                                    </div>
                                </div> 
                        <div class="row" style="margin-top: 15px;"> 
                            <div class="col-md-12"><button class="btn btn-primary" id="" type="submit">Send!</button></div>
                        </div>
                        </form>
                    </div>
                </div>

                </div>
            </div>
        </section>
        <!-- Contact-->
        <section class="contact-section bg-black">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5">
                    <div class="social d-flex justify-content-center">
                        <a class="mx-2" href="#!"><i class="fab fa-whatsapp"></i></a>
                        <a class="mx-2" href="#!"><i class="fab fa-facebook-f"></i></a>
                        <a class="mx-2" href="#!"><i class="fab fa-github"></i></a>
                    </div>
            </div>
        </section>
          <!-- Modal -->
  <div class="modal fade" id="passModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="passModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="passModalLabel">Change Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{route('users.update')}}" method="POST">
          @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label for="passo" class="form-label">Password Old</label>
                <input type="password" class="form-control" id="passo" name="passo" required />
            </div>
            <div class="mb-3">
                <label for="pass" class="form-label">Password</label>
                <input type="password" class="form-control" id="pass" name="pass" required />
            </div>
            <div class="mb-3">
                <label for="passv" class="form-label">Password Verification</label>
                <input type="password" class="form-control" id="passv" name="passv" required />
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
        <!-- Footer-->
        <footer class="footer bg-black small text-center text-white-50"><div class="container px-4 px-lg-5">By GoDjango 2021</div></footer>
        <!-- Bootstrap core JS-->
        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.me.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/scripts.js') }}"></script>
        <script src="{{ asset('vendor/sweetalert.min.js') }}"></script>
        <script src="{{ asset('vendor/clipboard.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('[data-bs-toggle="tooltip"]').tooltip()
            });
        </script>
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
                showConfirmButton: true,
              });
        </script>
        @endif 
        <!-- Core theme JS-->
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <!-- * *                               SB Forms JS                               * *-->
        <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
    </body>
</html>