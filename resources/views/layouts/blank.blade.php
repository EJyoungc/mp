<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 
    'resources/js/app.js', 
    'public/dist/css/adminlte.min.css',
    'public/plugins/jquery/jquery.min.js' ,
    'public/plugins/bootstrap/js/bootstrap.bundle.min.js', 
    'public/dist/js/adminlte.min.js',
    'public/plugins/fontawesome-free/css/all.min.css'
    ]) --}}

    <!-- Styles -->
    @livewireStyles
</head>

<body class="hold-transition sidebar-mini">
    
       
           
        <div class="container-fluid">
            
            {{ $slot }}

        </div>
           
        

        
        

        
        @livewireScripts
        <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
        <x-livewire-alert::scripts />
        <script>
            // document.addEventListener('livewire:init', () => {
            Livewire.on('modal-open', (data) => {
                // Handle the event here
                var modalbackdrop = document.createElement('div');
                modalbackdrop.classList.add("modal-backdrop", "fade", "show");
                document.body.appendChild(modalbackdrop);
    
            });
            Livewire.on('modal-cancel', (data) => {
                // Handle the event here
                var modalbackdrop = document.querySelector('.modal-backdrop');
                if (modalbackdrop) {
                    modalbackdrop.parentNode.removeChild(modalbackdrop);
                }
    
            });
            // });
        </script>
        

        <script src="{{ asset('plugins/jquery/jquery.min.js') }}" ></script>
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}" ></script>
        <script src="{{ asset('dist/js/adminlte.min.js') }}" ></script>
        
</body>

</html>
