@extends('layouts.admin')

@section('content')

<!-- Workers Section -->
<section id="worker" class="after-nav-margin">
    <div class="container">
        <h1>Карточка сотрудника #{{ $data['id'] }}</h1>
        <ul class="list-group grid">
            @foreach ($data as $label => $value)
            <li class="list-group-item row">
                <span class="caption col-xs-5 col-sm-4 col-md-3">@lang('worker.data.' . $label)</span>
                <span class="value col-xs-7 col-sm-8 col-md-9">{{ $value }}</span>
            </li>
            @endforeach
        </ul>
    </div>
</section>

@endsection
