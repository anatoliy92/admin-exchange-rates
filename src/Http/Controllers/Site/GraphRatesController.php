<?php namespace Avl\ExchangeRates\Controllers\Site;

use App\Http\Controllers\Site\Sections\SectionsController;
	use Avl\ExchangeRates\Models\ExchangeRates;
	use Illuminate\Http\Request;
	use App\Models\Sections;
	use Carbon\Carbon;
	use View;

class GraphRatesController extends SectionsController
{

	public function graph (Request $request)
	{
		if ($request->input('rates')) {
			$rates = ExchangeRates::good()->whereBetween('relevant', [$request->input('beginDate'), $request->input('endDate')])->orderBy('relevant', 'ASC')->get();

			share([
				'rates' => $this->prepare($rates, $request->input()),
			]);

			return view('exchangerates::site.exchangerates.graph', [
				'rates' => $rates
			]);
		}

		return redirect()->back();
	}

	public function prepare ($rates, $input)
	{
		$ratesCollect = collect($rates->toArray());

		$records = [];

		foreach ($ratesCollect as $collect) {
			$rateCollect = \collect($collect['rates']);
			foreach ($input['rates'] as $code) {
				if ($rateCollect->has($code)) {
					$records[$code]['title'] = $rateCollect->get($code)['title_ru'];
					$records[$code]['datasets'][$collect['relevant']] = $rateCollect->get($code)['amount'];
					$records[$code]['datasets'] = \array_values($records[$code]['datasets']);
				}
			}
		}

		$relevants = [];
		foreach ($ratesCollect as $index => $rate) {
			$relevants[] = $rate['relevant'];
		}

		$dataset = [];
		foreach ($records as $code => $record) {
			$colorLine = randomHex();
			$dataset[] = [
				'label' => $record['title'],
				'backgroundColor' => $colorLine,
				'borderColor' => $colorLine,
				'data' => $record['datasets'],
				'fill' => false,
			];
		}

		return [
			'labels' => $relevants,
			'datasets' => $dataset
		];
	}

}
