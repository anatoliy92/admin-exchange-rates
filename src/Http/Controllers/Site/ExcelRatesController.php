<?php namespace Avl\ExchangeRates\Controllers\Site;

use App\Http\Controllers\Site\Sections\SectionsController;
	use Avl\ExchangeRates\Models\ExchangeRates;

	use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;

	use Illuminate\Http\Request;
	use App\Models\Sections;
	use Carbon\Carbon;
	use View;

class ExcelRatesController extends SectionsController
{

	protected $spreadsheet;

	protected $sheet;

	public function __construct (Request $request)
	{
		$this->spreadsheet = new Spreadsheet();

		$this->spreadsheet->getProperties()->setCreator('nationalbank.kz')
											->setLastModifiedBy('nationalbank.kz')
											->setTitle('Course')
											->setDescription('Course');

		$this->sheet = $this->spreadsheet->getActiveSheet();

		// Отключаем debugbar
		app('debugbar')->disable();
	}

	public function excel (Request $request)
	{

		$spreadsheet = $this->spreadsheet->getActiveSheet()->setTitle('Courses');

		$rates = ExchangeRates::good()->whereBetween('relevant', [$request->input('beginDate'), $request->input('endDate')])->get();

		$spreadsheet->setCellValue('A1', 'Date');
		$spreadsheet->getColumnDimension('A')->setWidth(13);

		$letter = 'B';
		foreach ($request->input('rates') as $code) {
			$spreadsheet->setCellValue($letter . '1', $code . '_quant');
			$spreadsheet->getColumnDimension($letter)->setWidth(13);

			++$letter;
			$spreadsheet->setCellValue($letter. '1', $code);
			$spreadsheet->getColumnDimension($letter)->setWidth(13);

			$letter++;
		}

		$i = 2;
		foreach ($rates as $index => $rate) {
			$spreadsheet->setCellValue('A' . $i, $rate->relevant);

			$letter = 'B';
			foreach ($request->input('rates') as $code) {

				$spreadsheet->setCellValue($letter . $i, $rate->rates[$code]['unit'] ?? '');
				$spreadsheet->setCellValue(++$letter. $i, $rate->rates[$code]['amount'] ?? '');

				$letter++;
			}

			$i++;
		}

		$this->close();
	}

	public function close ()
	{

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . gmdate('D, d M Y H:i:s') . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');

		$writer->save('php://output');
	}
}
