{{-- @php $records = session('records')['rates'] ?? $records->rates ?? $records['rates'] ?? [] ; ksort($records); @endphp --}}

@forelse ($records as $record)
	<tr>
		<td class="text-center">
			{{ $record['code'] }}
			{{ Form::hidden('rates[' . $loop->index . '][code]', $record['code']) }}
		</td>
		<td class="text-center">
			{{ $record['unit'] }}
			{{ Form::hidden('rates[' . $loop->index . '][unit]', $record['unit']) }}
		</td>
		@if ($langs)
			@foreach ($langs as $lang)
				<td>{{ Form::text('rates[' . $loop->parent->index . '][title_' . $lang->key . ']', $record['title_' . $lang->key], ['class' => 'form-control']) }}</td>
			@endforeach
		@endif
		<td>
			<div class="input-group">
				{{ Form::text('rates[' . $loop->index . '][amount]', $record['amount'], ['class' => 'form-control']) }}
				<div class="input-group-append">
					{{ Form::hidden('rates[' . $loop->index . '][course]', $record['course']) }}
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
