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

                        <div class="card mb-3">
                            <article class="card-group-item">
                                <header class="card-header p-2">
                                    <h6 class="title m-0">Группы статусов</h6>
                                </header>
                                <select autocomplete="off" class="custom-select pl-1" name="inputFilterByGroupStatuses">
                                    <option value="all" @if (isset($userFilters['extendedStatus']) === false) selected @endif>Все группы статусов</option>
                                    @foreach($baseGroupStatusFilters as $item)
                                        <option @if (isset($userFilters['extendedStatus']) === true and $userFilters['extendedStatus'][0] === $item) selected="true" @endif value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
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