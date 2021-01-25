@extends('layouts.base')

@section('title', 'Курьеры')

@section('css')

    <style>
        .loader {
            border: 4px solid #acacac;
            border-top: 4px solid #000;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            animation: spin 2s linear infinite;
            display: none;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .courierSelectColor {
            width: 100px;
            height: 30px;
            border: 0 none;
            padding: 0;
            cursor: pointer;
        }
        
    </style>
@endsection

@section('content')

    <div class="container">
        @csrf
        <div class="col-12">
            <p class="h3 my-4">Список курьеров</p>

            <div id="colorCodeAlert" class="d-none alert alert-danger" role="alert"></div>
            <div id="activeStatusAlert" class="d-none alert alert-danger" role="alert"></div>

            <table id="tableCouriers" class="table">
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th scope="col">#</th>
                        <th scope="col">Id в retailCRM</th>
                        <th scope="col">Имя</th>
                        <th scope="col">Фамилия</th>
                        <th scope="col">Активен</th>
                        <th scope="col">Цвет курьера</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($couriers as $courier)
                        <tr class="text-center">
                            <th scope="row">{{ $courier['id'] }}</th>
                            <th scope="row">{{ $courier['exId'] }}</th>
                            <td>{{ $courier['firstName'] }}</td>
                            <td>{{ $courier['lastName'] }}</td>
                            <td>
                                <select data-courierId="{{ $courier['id'] }}" onchange="changeActiveStatus(this);" autocomplete="off" class="custom-select pl-1 text-center">
                                    <option value="true" @if ($courier['active'] === 1) selected="selected" @endif>Да</option>
                                    <option value="false" @if ($courier['active'] === 0) selected="selected" @endif>Нет</option>
                                </select>
                                <div id="statusLoader" class="loader"></div>
                            </td>
                            <td>
                                <input autocomplete="off" type="color" class="courierSelectColor" data-courierId="{{ $courier['id'] }}" id="colorCourier{{ $courier['id'] }}" name="colorCourier{{ $courier['id'] }}" onchange="changeColorCode(this);" value="{{ $courier['colorCode'] }}">
                                <div id="colorLoader" class="loader"></div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center py-3">
                {{ $couriers->links() }}
            </div>

        </div>
    </div>
@endsection

@section('js')

    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tableCouriers').DataTable({
                paging: false,
                searching: false,
                info: false,
            });
        } );
    </script>

    <script>
        function isHexColor(hex) {
            return typeof hex === 'string'
                && hex.length === 6
                && !isNaN(Number('0x' + hex))
        }

        function changeActiveStatus(that) {
            const statusLoader = $(that).siblings('#statusLoader');
            const courierId = parseInt($(that).attr('data-courierId'));
            const status = $(that).val();
            const activeStatusAlert = $('#activeStatusAlert');
            statusLoader.show();
            $(that).hide();

            $.ajax({
                url: '/couriers/update-status',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                data: {
                    'courierId' : courierId,
                    'courierActiveStatus' : status,
                },
                success: function(response){
                    setTimeout(function() {
                        statusLoader.hide();
                        $(that).show();
                        activeStatusAlert.removeClass('d-none');
                        let activeStatusAlertMsg = response.msg;
                        activeStatusAlert.text(activeStatusAlertMsg);

                        if (response.error === true) {
                            activeStatusAlert.removeClass('alert-success');
                            activeStatusAlert.addClass('alert-danger');
                        } else {
                            activeStatusAlert.removeClass('alert-danger');
                            activeStatusAlert.addClass('alert-success');
                        }

                        setTimeout(function() {
                            activeStatusAlert.addClass('d-none');
                        }, 2000);

                    }, 1000);
                },
                error: function(response){
                },
            });
        }


        function changeColorCode(that) {
            const colorLoader = $(that).siblings('#colorLoader');
            const colorCodeAlert = $('#colorCodeAlert');
            const courierId = parseInt($(that).attr('data-courierId'));
            const colorCode = $(that).val();

            colorLoader.show();
            $(that).hide();

            if (isHexColor(colorCode.substr(1, 7)) === false) {
                let colorCodeAlertMsg = 'Ошибка при выборе цвета для курьера #'+courierId+'! Выберите корректный Hex (#000000) код для цвета.';
                colorCodeAlert.text(colorCodeAlertMsg);
                colorCodeAlert.removeClass('d-none');
                setTimeout(function() {
                    colorCodeAlert.addClass('d-none');
                    colorLoader.hide();
                    $(that).show();
                }, 3000);
            } else {

                $.ajax({
                    url: '/couriers/update',
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    data: {
                        'courierId' : courierId,
                        'colorCode' : colorCode,
                    },
                    success: function(response){
                        setTimeout(function() {

                            colorLoader.hide();
                            $(that).show();
                            colorCodeAlert.removeClass('d-none');
                            let colorCodeAlertMsg = response.msg;
                            colorCodeAlert.text(colorCodeAlertMsg);

                            if (response.error === true) {
                                colorCodeAlert.removeClass('alert-success');
                                colorCodeAlert.addClass('alert-danger');
                            } else {
                                colorCodeAlert.removeClass('alert-danger');
                                colorCodeAlert.addClass('alert-success');
                            }

                            setTimeout(function() {
                                colorCodeAlert.addClass('d-none');
                            }, 2000);

                        }, 1000);
                    },
                    error: function(response){
                    },
                });
            }
        }
    </script>
@stop
