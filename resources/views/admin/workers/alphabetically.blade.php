@extends('layouts.admin')

@section('content')

<!-- Workers Section -->
<section id="alphabetical" class="after-nav-margin">
    <div class="container">
        <div class="btn-group full-width">
            @foreach ($groups as $index => $key)
                <a class="btn btn-default" href="/admin/workers/alphabetically/{{ $index + 1 }}">{{ $key }}</a>
            @endforeach
        </div>
    </div>
    <div class="container-fluid">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>@lang('worker.data.surname')</th>
                    <th>@lang('worker.data.name')</th>
                    <th>@lang('worker.data.patronymic')</th>
                    <th>@lang('worker.data.birthdate')</th>
                    <th>@lang('worker.data.email')</th>
                    <th>@lang('worker.data.phone')</th>
                    <th>@lang('worker.data.work_start')</th>
                    <th>@lang('worker.data.work_end')</th>
                    <th>@lang('worker.data.position')</th>
                    <th>@lang('worker.data.department')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($workers as $worker)
                <tr>
                    <td>{{ $worker['id'] }}</td>
                    <td>
                        <a href="{{ url('/admin/workers/' . $worker['id']) }}">
                            {{ $worker['surname'] }}
                        </a>
                    </td>
                    <td>{{ $worker['name'] }}</td>
                    <td>{{ $worker['patronymic'] }}</td>
                    <td>{{ $worker['birthdate'] }}</td>
                    <td>{{ $worker['email'] }}</td>
                    <td>{{ $worker['phone'] }}</td>
                    <td>{{ $worker['work_start'] }}</td>
                    <td>{{ $worker['work_end'] }}</td>
                    <td>{{ $worker['position'] }}</td>
                    <td>{{ $worker['department']['title'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</section>

@endsection
