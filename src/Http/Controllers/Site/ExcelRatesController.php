<?php namespace Avl\ExchangeRates\Controllers\Site;

use App\Http\Controllers\Site\Sections\SectionsController;
	use Avl\ExchangeRates\Models\ExchangeRatesData;
	use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use Avl\ExchangeRates\Traits\RatesTrait;
	use Illuminate\Http\Request;
	use View;

class ExcelRatesController extends SectionsController
{
	use RatesTrait;

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

		$rates = ExchangeRatesData::whereIn('rate_id', $request->input('rates'))
															->good()
															->whereBetween('relevant', [$request->input('beginDate'), $request->input('endDate')])
															->with(['rate'])
															->orderBy('rate_id')
															->orderBy('relevant', 'DESC')
															->get();

		$records = $this->prepare($request->input(), $rates);

		$spreadsheet->setCellValue('A1', 'Date');
		$spreadsheet->getColumnDimension('A')->setWidth(13);

		$letter = 'B';
		foreach ($records['titles'] as $title) {
			$spreadsheet->setCellValue($letter . '1', $title['code'] . '_quant');
			$spreadsheet->getColumnDimension($letter)->setWidth(13);

			++$letter;
			$spreadsheet->setCellValue($letter. '1', $title['code']);
			$spreadsheet->getColumnDimension($letter)->setWidth(13);

			$letter++;
		}

		if (isset($records['records'])) {
			$i = 2;
			foreach ($records['records'] as $relevant => $record) {
				$spreadsheet->setCellValue('A' . $i, $relevant);

				$letter = 'B';
				foreach ($record['rates'] as $rate) {

					$spreadsheet->setCellValue($letter . $i, $rate['unit'] ?? '');
					$spreadsheet->setCellValue(++$letter. $i, $rate['amount'] ?? '');

					$letter++;
				}

				$i++;
			}
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
