<?php namespace Avl\ExchangeRates\Controllers\Site;

use App\Http\Controllers\Site\Sections\SectionsController;
	use Avl\ExchangeRates\Models\ExchangeRates;
	use Illuminate\Http\Request;
	use App\Models\Sections;
	use Carbon\Carbon;
	use View;
	use Dompdf\Css\Stylesheet;
	use Dompdf\Dompdf;
	use Dompdf\Options;

class ReportRatesController extends SectionsController
{

	public function report (Request $request)
	{
		if ($request->input('rates')) {
			$rates = ExchangeRates::good()->whereBetween('relevant', [$request->input('beginDate'), $request->input('endDate')])->get();

			return view('exchangerates::site.exchangerates.report', [
				'rates' => $rates
			]);
		}

		return redirect()->back();
	}

	public function pdf (Request $request)
	{
		$rates = ExchangeRates::good()->whereBetween('relevant', [$request->input('beginDate'), $request->input('endDate')])->get();
		$domPdf = new Dompdf();

		$domPdf->loadHtml(view('exchangerates::site.exchangerates.blocks.basePdf', [
			'template' => 'exchangerates::site.exchangerates.blocks.report',
			'rates' => $rates,
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
