<table class="table table-bordered table-striped" style="border-spacing: 0;">
	@if ($records)
		<thead>
			<tr>
				<th></th>
				@foreach ($records['titles'] as $id => $title)
					<th class="text-center align-middle">Числовое значение</th>
					<th class="text-center align-middle">{{ $title['title'] }}</th>
					<th width="8"></th>
				@endforeach
			</tr>
		</thead>
	@endif

	<tbody>
		@if (isset($records['records']))
			@foreach ($records['records'] as $relevant => $record)
				<tr>
					<td style="width: 100px;">{{ $record['relevant'] }}</td>
					@foreach ($record['rates'] as $rate)
						<td class="text-center">{{ $rate['unit'] }}</td>
						<td class="text-center">{{ $rate['amount'] }}</td>
						<td></td>
					@endforeach
				</tr>
			@endforeach
		@else
			<tr>
				<td colspan="{{ count($records['titles']) * 3 + 1 }}">Курсы не загружены</td>
			</tr>
		@endif
	</tbody>
</table>
