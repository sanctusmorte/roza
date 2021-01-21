<div class="container mb-4">
    <div class="row justify-content-between align-items-center">
        <div class="col-8 p-0">

            @if(count($userFilters) === 0)
                <span>Нет активных фильтров! Выводятся все заказы за всё время со всех магазинов, к которым есть доступ по указанному в настройках API ключу.</span>
            @else
                <span>Активных фильтров: {{ count($userFilters) }}</span>
            @endif

        </div>
        <div class="col-auto p-0">
            <div class="btn btn-primary" data-toggle="modal" data-target="#modalFilters">
                <span>Фильтры</span>
                <i class="fas fa-filter"></i>
            </div>
        </div>
    </div>
</div>