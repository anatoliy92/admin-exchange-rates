<?php namespace Avl\ExchangeRates\Controllers\Site;

use App\Http\Controllers\Site\Sections\SectionsController;
	use Avl\ExchangeRates\Models\ExchangeRatesData;
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

			$rates = ExchangeRatesData::WhereIn('rate_id', $request->input('rates'))->good()->whereBetween('relevant', [$request->input('beginDate'), $request->input('endDate')])->orderBy('relevant', 'ASC')->get();

			share([
				'rates' => $this->prepare($rates, $request->input()),
			]);

			return view('exchangerates::site.exchangerates.graph');
		}

		return redirect()->back();
	}

	public function prepare ($rates, $input)
	{
		$records = [];

		foreach ($rates as $rate) {
			$records[$rate->rate->code]['title'] = $rate->rate->title_ru;
			$records[$rate->rate->code]['code'] = $rate->rate->code;
			$records[$rate->rate->code]['datasets'][] = $rate->amount;
		}

		$relevants = [];
		$rates = $rates->groupBy('relevant')->toArray();
		foreach ($rates as $relevant => $rate) {
			$relevants[] = $relevant;
		}

		$dataset = [];
		foreach ($records as $code => $record) {
			$colorLine = randomHex();
			$dataset[] = [
				'label' => $record['code'],
				'pointBackgroundColor' => $colorLine,
				'pointRadius' => 5,
				'pointStrokeColor' => $colorLine,
				'backgroundColor' => $colorLine,
				'borderColor' => $colorLine,
				'data' => $record['datasets'],
				'fill' => true,
			];
		}

		return [
			'labels' => $relevants,
			'datasets' => $dataset
		];
	}

}
