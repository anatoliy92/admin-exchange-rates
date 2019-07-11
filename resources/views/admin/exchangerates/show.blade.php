@extends('avl.default')

@section('main')
	<div class="card">
		<div class="card-header">
			<i class="fa fa-align-justify"></i> Просмотр курсов по состоянию на: <b>{{ $records->relevant }}</b>
			<div class="card-actions">
				<a href="{{ route('adminexchangerates::sections.exchangerates.index', [ 'id' => $section->id ]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
			</div>
		</div>

		<div class="card-body">

			<div class="card">
				<div class="card-body p-2">
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
								@forelse ($records->rates as $record)
									@if ($record['code'])
										<tr>
											<td class="text-center">{{ $record['code'] }}</td>
											<td class="text-center">{{ $record['unit'] }}</td>
											@if ($langs)
												@foreach ($langs as $lang)
													<td>{{ Form::text(null, $record['title_' . $lang->key], ['class' => 'form-control bg-light', 'readonly']) }}</td>
												@endforeach
											@endif
											<td>
												<div class="input-group">
													{{ Form::text(null, $record['amount'], ['class' => 'form-control bg-light', 'readonly']) }}
													<div class="input-group-append">
														<span class="input-group-text">{{ ($record['course'] === 'ТЕНГЕ') ? 'KZT' : $record['course'] }}</span>
													</div>
												</div>
											</td>
										</tr>
									@endif
								@empty
									<tr>
										<td colspan="6">Данные по курсам не загружены</td>
									</tr>
								@endforelse
							</tbody>
						</table>
				</div>
			</div>

		</div>

		<div class="card-footer position-relative">
			<i class="fa fa-align-justify"></i> Добавление курсов
			<div class="card-actions">
				<a href="{{ route('adminexchangerates::sections.exchangerates.index', [ 'id' => $section->id ]) }}" class="btn btn-default pl-3 pr-3" style="width: 70px;" title="Назад"><i class="fa fa-arrow-left"></i></a>
			</div>
		</div>
	</div>
@endsection
