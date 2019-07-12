<table class="table table-bordered table-striped" style="border-spacing: 0;">
	<thead>
		<tr>
			<th></th>
			@foreach (request()->input('rates') as $code)
				<th class="text-center align-middle">Числовое значение</th>
				<th class="text-center align-middle">{{ $code }}</th>
				<th width="8"></th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach ($rates as $index => $rate)
			<tr>
				<td style="width: 100px;">{{ $rate->relevant }}</td>
				@foreach (request()->input('rates') as $code)
					<td class="text-center">{{ $rate->rates[$code]['unit'] }}</td>
					<td class="text-center">{{ $rate->rates[$code]['amount'] }}</td>
					<td></td>
				@endforeach
			</tr>
		@endforeach
	</tbody>
</table>
