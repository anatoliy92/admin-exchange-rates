<?php namespace Avl\ExchangeRates\Controllers\Site;

use App\Http\Controllers\Site\Sections\SectionsController;
	use Avl\ExchangeRates\Models\ExchangeRates;
	use Illuminate\Http\Request;
	use App\Models\Sections;
	use Carbon\Carbon;
	use Cache;
	use View;

class ExchangeRatesController extends SectionsController
{

	public function index (Request $request)
	{
		$template = 'site.templates.exchangerates.short.' . $this->getTemplateFileName($this->section->current_template->file_short) . '1';

		$template = (View::exists($template)) ? $template : 'exchangerates::site.exchangerates.index';

		$records = $this->section->rates()->good()->whereDate('relevant', '<=', Carbon::now())->orderBy('relevant', 'DESC')->limit(2)->get();

		return view($template, [
				'records' => $records->first(),
				'previous' => $records->last() ?? [],
				'request' => $request
		]);
	}
}
