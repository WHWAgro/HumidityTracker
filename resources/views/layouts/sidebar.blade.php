<!-- SIDEBAR -->
<section id="sidebar">
    <a href="javascript:void(0);"class="brand">
        <!-- <i class='bx bxs-smile'></i> -->
        <img src="{{ asset('images/logo.png') }}" alt="">
        <span class="text"><img src="{{ asset('images/logo-txt.png') }}" alt=""></span>
    </a>
    <ul class="side-menu">
        <h1>Módulos</h1>
        <li class="{{ Request::segment(1)== 'forecast' ? 'active' : '' }}">
            <a href="{{ url('forecast') }}">
                <img src="{{ asset('images/menu-icons/forecast-data.svg') }}"alt="">
                <span class="text">Proyección Humedad</span>
            </a>
        </li>
        <li class="{{ Request::segment(1)== 'forecast_data' ? 'active' : '' }}">
            <a href="{{ url('forecast_data') }}">
                <img src="{{ asset('images/menu-icons/check-data.svg') }}"alt="">
                <span class="text">Revisar Mediciones</span>
            </a>
        </li>
        <li>
            <a href="{{ url('logout') }}" onclick="event.preventDefault(); document.getElementById('form-logout').submit();">
                <img src="{{ asset('images/menu-icons/logout.svg') }}"alt="">
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</section>
<!-- SIDEBAR -->
<form id="form-logout" action="{{ url('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
</form>