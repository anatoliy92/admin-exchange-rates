<?php namespace Avl\ExchangeRates\Traits;

use Avl\ExchangeRates\Models\ExchangeRatesData;
use Avl\ExchangeRates\Models\ExchangeRates;
use Carbon\Carbon;
/**
 * Для получения курсов валют
 */
trait RatesTrait
{

	function getRates ($relevant = null)
	{
		$records = [];

		if (is_null($relevant)) {
			// Находим последние актуальные данные по курсам
			$lastRate = ExchangeRatesData::orderBy('relevant', 'DESC')->good()->first();
			if ($lastRate) {
				$relevant = $lastRate->relevant;
			}
		}

		$rates = ExchangeRatesData::where('relevant', $relevant)->good()->with(['rate'])->get();

		if ($rates) {
			foreach ($rates as $rate) {
				$records[$rate->id] = [
					'code' => $rate->rate->code,
					'unit' => $rate->unit,
					'rate_id' => $rate->rate->id,
					'title_kz' => $rate->rate->title_kz ?? $rate->rate->title_ru,
					'title_ru' => $rate->rate->title_ru,
					'title_en' => $rate->rate->title_en ?? $rate->rate->title_ru,
					'amount' => $rate->amount,
					'course' => $rate->course,
					'up' => $rate->up,
					'relevant' => $rate->relevant
				];
			}
		}

		return $records;
	}


	/**
	 * Формируем массив для построения таблицы с курсами
	 * @param  array  $input Request $request
	 * @param  items $rates ExchangeRatesData
	 * @return array
	 */
	public function prepare ($input = [], $rates = null) : array
	{
		if (!empty($input)) {
			$rateNames = ExchangeRates::whereIn('id', $input['rates'])->orderBy('id')->get();
			if ($rateNames) {
				foreach ($rateNames as $rateName) {
					$records['titles'][$rateName->id] = [
						'code' => $rateName->code,
						'title' => $rateName->title,
					];
				}

				if (!is_null($rates)) {
					$i = 1;
					foreach ($rates as $rate) {
						$records['records'][$rate->relevant]['relevant'] = $rate->relevant;
						$records['records'][$rate->relevant]['rates'][$rate->rate_id]['unit'] = $rate->unit;
						$records['records'][$rate->relevant]['rates'][$rate->rate_id]['amount'] = $rate->amount;
						$i++;
					}
				}
			}
		}
		// dd($records);

		return $records ?? [];
	}
}
