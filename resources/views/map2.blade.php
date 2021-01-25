@extends('layouts.base')

@section('title', 'Логистическая карта')

@section('head-js')


    <style type="text/css">
        html, body, #map {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
        }

        .customControl {
            position: relative;
            display: none;
            min-width: 400px;
            min-height: 300px;
            max-height: 350px;
            background-color: #fff;
            padding: 5px;
            border-radius: 3px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .customControl .custom-select {
            max-width: 200px;
        }

        .closeButton {
            flex-basis: 15px;
            flex-grow: 0;
            flex-shrink: 0;
            padding: 3px;
            width: 15px;
            position: absolute;
            top: 10px;
            right: 10px;
            height: 15px;
            cursor: pointer;
            background: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNCIgaGVpZ2h0PSIxNCI+PHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBkPSJNMTQgLjdsLS43LS43TDcgNi4zLjcgMCAwIC43IDYuMyA3IDAgMTMuM2wuNy43TDcgNy43bDYuMyA2LjMuNy0uN0w3LjcgN3oiIGNsaXAtcnVsZT0iZXZlbm9kZCIvPjwvc3ZnPg==") 50% no-repeat;
            opacity: 0.4;

        }

        .content {
            padding: 5px;
            overflow-y: auto;
            width: 100%;
        }

        a, a:visited {
            color: #04b;
            text-decoration: none !important;
        }

        #balloonCourierInfo {
            max-width: 350px;
        }
    </style>

    <script src="https://api-maps.yandex.ru/2.1/?apikey=f5de97c5-850d-4e35-b3e2-e85aba272e55&lang=ru_RU" type="text/javascript"></script>
@endsection

@section('content')

    @include('components.filters')

    <div class="container">

        <input autocomplete="off" type="hidden" id="allUpdatedOrders" value="">

        <div class="row mb-3">
            <div>
            <p class="h5">Всего заказов: {{ count($data['orders']) }}</p>
            <p class="h5">Заказов выведено на карту: {{ $data['countOrdersWithGeoLocation'] }}</p>
            </div>
        </div>

        <div class="row mt-3">
            <div class="alert alert-success w-100" role="alert">
                Для того, чтобы заказ отобразился на карте необходимо заполнить поле "Город" в разделе ""Доставка
            </div>
        </div>

        <div class="row">
            <div id="map" style="width: 100%; height: 500px"></div>
        </div>


    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('components.modals.modal-filters', ['data' => $data, 'userFilters' => $userFilters, 'baseGroupStatusFilters' => $baseGroupStatusFilters])

@endsection

@section('js')


    <script>
        // Пример реализации боковой панели на основе наследования от collection.Item.
        // Боковая панель отображает информацию, которую мы ей передали.
        ymaps.modules.define('Panel', [
            'util.augment',
            'collection.Item'
        ], function (provide, augment, item) {
            // Создаем собственный класс.
            var Panel = function (options) {
                Panel.superclass.constructor.call(this, options);
            };

            // И наследуем его от collection.Item.
            augment(Panel, item, {
                onAddToMap: function (map) {
                    Panel.superclass.onAddToMap.call(this, map);
                    this.getParent().getChildElement(this).then(this._onGetChildElement, this);
                    // Добавим отступы на карту.
                    // Отступы могут учитываться при установке текущей видимой области карты,
                    // чтобы добиться наилучшего отображения данных на карте.
                    map.margin.addArea({
                        top: 0,
                        left: 0,
                        width: '250px',
                        height: '100%'
                    })
                },

                onRemoveFromMap: function (oldMap) {
                    if (this._$control) {
                        this._$control.remove();
                    }
                    Panel.superclass.onRemoveFromMap.call(this, oldMap);
                },

                _onGetChildElement: function (parentDomContainer) {
                    // Создаем HTML-элемент с текстом.
                    // По-умолчанию HTML-элемент скрыт.
                    this._$control = $('<div class="customControl"><div class="content"></div><div class="closeButton"></div></div>').appendTo(parentDomContainer);
                    this._$content = $('.content');
                    // При клике по крестику будем скрывать панель.
                    $('.closeButton').on('click', this._onClose);
                },
                _onClose: function () {
                    $('.customControl').css('display', 'none');
                },
                // Метод задания контента панели.
                setContent: function (text) {
                    // При задании контента будем показывать панель.
                    this._$control.css('display', 'flex');
                    this._$content.html(text);
                }
            });

            provide(Panel);
        });
    </script>

    <script>
        function setNewContentToBalloonIfSuccess(response, that, balloonSelectedCourier, balloonSite)
        {
            const allUpdatedOrders = $('#allUpdatedOrders');
            let balloonDeliveryMethodInfo = null;

            let newData = [];
            if (allUpdatedOrders.val() === "") {
                newData.push({
                    'orderId' : response.updatedOrder.id,
                    'orderData' : {
                        'balloonDeliveryMethodInfo' : balloonDeliveryMethodInfo,
                        'balloonSelectedCourier' : balloonSelectedCourier,
                        'balloonSite' : balloonSite,
                        'customerFirstName': response.updatedOrder.firstName,
                        'createdAt' : response.updatedOrder.createdAt,
                        'deliveryDate' : response.updatedOrder.deliveryDate,
                        'deliveryTime' : response.updatedOrder.deliveryTime,
                        'geoQuery' : response.updatedOrder.delivery.address.city,
                        'status' : response.updatedOrder.status,
                    },
                    'externalId' : response.updatedOrder.externalId,
                    'iconColor' : response.updatedOrder.iconColor,
                    'items' : response.updatedOrder.items,
                    'existCouriers' : response.existCouriers,
                });

                newData[0].orderData.delivery = {
                    'courierId' : balloonSelectedCourier
                };

                allUpdatedOrders.val(JSON.stringify(newData));
            } else {
                let existData = JSON.parse(allUpdatedOrders.val());
                let foundOrderId = false;
                let keyOfFoundOrder = 0;
                for(let i=0; i < existData.length; i++) {
                    if(existData[i].orderId === response.updatedOrder.id) {
                        foundOrderId = true;
                        keyOfFoundOrder = i;
                    }
                }
                if (foundOrderId === false) {
                    existData[keyOfFoundOrder] = {
                        'orderId' : response.updatedOrder.id,
                        'orderData' : {
                            'balloonDeliveryMethodInfo' : balloonDeliveryMethodInfo,
                            'balloonSelectedCourier' : balloonSelectedCourier,
                            'balloonSite' : balloonSite,
                            'customerFirstName': response.updatedOrder.firstName,
                            'createdAt' : response.updatedOrder.createdAt,
                            'deliveryDate' : response.updatedOrder.deliveryDate,
                            'deliveryTime' : response.updatedOrder.deliveryTime,
                            'geoQuery' : response.updatedOrder.delivery.address.city,
                            'status' : response.updatedOrder.status,
                        },
                        'externalId' : response.updatedOrder.externalId,
                        'iconColor' : response.updatedOrder.iconColor,
                        'items' : response.updatedOrder.items,
                        'existCouriers' : response.existCouriers,
                    };

                    existData[keyOfFoundOrder].orderData.delivery = {
                        'courierId' : balloonSelectedCourier
                    };

                    allUpdatedOrders.val(JSON.stringify(existData));
                } else {
                    existData[keyOfFoundOrder].orderData = {
                        'balloonDeliveryMethodInfo' : balloonDeliveryMethodInfo,
                        'balloonSelectedCourier' : balloonSelectedCourier,
                        'balloonSite' : balloonSite,
                        'customerFirstName': response.updatedOrder.firstName,
                        'createdAt' : response.updatedOrder.createdAt,
                        'deliveryDate' : response.updatedOrder.deliveryDate,
                        'deliveryTime' : response.updatedOrder.deliveryTime,
                        'geoQuery' : response.updatedOrder.delivery.address.city,
                        'status' : response.updatedOrder.status,
                    };

                    existData[keyOfFoundOrder].orderData.delivery = {
                        'courierId' : balloonSelectedCourier
                    };
                    allUpdatedOrders.val(JSON.stringify(existData));
                }
            }
        }
        function changeBalloonCourier(that) {
            const balloonisChanged = $(that).parents().find('#isChanged');
            const balloonAlert = $(that).parents().find('#balloonAlert');

            balloonAlert.addClass('d-none');
            balloonAlert.removeClass('d-flex');
            balloonisChanged.val('true');
        }
        function saveChanges(that, orderId) {

            const balloonisChanged = $(that).parents().find('#isChanged');
            const balloonAlert = $(that).parents().find('#balloonAlert');
            const balloonSite = $(that).parents().find('#balloonSite').val();

            let balloonAlertMsg = 'Вы не внесли изменений!';

            let balloonSelectedCourier = null;
            balloonSelectedCourier =  $(that).parents().find('#balloonCourierInfo').children('select').val();

            balloonAlert.addClass('d-none');

            if (balloonisChanged.val() === 'false') {
                balloonAlert.addClass('alert-danger');
                balloonAlert.removeClass('d-none');
                balloonAlert.text(balloonAlertMsg);
            } else {
                $.ajax({
                    url: '/orders/update',
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'orderId' : orderId,
                        'courier' : balloonSelectedCourier,
                        'site' : balloonSite,
                    },
                    success: function(response){
                        if (response.error === false) {
                            balloonAlertMsg = response.msg;
                            balloonAlert.addClass('alert-success');
                            balloonAlert.removeClass('alert-danger');
                            balloonAlert.text(balloonAlertMsg);
                            balloonAlert.addClass('d-flex');
                            balloonAlert.removeClass('d-none');

                            setNewContentToBalloonIfSuccess(response, that, balloonSelectedCourier, balloonSite);

                            setTimeout(function() {
                                $(that).parent().parent().parent().parent().parent().parent().siblings().click();
                            }, 2000);

                        } else {
                            if(typeof response.msg === "object") {
                                balloonAlertMsg = response.msg.errorMsg;
                            } else {
                                balloonAlertMsg = response.msg;
                            }

                            balloonAlert.removeClass('alert-success');
                            balloonAlert.addClass('alert-danger');
                            balloonAlert.text(balloonAlertMsg);
                            balloonAlert.addClass('d-flex');
                            balloonAlert.removeClass('d-none');
                        }
                    },
                    error: function(response){
                    },
                });
            }
        }
        function changeBalloonDeliveryMethod(that) {

            const balloonCourierInfo = $(that).parents().find('#balloonCourierInfo');
            const balloonisChanged = $(that).parents().find('#isChanged');
            const balloonAlert = $(that).parents().find('#balloonAlert');

            balloonAlert.text('');
            balloonisChanged.val('true');

            if ($(that).val() === 'courier') {
                balloonCourierInfo.removeClass('d-none');
                balloonCourierInfo.addClass('d-flex');
            } else {
                balloonCourierInfo.addClass('d-none');
                balloonCourierInfo.removeClass('d-flex');
            }
        }
    </script>

    <script>
        $( document ).ready(function() {
            var ballooonPreset = '';
            ymaps.ready(['Panel']).then(function () {
                const map = new ymaps.Map('map', {
                    center: [55.755381, 37.619044],
                    zoom: 5,
                    controls: []
                });
                const objectManager = new ymaps.ObjectManager({
                    clusterize: false,
                    hasBalloon: false,
                });
                @if($data['countOrdersWithGeoLocation'] > 0)
                    var features = [];
                @foreach($data['orders'] as $item)
                @if ($item['latitude'] !== null && $item['longitude'] !== null)

                // координаты для метки
                var coordinates = [];
                coordinates.push({{$item['latitude']}});
                coordinates.push({{$item['longitude']}});

                var feature = {
                        type: 'Feature',
                        id: null,
                        geometry: {
                            type: 'Point',
                            coordinates: coordinates
                        },
                        properties: {
                            balloonContentHeader: 'Заказ {{ $item['number'] }}',
                            iconCaption: '{{ $item['deliveryTime'] }}',
                            balloonContentBody: getBalloonContentBody(<?php echo json_encode($item); ?>),
                        },
                        options: {
                            iconColor: '{{ $item['iconColor'] }}',
                            balloonMinWidth: 400,
                            balloonMinHeight: 400,
                        }
                    };
                feature.id = {{ $item['id'] }}
                features.push(feature);

                @endif
                @endforeach



                objectManager.add({
                    type: 'FeatureCollection',
                    features: features
                });

                let panel = new ymaps.Panel();
                map.controls.add(panel, {
                    float:
                        'left'
                });

                objectManager.objects.events.add('click', function (e) {
                    let objectId = e.get('objectId');
                    let feature = objectManager.objects.getById(objectId);

                    let htmlBalloonContentBody = feature.properties.balloonContentBody;
                    let htmlBalloonContentBodyParsed = $($.parseHTML(htmlBalloonContentBody));
                    let balloonOrderId = parseInt(htmlBalloonContentBodyParsed[7].value);

                    const allUpdatedOrders = $('#allUpdatedOrders');

                    if (allUpdatedOrders.val() !== "") {
                        let existData = JSON.parse(allUpdatedOrders.val());

                        for(let i=0; i < existData.length; i++) {

                            if(existData[i].orderId === balloonOrderId) {

                                let newItem = {
                                    'id' : existData[i].orderId,
                                    'externalId' : existData[i].externalId,
                                    'site' : existData[i].orderData.balloonSite,
                                    'customerFirstName' : existData[i].orderData.customerFirstName,
                                    'createdAt' : existData[i].orderData.createdAt,
                                    'deliveryDate' : existData[i].orderData.deliveryDate,
                                    'deliveryTime' : existData[i].orderData.deliveryTime,
                                    'delivery' : {
                                        'code' : existData[i].orderData.balloonDeliveryMethodInfo,
                                        'data' : {
                                            'courierId' : parseInt(existData[i].orderData.delivery.courierId),
                                            'id' : parseInt(existData[i].orderData.delivery.courierId),
                                        },
                                    },
                                    'iconColor' : existData[i].iconColor,
                                    'items' : [],
                                    'status' : existData[i].orderData.status,
                                    'geoQuery' : existData[i].orderData.geoQuery,
                                };

                                if (typeof(existData[i].items) !== "undefined" && existData[i].items.length > 0) {
                                    for (let n = 0; n < existData[i].items.length > 0; n++) {
                                        if (typeof(existData[i].items[n].offer.displayName) !== "undefined") {

                                            newItem.items.push({
                                                'offer' : {
                                                  'displayName' : existData[i].items[n].offer.displayName
                                                },
                                                'name' : existData[i].items[n].offer.displayName,
                                                'quantity' : existData[i].items[n].quantity,
                                            });
                                        }
                                    }
                                }

                                objectManager.objects.getById(objectId).properties.balloonContentBody = getBalloonContentBody(newItem);
                                objectManager.objects.getById(objectId).options.iconColor = newItem.iconColor;
                            }
                        }
                    }

                    panel.setContent(feature.properties.balloonContentBody);
                    map.panTo(feature.geometry.coordinates, {useMapMargin: true});

                });

                map.geoObjects.add(objectManager);
                @endif
            });

            function setSelectedTrueForDeliveryMethod(orderMethodCode, deliveryMethodCode) {
                let deliveryCode = null;

                if (typeof deliveryMethodCode === 'object') {
                    deliveryCode = deliveryMethodCode.code;
                } else {
                    deliveryCode = deliveryMethodCode;
                }

                if (orderMethodCode === deliveryCode) {
                    return 'selected="true"';
                } else {
                    return '';
                }
            }

            function setSelectedTrueForCourier(orderSelectedCourierId, existCourierId) {
                let finalExistCourierId = null;



                if (typeof existCourierId === 'object') {
                    finalExistCourierId = existCourierId.exId;
                } else {
                    finalExistCourierId = existCourierId;
                }

                if (orderSelectedCourierId.toString() === finalExistCourierId) {
                    return 'selected="true"';
                } else {
                    return '';
                }
            }

            function getBalloonCourierInfoForBalloonContentBody(item) {

                    if (item.isCourierSelected === false) {
                        return  '<div id="balloonCourierInfo" class="mt-3 mb-2 d-flex align-items-center justify-content-between">' +
                            '<select autocomplete="off" onchange="changeBalloonCourier(this);" class="custom-select custom-select-md pl-1">' +
                            '<option value="courier" selected="true">Не выбрано</option>' +
                                @foreach($data['couriers'] as $existCourier)
                                    '<option value="{{ $existCourier["exId"] }}">{{ $existCourier['firstName'] }}</option>' +
                                @endforeach
                                    '</select>' +
                            '<button onclick="saveChanges(this, '+item.id+');" class="btn btn-primary">Применить</button>' +
                            '</div>';
                    } else {
                        return  '<div id="balloonCourierInfo" class="mt-3 mb-2 d-flex align-items-center justify-content-between">' +
                            '<select autocomplete="off" onchange="changeBalloonCourier(this);" class="custom-select custom-select-md pl-1">' +
                                @foreach($data['couriers'] as $existCourier)
                                    '<option '+setSelectedTrueForCourier(item.delivery.data.id, <?php echo json_encode($existCourier); ?>)+' value="{{ $existCourier["exId"] }}">{{ $existCourier['firstName'] }}</option>' +
                                @endforeach
                                    '</select>' +
                            '<button onclick="saveChanges(this, '+item.id+');" class="btn btn-primary">Применить</button>' +
                            '</div>';
                    }
            }

            function getNotSelectedDeliveryMethod(item)
            {
                if (item.delivery.code === 'not_selected') {
                    return '<option value="not_selected" selected="true">Не выбрано</option>'
                }
            }

            function getItemsForBalloonContentBody(item)
            {
                let html = '';
                for(let i = 0; i < item.items.length; i++) {
                    html += '<li>'+item.items[i].offer.displayName+' '+item.items[i].quantity+' шт.</li>';
                }
                return html;
            }


            function getBalloonContentBody(item) {

                return  '<div>' +
                    '<div>' +

                    // если у заказа метод доставки = курьером
                    // то выводим список всех активных курьеров для возможности выбора в виде <select>
                    getBalloonCourierInfoForBalloonContentBody(item) +

                    '<div class="d-block pb-2">' +
                    '<div id="balloonAlert" style="max-width: 350px;" class="mx-auto d-none alert my-3" role="alert"></div>' +
                    '<div>' +
                    '<p id="notChanges" class="d-none h6 mt-3 text-danger">Внесите изменения!</p>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +

                    '<p class="h7 mb-1"><b>Заказ:</b> <a class="text-primary" target="_blank" href="{{ $config['url'] }}/orders/'+item.id+'/edit">'+item.externalId+'</a></p>' +
                    // '<p class="h7 mb-1"><b>Клиент:</b> '+item.customerFirstName+'</p>' +
                    '<p class="h7 mb-1"><b>Дата создания:</b> '+item.createdAt+'</p>' +
                    '<p class="h7 mb-1"><b>Дата доставки:</b> '+item.deliveryDate+'</p>' +
                    '<p class="h7 mb-1"><b>Время доставки:</b> '+item.deliveryTime+'</p>' +
                    '<p class="h7 mb-1"><b>Статус:</b> '+item.status+'</p>' +
                    '<p class="h7 mb-1"><b>Адрес:</b> '+item.geoQuery+'</p>' +
                    '</div>' +
                    '<div>' +


                    '<p class="h7 mb-1 font-weight-bold mt-3">Состав заказа:</p>' +
                    '<ul style="max-width: 300px;list-style-type: square;" class="my-2">' +
                    getItemsForBalloonContentBody(item) +
                    '</ul>' +
                    '</div>' +
                    '<hr>' +

                    '<input type="hidden" id="balloonDeliveryMethod" name="balloonDeliveryMethod" value="'+item.delivery.code+'">' +
                    '<input type="hidden" id="balloonCourier" name="balloonCourier" value="">' +
                    '<input type="hidden" id="isChanged" name="isChanged" value="false">' +
                    '<input type="hidden" id="balloonSite" name="balloonSite" value="'+item.site+'">' +
                    '<input type="hidden" id="balloonOrderId" name="balloonSite" value="'+item.id+'">'

                    ;
            }

            function getBalloonContentFooter(item, feature) {
                return  '<div class="d-block" style="position: absolute; bottom: 0;">' +
                    '<div id="balloonAlert" class="alert mb-0 mt-3" role="alert"></div>' +
                    '<div class="my-2 py-2">' +
                    '<p id="notChanges" class="d-none h6 mt-3 text-danger">Внесите изменения!</p>' +
                    '<button onclick="saveChanges(this, item.id, feature);" class="btn btn-primary">Применить</button>' +
                    '</div>' +
                    '</div>';
            }

        });


    </script>
    <script>
        $(document).ready(function () {
            $('.datepicker').datepicker({
                dateFormat: "dd-mm-yy"
            });
        });
    </script>

            <script>
                function selectStatus(that) {
                    let selectedStatuses = $('#selectedStatuses');
                    let existStatuses = selectedStatuses.val();
                    let statusCode = $(that).val();
                    if (existStatuses === '' || existStatuses === '[]') {
                        if ($(that).prop('checked') === true) {
                            let newStatuses = [];
                            newStatuses.push(statusCode);
                            selectedStatuses.val(JSON.stringify(newStatuses));
                        }
                    } else {
                        let statuses = JSON.parse(existStatuses);

                        if ($(that).prop('checked') === true) {

                            let isFound = false;
                            for (let i = 0; i < statuses.length; i++) {
                                if (statuses[i] === statusCode) {
                                    isFound = true;
                                }
                            }
                            if (isFound === false) {
                                statuses.push(statusCode);
                            }

                        } else {
                            let isFound = false;
                            let foundIndex = 0;
                            for (let t = 0; t < statuses.length; t++) {
                                if (statuses[t] === statusCode) {
                                    isFound = true;
                                    foundIndex = t;
                                }
                            }
                            if (isFound === true) {
                                statuses.splice(foundIndex, 1);
                            }
                        }

                        selectedStatuses.val(JSON.stringify(statuses));
                    }
                }
            </script>
@stop
