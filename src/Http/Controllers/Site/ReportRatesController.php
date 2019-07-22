<?php namespace Avl\ExchangeRates\Controllers\Site;

use App\Http\Controllers\Site\Sections\SectionsController;
	use Avl\ExchangeRates\Models\ExchangeRatesData;
	use Avl\ExchangeRates\Models\ExchangeRates;
	use Avl\ExchangeRates\Traits\RatesTrait;
	use Illuminate\Http\Request;
	use Dompdf\Css\Stylesheet;
	use Dompdf\Options;
	use Dompdf\Dompdf;
	use Carbon\Carbon;
	use View;

class ReportRatesController extends SectionsController
{
	use RatesTrait;

	protected $rates = null;

	public function __construct (Request $request)
	{
		parent::__construct($request);

		$this->rates = ExchangeRatesData::whereIn('rate_id', $request->input('rates'))
															->good()
															->whereBetween('relevant', [$request->input('beginDate'), $request->input('endDate')])
															->with(['rate'])
															->orderBy('rate_id')
															->orderBy('relevant', 'DESC')
															->get();
	}

	public function report (Request $request)
	{
		if ($request->input('rates')) {
			return view('exchangerates::site.exchangerates.report', [
				'records' => $this->prepare($request->input(), $this->rates)
			]);
		}

		return redirect()->back();
	}

	public function pdf (Request $request)
	{
		$domPdf = new Dompdf();

		$domPdf->loadHtml(view('exchangerates::site.exchangerates.blocks.basePdf', [
			'template' => 'exchangerates::site.exchangerates.blocks.report',
			'records' => $this->prepare($request->input(), $this->rates),
			'request' => $request->input()
		])->render());

		// $style = new Stylesheet($domPdf);
		// // $style->load_css_file(public_path('/site/cache/app.css'));
		// $style->load_css_file(public_path('/site/bootstrap.css'));
		// $domPdf->setCss($style);

		$domPdf->setOptions(new Options([
			'defaultFont' => 'sans-serif'
		]));

		$domPdf->render();
		$output = $domPdf->stream(gmdate('D, d M Y H:i:s'));
	}

}
