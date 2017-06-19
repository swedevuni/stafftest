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
                        @foreach ([1 => 'Да', 0 => 'Нет'] as $k => $v)
                            <option value="{{ $k }}"
                                @if (app('request')->input('working') == $k)
                                    selected="selected"
                                @endif
                            >{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-default" type="submit">Фильтровать</button>
                <!--<input type="hidden" name="page" value="{{ app('request')->input('page') }}">-->
            </form>
            <form>
                @if (app('request')->input('page') > 1)
                    <input type="hidden" name="page" value="{{ app('request')->input('page') }}">
                @endif
                <button class="btn btn-default clear-btn" type="submit">Сбросить</button>
            </form>
        </div>
    </div>
    <div class="container-fluid">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Дата рождения</th>
                    <th>Эл. почта</th>
                    <th>Телефон</th>
                    <th>Начало работы</th>
                    <th>Окончание работы</th>
                    <th>Должность</th>
                    <th>Отдел</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($workers as $worker)
                <tr>
                    <td>{{ $worker->id }}</td>
                    <td><a href="/admin/worker/{{ $worker->id }}">{{ $worker->surname }}</a></td>
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
