@extends('layouts.base')

@section('title', 'Главная')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        Перейти к списку курьеров <a href="/couriers">ссылка</a>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        Перейти к карте <a href="/map">ссылка</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop