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
			<i class="fa fa-align-justify"></i> {{ $section->name_ru }}
			@can('create', $section)
				<div id="datepicker-container" class="position-absolute" style="display: none; right: 0px; z-index: 10;">
					<div id="relevant"> </div>
				</div>
				<div class="card-actions">
					<a href="#" id="show-datepicker" class="text-dark" style="width: 120px;"><span>{{ date('Y-m-d') }}</span> <i class="fa fa-calendar"></i></a>
					<a href="{{ route('adminexchangerates::sections.exchangerates.create', ['id' => $section->id, 'date' => date('Y-m-d')]) }}" id="url-create" class="w-70 bg-primary text-white" title="Добавить"><i class="fa fa-plus"></i></a>
				</div>
			@endcan
		</div>
		<div class="card-body">

				@if ($rates)
					<div class="table-responsive">
						@php $iteration = 30 * ($rates->currentPage() - 1); @endphp

						<table class="table table-bordered">
							<thead>
								<tr>
									<th width="50" class="text-center">#</th>
									<th width="50" class="text-center"></th>
									<th>Актуально</th>
									<th class="text-center" style="width: 160px">Дата публикации</th>
									<th class="text-center" style="width: 100px;">Действие</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($rates as $rate)
									<tr>
										<td class="text-center">{{ ++$iteration }}</td>
										<td class="text-center">
											<a class="change--status" href="#" data-id="{{ $rate->id }}" data-model="Avl\ExchangeRates\Models\ExchangeRates">
												<i class="fa @if ($rate->good){{ 'fa-eye' }}@else{{ 'fa-eye-slash' }}@endif"></i>
											</a>
										</td>
										<td>{{ $rate->relevant }}</td>
										<td>{{ $rate->created_at }}</td>
										<td class="text-right">
											<div class="btn-group" role="group">
												@can('view', $section) <a href="{{ route('adminexchangerates::sections.exchangerates.show', ['id' => $section->id, 'exchangerate' => $rate->id]) }}" class="btn btn btn-outline-primary" title="Просмотр"><i class="fa fa-eye"></i></a> @endcan
												@can('update', $section) <a href="{{ route('adminexchangerates::sections.exchangerates.edit', ['id' => $section->id, 'exchangerate' => $rate->id]) }}" class="btn btn btn-outline-success" title="Изменить"><i class="fa fa-edit"></i></a> @endcan
												@can('delete', $section) <a href="#" class="btn btn btn-outline-danger remove--record" title="Удалить"><i class="fa fa-trash"></i></a> @endcan
											</div>
											@can('delete', $section)
												<div class="remove-message">
														<span>Вы действительно желаете удалить запись?</span>
														<span class="remove--actions btn-group btn-group-sm">
																<button class="btn btn-outline-primary cancel"><i class="fa fa-times-circle"></i> Нет</button>
																<button class="btn btn-outline-danger remove--news" data-id="{{ $rate->id }}" data-section="{{ $section->id }}"><i class="fa fa-trash"></i> Да</button>
														</span>
												</div>
											 @endcan
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>

					</div>
				@endif

		</div>
	</div>
@endsection
