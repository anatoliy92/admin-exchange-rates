@extends('site.templates.pages')

@section('css')
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
@endsection

@section('content')
	<div class="container mb-5">

		<div class="py-5 text-center">
			<h2>{{ $section->name }}</h2>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="table-responsive">
					@include('exchangerates::site.exchangerates.blocks.report')
				</div>
				<a href="{{ route('site.exchangerates.pdf', [
														'alias' => $section->alias,
														'rates' => request()->input('rates'),
														'beginDate' => request()->input('beginDate'),
														'endDate' => request()->input('endDate'),
													]) }}">Скачать в PDF</a>
			</div>

		</div>
	</div>
@endsection
