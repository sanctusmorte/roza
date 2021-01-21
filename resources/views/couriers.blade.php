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

            <table class="table">
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
                                @if ($courier['active'] === 1)
                                    <span class="text-success">Да</span>
                                @else
                                    Нет
                                @endif
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
    <script>
        function isHexColor(hex) {
            return typeof hex === 'string'
                && hex.length === 6
                && !isNaN(Number('0x' + hex))
        }

        function changeColorCode(that) {
            const colorLoader = $(that).siblings('#colorLoader');
            const colorCodeAlert = $('#colorCodeAlert');
            const courierId = parseInt($(that).attr('data-courierId'));
            const colorCode = $(that).val();

            colorLoader.show();
            $(that).hide();

            console.log(isHexColor(colorCode.substr(1, 7)));

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