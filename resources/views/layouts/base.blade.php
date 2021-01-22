<html>

<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.23/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"/>
    <link rel="stylesheet" type="text/css" href="/css/fontawesome/all.css"/>

    <style>
        #orders_wrapper {
            width: 100%;
        }
    </style>

    @yield('css')
    @yield('head-js')
</head>

<body>

    @include('components.navbar')

    @yield('content')

    <script
            src="https://code.jquery.com/jquery-3.5.1.js"
            integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
            crossorigin="anonymous"></script>
    <script
            src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
            integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.23/datatables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#orders').DataTable( {
                searching: false,
                lengthChange: false,
                pageLength: 10,
                autoWidth: false,
                autoFill: {
                    horizontal: true
                },
                language: {
                    "processing": "Подождите...",
                    "search": "Поиск:",
                    "lengthMenu": "Показать _MENU_ записей",
                    "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                    "infoEmpty": "Записи с 0 до 0 из 0 записей",
                    "infoFiltered": "(отфильтровано из _MAX_ записей)",
                    "loadingRecords": "Загрузка записей...",
                    "zeroRecords": "Записи отсутствуют.",
                    "emptyTable": "В таблице отсутствуют данные",
                    "paginate": {
                        "first": "Первая",
                        "previous": "Предыдущая",
                        "next": "Следующая",
                        "last": "Последняя"
                    },
                    "aria": {
                        "sortAscending": ": активировать для сортировки столбца по возрастанию",
                        "sortDescending": ": активировать для сортировки столбца по убыванию"
                    },
                    "select": {
                        "rows": {
                            "_": "Выбрано записей: %d",
                            "0": "Кликните по записи для выбора",
                            "1": "Выбрана одна запись"
                        },
                        "1": "%d ряд выбран",
                        "_": "%d ряда(-ов) выбрано",
                        "cells": {
                            "1": "1 ячейка выбрана",
                            "_": "Выбрано %d ячеек"
                        },
                        "columns": {
                            "1": "1 столбец выбран",
                            "_": "%d столбцов выбрано"
                        }
                    },
                    "searchBuilder": {
                        "conditions": {
                            "string": {
                                "notEmpty": "Не пусто",
                                "startsWith": "Начинается с",
                                "contains": "Содержит",
                                "empty": "Пусто",
                                "endsWith": "Заканчивается на",
                                "equals": "Равно",
                                "not": "Не"
                            },
                            "date": {
                                "after": "После",
                                "before": "До",
                                "between": "Между",
                                "empty": "Пусто",
                                "equals": "Равно",
                                "not": "Не",
                                "notBetween": "Не между",
                                "notEmpty": "Не пусто"
                            },
                            "moment": {
                                "after": "После",
                                "before": "До",
                                "between": "Между",
                                "empty": "Не пусто",
                                "equals": "Между",
                                "not": "Не",
                                "notBetween": "Не между",
                                "notEmpty": "Не пусто"
                            },
                            "number": {
                                "between": "В промежутке от",
                                "empty": "Пусто",
                                "equals": "Равно",
                                "gt": "Больше чем",
                                "gte": "Больше, чем равно",
                                "lt": "Меньше чем",
                                "lte": "Меньше, чем равно",
                                "not": "Не",
                                "notBetween": "Не в промежутке от",
                                "notEmpty": "Не пусто"
                            }
                        },
                        "data": "Данные",
                        "deleteTitle": "Удалить условие фильтрации",
                        "logicAnd": "И",
                        "logicOr": "Или",
                        "title": {
                            "0": "Конструктор поиска",
                            "_": "Конструктор поиска (%d)"
                        },
                        "value": "Значение",
                        "add": "Добавить условие",
                        "button": {
                            "0": "Конструктор поиска",
                            "_": "Конструктор поиска (%d)"
                        },
                        "clearAll": "Очистить всё",
                        "condition": "Условие"
                    },
                    "searchPanes": {
                        "clearMessage": "Очистить всё",
                        "collapse": {
                            "0": "Панели поиска",
                            "_": "Панели поиска (%d)"
                        },
                        "count": "{total}",
                        "countFiltered": "{shown} ({total})",
                        "emptyPanes": "Нет панелей поиска",
                        "loadMessage": "Загрузка панелей поиска",
                        "title": "Фильтры активны - %d"
                    },
                    "thousands": ",",
                    "buttons": {
                        "pageLength": {
                            "_": "Показать 10 строк",
                            "-1": "Показать все ряды",
                            "1": "Показать 1 ряд"
                        },
                        "pdf": "PDF",
                        "print": "Печать",
                        "collection": "Коллекция <span class=\"ui-button-icon-primary ui-icon ui-icon-triangle-1-s\"><\/span>",
                        "colvis": "Видимость столбцов",
                        "colvisRestore": "Восстановить видимость",
                        "copy": "Копировать",
                        "copyKeys": "Нажмите ctrl or u2318 + C, чтобы скопировать данные таблицы в буфер обмена.  Для отмены, щелкните по сообщению или нажмите escape.",
                        "copySuccess": {
                            "1": "Скопирована 1 ряд в буфер обмена",
                            "_": "Скопировано %ds рядов в буфер обмена"
                        },
                        "copyTitle": "Скопировать в буфер обмена",
                        "csv": "CSV",
                        "excel": "Excel"
                    },
                    "decimal": ".",
                    "infoThousands": ",",
                    "autoFill": {
                        "cancel": "Отменить",
                        "fill": "Заполнить все ячейки <i>%d<i><\/i><\/i>",
                        "fillHorizontal": "Заполнить ячейки по горизонтали",
                        "fillVertical": "Заполнить ячейки по вертикали",
                        "info": "Пример автозаполнения"
                    }
                }
            } );
        } );
    </script>


    @yield('js')
</body>

</html>