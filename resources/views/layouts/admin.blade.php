<!DOCTYPE html>



<html lang="id">



<head>



<meta charset="UTF-8">



<meta name="viewport" content="width=device-width, initial-scale=1">



<title>@yield('title')</title>



<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">



<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">



<link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}?v=11">



@stack('styles')

<link rel="stylesheet" href="{{ asset('css/admin/responsive-fix.css') }}?v=13">



</head>



<body>



<div class="wrapper">



@include('partials.sidebar')

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>



<div class="main-content" id="mainContent">



@include('partials.navbar')



<div class="content">



@yield('content')



@include('partials.admin-footer')



</div>



</div>



</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



@stack('scripts')



<script>

const toggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
const main = document.getElementById('mainContent');
const backdrop = document.getElementById('sidebarBackdrop');
const mobileQuery = window.matchMedia('(max-width: 1180px)');

function setSidebar(open) {
    if (mobileQuery.matches) {
        sidebar.classList.toggle('hide', !open);
        main.classList.remove('full');
        document.body.classList.toggle('sidebar-open', open);
    } else {
        sidebar.classList.toggle('hide', !open);
        main.classList.toggle('full', !open);
        document.body.classList.remove('sidebar-open');
    }
}

function syncSidebarForViewport() {
    if (mobileQuery.matches) {
        setSidebar(false);
    } else {
        setSidebar(true);
    }
}

toggle.addEventListener('click', function (event) {
    event.stopPropagation();
    setSidebar(sidebar.classList.contains('hide'));
});

backdrop.addEventListener('click', function () {
    setSidebar(false);
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && mobileQuery.matches) {
        setSidebar(false);
    }
});

window.addEventListener('resize', syncSidebarForViewport);
window.addEventListener('pageshow', syncSidebarForViewport);
syncSidebarForViewport();

</script>



</body>



</html>




