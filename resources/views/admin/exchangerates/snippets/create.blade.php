@php $records = session('records')['rates'] ?? $records->rates ?? $records['rates'] ?? [] ; ksort($records); @endphp

@forelse ($records as $code => $record)
	@if ($code)
		<tr>
			<td class="text-center">
				{{ $code }}
			</td>
			<td class="text-center">
				{{ $record['unit'] }}
				{{ Form::hidden('rates[' . $code . '][unit]', $record['unit']) }}
			</td>
			@if ($langs)
				@foreach ($langs as $lang)
					<td>{{ Form::text('rates[' . $code . '][title_' . $lang->key . ']', $record['title_' . $lang->key], ['class' => 'form-control']) }}</td>
				@endforeach
			@endif
			<td>
				<div class="input-group">
					{{ Form::text('rates[' . $code . '][amount]', $record['amount'], ['class' => 'form-control']) }}
					<div class="input-group-append">
						{{ Form::hidden('rates[' . $code . '][course]', $record['course']) }}
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
