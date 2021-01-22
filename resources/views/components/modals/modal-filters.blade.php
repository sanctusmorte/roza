<div class="modal fade" id="modalFilters" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Фильтры</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>


            <form method="POST" action="/user/filters/set">

                @csrf

                <div class="modal-body">

                        {{--<div class="card mb-3">--}}
                            {{--<article class="card-group-item">--}}
                                {{--<header class="card-header p-2">--}}
                                    {{--<h6 class="title m-0">Группы статусов</h6>--}}
                                {{--</header>--}}
                                {{--<select autocomplete="off" class="custom-select pl-1" name="inputFilterByGroupStatuses">--}}
                                    {{--<option value="all" @if (isset($userFilters['extendedStatus']) === false) selected @endif>Все группы статусов</option>--}}
                                    {{--@foreach($baseGroupStatusFilters as $item)--}}
                                        {{--<option @if (isset($userFilters['extendedStatus']) === true and $userFilters['extendedStatus'][0] === $item['code']) selected="true" @endif value="{{ $item['code'] }}">{{ $item['name'] }}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</article>--}}
                        {{--</div>--}}

                    <div class="card mb-3">
                        <article class="card-group-item">
                            <header class="card-header p-2">
                                <h6 class="title m-0">Статусы</h6>
                            </header>
                            <div class="form-check pl-3 m-3">
                                @foreach ($statuses as $status)
                                    <div>
                                        <input @if (isset($userFilters['extendedStatus']) and array_search($status['code'], $userFilters['extendedStatus']) !== false) checked="true" @endif onchange="selectStatus(this);" autocomplete="off" class="form-check-input" name="checkStatus{{ $status['code'] }}" type="checkbox" value="{{ $status['code'] }}" id="checkStatus{{ $status['code'] }}">
                                        <label style="cursor: pointer;" class="form-check-label" for="checkStatus{{ $status['code'] }}">
                                            {{ $status['name'] }}
                                        </label>
                                    </div>
                                @endforeach
                                <input autocomplete="off" id="selectedStatuses" type="hidden" name="selectedStatuses" value="@if(isset($userFilters['extendedStatus'])) {{ json_encode($userFilters['extendedStatus']) }} @endif">
                            </div>
                        </article>

                        <article class="card-group-item">
                            <header class="card-header p-2">
                                <h6 class="title m-0">Дата доставки</h6>
                            </header>
                            <div class="form-check pl-3 py-2 pb-3">
                                <div>
                                    <label for="datepicker" class="col-form-label">Дата доставки с</label>
                                    <div>
                                        <input autocomplete="off" class="form-control datepicker" id="datepicker" name="deliveryDateFrom" value="@if (isset($userFilters['deliveryDateFrom'])){{ date('d-m-Y', strtotime($userFilters['deliveryDateFrom'])) }}@endif">
                                    </div>
                                </div>
                                <div>
                                    <label for="datepicker2" class="col-form-label">Дата доставки по</label>
                                    <div>
                                        <input autocomplete="off" class="form-control datepicker" id="datepicker2" name="deliveryDateTo"  value="@if (isset($userFilters['deliveryDateTo'])){{ date('d-m-Y', strtotime($userFilters['deliveryDateTo'])) }}@endif">
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>



                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Применить</button>
                </div>

            </form>

        </div>
    </div>
</div>