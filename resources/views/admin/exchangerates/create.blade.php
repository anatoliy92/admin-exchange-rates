@extends('avl.default')

@section('js')
	<script src="/avl/js/jquery-ui/jquery-ui.min.js" charset="utf-8"></script>
	<script src="{{ asset('vendor/exchangerates/js/exchangerates.js') }}" charset="utf-8"></script>
@endsection

@section('css')
	<link rel="stylesheet" href="/avl/js/jquery-ui/jquery-ui.min.css">
@endsection

@section('main')
	<div class="card">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Добавление курсов
			<div class="card-actions">
				<a href="{{ route('adminexchangerates::sections.exchangerates.index', [ 'id' => $section->id ]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
				<button type="submit" form="submit" name="button" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить"><i class="fa fa-floppy-o"></i></button>
			</div>
		</div>

		<div class="card-body position-relative">
			{{ Form::hidden(null, $section->id, ['id' => 'section--id']) }}

			<div class="card">
				<div class="card-body p-2">
					{{ Form::open(['url' => route('adminexchangerates::sections.exchangerates.upload', ['id' => $section->id, 'date' => request()->input('date')]), 'class' => 'needs-validation', 'novalidate', 'files' => true ]) }}
						<div class="row">
							{{ Form::hidden('relevant', request()->input('date'), ['id' => 'relevant']) }}

							<div class="col-12 col-sm-2">
								{{ Form::file('docfile', ['class' => 'form-control']) }}
							</div>
							<div class="col-12 col-sm-2">
								<div class="btn-group" role="group" aria-label="Basic example">
									{{ Form::submit('Загрузить из Excal', ['class' => 'btn btn-primary']) }}
									{{ Form::button('Обновить из НСИ', ['class' => 'btn btn-success', 'id' => 'update-nsi']) }}
								</div>
							</div>
						</div>
					{{ Form::close() }}
				</div>
			</div>

			<div class="card">
				<div class="card-body p-2">
					{{ Form::open(['url' => route('adminexchangerates::sections.exchangerates.store', ['id' => $section->id]), 'class' => 'needs-validation', 'novalidate', 'id' => 'submit' ]) }}
						{{ Form::hidden('relevant', request()->input('date')) }}

						<table class="table table-bordered">
							<thead>
								<tr>
									<th class="align-middle">Код</th>
									<th class="align-middle" style="width: 80px;">Кол-во</th>
									@if ($langs)
										@foreach ($langs as $lang)
											<th class="col-3 align-middle">Наименование [{{ mb_strtoupper($lang->key) }}]</th>
										@endforeach
									@endif
									<th class="align-middle">Значение</th>
								</tr>
							</thead>
							<tbody id="receiveNSI">
								@include('exchangerates::admin.exchangerates.snippets.create')
							</tbody>
						</table>
					{{ Form::close() }}
				</div>
			</div>

			<div class="card-body-overlay"></div>
		</div>

		<div class="card-footer position-relative">
			<i class="fa fa-align-justify"></i> Добавление курсов
			<div class="card-actions">
				<a href="{{ route('adminexchangerates::sections.exchangerates.index', [ 'id' => $section->id ]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
				<button type="submit" form="submit" name="button" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить"><i class="fa fa-floppy-o"></i></button>
			</div>
		</div>
	</div>
@endsection
