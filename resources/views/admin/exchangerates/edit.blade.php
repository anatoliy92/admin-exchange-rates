@extends('avl.default')

@section('js')
	<script src="/avl/js/jquery-ui/jquery-ui.min.js" charset="utf-8"></script>
@endsection

@section('css')
	<link rel="stylesheet" href="/avl/js/jquery-ui/jquery-ui.min.css">
@endsection

@section('main')
	<div class="card">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Редактирование курсов по состоянию на: <b>{{ $relevant }}</b>
			<div class="card-actions">
				<a href="{{ route('adminexchangerates::sections.exchangerates.index', [ 'id' => $section->id ]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
				<button type="submit" form="submit" name="button" class="btn btn-success pl-3 pr-3" style="width: 70px;" title="Сохранить"><i class="fa fa-floppy-o"></i></button>
			</div>
		</div>

		<div class="card-body">

			<div class="card">
				<div class="card-body p-2">
					{{ Form::open(['url' => route('adminexchangerates::sections.exchangerates.update', ['id' => $section->id, 'exchangerate' => $relevant]), 'class' => 'needs-validation', 'novalidate', 'id' => 'submit' ]) }}
						@method('PUT')

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
							<tbody>
								@forelse ($records as $id => $record)
										<tr>
											<td class="text-center">
												{{ $record['code'] }}
											</td>
											<td class="text-center">
												{{ $record['unit'] }}
												{{ Form::hidden('rates[' . $id . '][unit]', $record['unit']) }}
											</td>
											@if ($langs)
												@foreach ($langs as $lang)
													<td>{{ Form::text('rates[' . $id . '][title_' . $lang->key . ']', $record['title_' . $lang->key], ['class' => 'form-control']) }}</td>
												@endforeach
											@endif
											<td>
												<div class="input-group">
													{{ Form::text('rates[' . $id . '][amount]', $record['amount'], ['class' => 'form-control']) }}
													<div class="input-group-append">
														{{ Form::hidden('rates[' . $id . '][course]', $record['course']) }}
														<span class="input-group-text">{{ ($record['course'] === 'ТЕНГЕ') ? 'KZT' : $record['course'] }}</span>
													</div>
												</div>
											</td>
										</tr>
								@empty
									<tr>
										<td colspan="6">Данные по курсам не загружены</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					{{ Form::close() }}
				</div>
			</div>

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
