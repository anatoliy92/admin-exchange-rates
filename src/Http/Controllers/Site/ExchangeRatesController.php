<?php namespace Avl\ExchangeRates\Controllers\Site;

use App\Http\Controllers\Site\Sections\SectionsController;
	use Avl\ExchangeRates\Models\ExchangeRatesData;
	use Avl\ExchangeRates\Models\ExchangeRates;
	use Avl\ExchangeRates\Traits\RatesTrait;
	use Illuminate\Http\Request;
	use App\Models\Sections;
	use Carbon\Carbon;
	use Cache;
	use View;

class ExchangeRatesController extends SectionsController
{
	use RatesTrait;

	public function index (Request $request)
	{
		$template = 'site.templates.exchangerates.short.' . $this->getTemplateFileName($this->section->current_template->file_short) . '1';

		$template = (View::exists($template)) ? $template : 'exchangerates::site.exchangerates.index';

		return view($template, [
			'records' => $this->getRates(),
			'request' => $request
		]);
	}
}
