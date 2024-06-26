@extends('vendor.adminlte.master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@if($layoutHelper->isLayoutTopnavEnabled())
    @php( $def_container_class = 'container' )
@else
    @php( $def_container_class = 'container-fluid' )
@endif

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    <div class="wrapper">

        {{-- Top Navbar --}}
        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('vendor.adminlte.partials.navbar.navbar-layout-topnav')
        @else
            @include('vendor.adminlte.partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if(!$layoutHelper->isLayoutTopnavEnabled())
            @include('vendor.adminlte.partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        <div class="content-wrapper {{ config('adminlte.classes_content_wrapper') ?? '' }}">

            {{-- Content Header --}}
            @hasSection('content_header')
                <div class="content-header">
                    <div class="{{ config('adminlte.classes_content_header') ?: $def_container_class }}">
                        @yield('content_header')
                    </div>
                </div>
            @endif

            {{-- Main Content --}}
            <div class="content">
                <div class="{{ config('adminlte.classes_content') ?: $def_container_class }}">
                    @yield('content')
                </div>
            </div>

        </div>

        {{-- Footer --}}
        @hasSection('footer')
            @include('vendor.adminlte.partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if(config('adminlte.right_sidebar'))
            @include('vendor.adminlte.partials.sidebar.right-sidebar')
        @endif

    </div>
@stop

@section('adminlte_js')
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
    @if ($errors->any())
    <script>
          Swal.fire({
          position: 'top-end',
          icon: 'error',
          title: '{{$errors->first()}}',
          showConfirmButton: false,
          timer: 3500
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
            timer: 3500
          });
    </script>
    @endif 
    @stack('js')
    @yield('js')
@stop
