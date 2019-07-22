@extends('site.templates.pages')

@section('css')
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="/site/libs/Chart.js-2.8.0/dist/Chart.min.css">
@endsection

@section('js')
	{!! shared()->render() !!}

	<script src="/site/libs/Chart.js-2.8.0/dist/Chart.min.js" charset="utf-8"></script>
	<script src="{{ asset('vendor/exchangerates/site/js/graph.js') }}" charset="utf-8"></script>

@endsection

@section('content')
	<div class="container mb-5">
		<div class="py-5 text-center">
			<h2>{{ $section->name }}</h2>
		</div>

		<div class="row">
			<div class="col-12">
				<canvas id="chart" style="background-color: rgb(51, 51, 51);" width="1000" height="500" class="chartjs-render-monitor"></canvas>
			</div>
		</div>
	</div>
@endsection
