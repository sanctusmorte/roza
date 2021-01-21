<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <div class="row">
            <a class="navbar-brand" href="/">
                <img alt="logo" style="width: 30px;" src="https://im-business.com/wp-content/themes/im-business/assets/images/logo.png">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item {{ Request::path() === '/' ? 'active' : '' }}">
                        <a class="nav-link" href="/">Главная</a>
                    </li>
                    <li class="nav-item {{ Request::is('map2') ? 'active' : '' }}">
                        <a class="nav-link" href="/map2">Логистическая карта</a>
                    </li>
                    <li class="nav-item {{ Request::is('couriers') ? 'active' : '' }}">
                        <a class="nav-link" href="/couriers">Курьеры</a>
                    </li>
                </ul>

            </div>
        </div>
    </div>
</nav>