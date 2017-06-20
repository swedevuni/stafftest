@extends('layouts.admin')

@section('content')

<!-- Workers Section -->
<section id="workers" class="after-nav-margin">
    <div class="container">
        <div class="form-wrapper">
            <form>
                <div class="input-group">
                    <span class="input-group-addon">Отдел</span>
                    <select name="department" class="form-control">
                        <option value="">Все варианты</option>
                        @foreach ($departments as $department)
                        <option value="{{ $department->id }}"
                            @if (app('request')->input('department') == $department->id)
                            selected="selected"
                            @endif
                        >{{ $department->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <span class="input-group-addon">Работает в компании?</span>
                    <select name="working" class="form-control">
                        <option value="">Все варианты</option>
                        @foreach (['true' => 'Да', 'false' => 'Нет'] as $value => $label)
                        <option value="{{ $value }}"
                            @if (app('request')->input('working') == $value)
                            selected="selected"
                            @endif
                        >{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-default" type="submit">Фильтровать</button>
            </form>
            <form>
                <button class="btn btn-default clear-btn" type="submit">Сбросить</button>
            </form>
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
                    <td>{{ $worker->id }}</td>
                    <td>
                        <a href="{{ url('/admin/workers/' . $worker->id) }}">
                            {{ $worker->surname }}
                        </a>
                    </td>
                    <td>{{ $worker->name }}</td>
                    <td>{{ $worker->patronymic }}</td>
                    <td>{{ $worker->birthdate }}</td>
                    <td>{{ $worker->email }}</td>
                    <td>{{ $worker->phone }}</td>
                    <td>{{ $worker->work_start }}</td>
                    <td>{{ $worker->work_end }}</td>
                    <td>{{ $worker->position }}</td>
                    <td>{{ $worker->department->title }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $workers->links() }}
    </div>
</section>

@endsection
