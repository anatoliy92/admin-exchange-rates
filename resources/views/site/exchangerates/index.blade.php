@extends('site.templates.pages')

@section('css')
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
@endsection

@section('js')
	<script type="text/javascript">
		$(document).ready(function () {
			$(".change--form-action").hover(function () {
				$(".form--action").attr('action', $(this).val());
			});
			// Ставлю пока временно ограничение на выделение курсов за раз
			$('.checkbox--course').on('click', function () {
	      if ($('.checkbox--course:checked').length > 3) {
	        return false;
	      }
	      return true;
	    });
		});
	</script>
@endsection

@section('content')

	<div class="container mb-5">

		<div class="py-5 text-center">
			<h2>{{ $section->name }}</h2>
		</div>

		<div class="row">

			<div class="col-12">
				{{ Form::open(['url' => route('site.exchangerates.graph', ['alias' => $section->alias]), 'class' => 'form--action' ]) }}
					<div class="card">
						<div class="card-header">
							Курсы по состоянию на: {{ $records->relevant }}
						</div>
						<div class="card-body">
							<table class="table table-bordered table-striped">
								<tbody>
									@php $records = $records->rates ?? [] ; ksort($records); @endphp

									@forelse ($records as $code => $record)
										@if ($code)
											<tr>
												<td style="width: 30px;">
													{{ Form::checkbox('rates[]', $code, false, ['class' => 'checkbox--course']) }}
												</td>
												<td>{{ $record['unit'] }} {{ $record['title_' . LaravelLocalization::getCurrentLocale()] }}</td>
												<td>{{ $code . ' / KZT' }}</td>
												<td>{{ $record['amount'] }}</td>
												<td class="text-center" style="width: 50px;">
													@if ($previous)
														@if ($record['amount'] != $previous['rates'][$code]['amount'])
															@if ($record['amount'] > $previous['rates'][$code]['amount'])
																<i class="fa fa-arrow-up text-danger"></i>
															@else
																<i class="fa fa-arrow-down text-success"></i>
															@endif
														@endif
													@endif
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

						<div class="card-footer">
							<div class="row border-bottom pb-2 mb-2">
								<div class="col-12">
									Начальная дата: {{ Form::text('beginDate', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) }}
									Конечная дата: {{ Form::text('endDate', \Carbon\Carbon::now()->format('Y-m-d')) }}
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									<div class="btn-group" role="group" aria-label="Basic example">
										<button type="submit" name="action" value="{{ route('site.exchangerates.graph', ['alias' => $section->alias]) }}"  class="change--form-action btn btn-primary">Показать график</button>
										<button type="submit" name="action" value="{{ route('site.exchangerates.report', ['alias' => $section->alias]) }}" class="change--form-action btn btn-success">Показать отчет</button>
										<button type="submit" name="action" value="{{ route('site.exchangerates.excel', ['alias' => $section->alias]) }}" class="change--form-action btn btn-warning">Выгрузить отчет в Excel</button>
									</div>
								</div>
							</div>
						</div>

					</div>
				{{ Form::close() }}
			</div>

		</div>
	</div>

@endsection
