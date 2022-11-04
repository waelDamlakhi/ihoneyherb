<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            .parent-el {
                padding: 4rem;
                background-color: #f8f8f8 !important;
            }
            .main-el {
                width: 36rem;
                background-color: #fff !important; 
                padding: 2rem; 
                border-radius: .4rem; 
                margin: 0 auto;
            }
            .link-btn-el {
                display: block;
                width: fit-content;
                margin: 2rem auto;
                background-color: #16a34a !important;
                color: #fff !important;
                font-weight: bold;
                text-transform: uppercase;
                padding: 1rem 2rem;
                border-radius: .4rem;
                transition: opacity .3s ease-in-out;
            }
            .link-btn-el:hover {opacity: .8;}
            .link-el {color: #ca8a04 !important}
            @media (max-width: 767px) {
                .parent-el {padding: 1rem;}
                .main-el {width: auto;}
            }
            a {text-decoration: none;}
        </style>
    </head>
<body>
    <div class="parent-el">
        <div style="width:200px; margin: 0 auto 2rem">
            <img src={{ env('APP_WEBSITE') . '/logo/logo-dark.png' }} alt="ihoneyherb.ae" style="width: 100%; height: auto">
        </div>
        <div class="main-el">
            @yield('content')
        </div>
    </div>
</body>
</html>