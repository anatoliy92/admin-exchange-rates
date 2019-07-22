<?php namespace Avl\ExchangeRates\Controllers\Admin;

use Avl\ExchangeRates\Models\ExchangeRatesData;
	use Avl\ExchangeRates\Models\ExchangeRates;
	use App\Http\Controllers\Avl\AvlController;
	use Avl\ExchangeRates\Traits\RatesTrait;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use App\Models\{ Langs, Sections };
	use Illuminate\Http\Request;
	use Carbon\Carbon;
	use SoapClient;
	use Validator;

class ExchangeRatesController extends AvlController
{
		use RatesTrait;

		protected $langs = null;

		protected $section = null;

		public function __construct (Request $request) {

			parent::__construct($request);

			$this->langs = Langs::get();

			$this->section = Sections::whereId($request->id)->firstOrFail() ?? null;
		}

		/**
		 * Страница вывода списка новостей к определенному новостному разделу
		 * @param  int  $id      номер раздела
		 * @param  Request $request
		 * @return \Illuminate\Http\Response
		 */
		public function index($id, Request $request)
		{
				$this->authorize('view', $this->section);

				return view('exchangerates::admin.exchangerates.index', [
					'section' => $this->section,
					'rates' => ExchangeRatesData::groupBy('relevant')->orderBy('relevant', 'DESC')->paginate(30)
				]);
		}

		/**
		 * Вывод формы на добавление новостей
		 * @param  int $id     Номер раздела
		 * @return [type]     [description]
		 */
		public function create($id)
		{
				$this->authorize('create', $this->section);
				// $this->getRates(Carbon::yesterday()->format('Y-m-d'));

				return view('exchangerates::admin.exchangerates.create', [
					'section' => $this->section,
					'records' => $this->getRates()
				]);
		}

		/**
		 * Метод для добавления новой записи в базу
		 * @param  Request $request
		 * @param  int  $id      номер раздела
		 * @return redirect to index or create method
		 */
		public function store($id, Request $request)
		{
			$this->authorize('create', $this->section);

			$validator = Validator::make($request->input(), [
				'rates' => 'required',
				'rates.*.unit' => 'required',
				'rates.*.title_kz' => 'required',
				'rates.*.title_ru' => 'required',
				'rates.*.title_en' => 'required',
				'rates.*.amount' => 'required|numeric',
				'rates.*.course' => '',
				'relevant' => 'required|date_format:"Y-m-d"|unique:exchange-rates-data,relevant',
			], [
				'rates.*.unit.required' => 'Проверьте, все ли поля заполнены',
				'rates.*.title_kz.required' => 'Проверьте, все ли поля заполнены',
				'rates.*.title_ru.required' => 'Проверьте, все ли поля заполнены',
				'rates.*.title_en.required' => 'Проверьте, все ли поля заполнены',
				'rates.*.amount.required' => 'Проверьте, все ли поля в столбце <b>Значение</b> заполнены',
				'rates.*.amount.numeric' => 'Значение в поле <b>Значение</b> должно быть числовым',
				'relevant.required' => 'Дата не указана',
				'relevant.date_format' => 'Формат даты не верен',
				'relevant.unique' => 'Курсы на выбранную дату уже загружены'
			]);

			if (!$validator->fails()) {

				foreach ($request->input('rates') as $rate) {
					$existRate = ExchangeRates::whereCode($rate['code'])->first();

					if (!$existRate) {
						$existRate = new ExchangeRates();
						$existRate->section_id = $this->section->id;
						$existRate->code = $rate['code'];
						$existRate->title_ru = $rate['title_ru'] ?? null;
						$existRate->title_kz = $rate['title_kz'] ?? null;
						$existRate->title_en = $rate['title_en'] ?? null;
						$existRate->save();
					}

					$rates = new ExchangeRatesData();

					$rates->rate_id = $existRate->id;
					// $rates->good = 1;
					$rates->relevant = $request->input('relevant');
					$rates->unit = $rate['unit'];
					$rates->amount = $rate['amount'];
					$rates->course = $rate['course'];

					$lastRate = ExchangeRates::whereCode($rate['code'])->first();
					if ($lastRate) {
						$lastRateData = $lastRate->rates()->orderBy('relevant', 'DESC')->first();
						if ($lastRateData) {
							if ($lastRateData->amount < (double)$rate['amount']) {
								$rates->up = true;
							}
							if ($lastRateData->amount > (double)$rate['amount']) {
								$rates->up = false;
							}
						}
					}

					$rates->save();

				}

				return redirect()->route('adminexchangerates::sections.exchangerates.index', ['id' => $this->section->id])->with(['success' => ['Сохранение прошло успешно!']]);
			}

			return redirect()->route('adminexchangerates::sections.exchangerates.create', ['id' => $this->section->id, 'date' => $request->input('relevant') ])
											->withInput()
											->withErrors($validator);
		}

		/**
		 * Отобразить запись на просмотр
		 * @param  int $id      Номер раздела
		 * @param  int $rate_id Номер записи
		 * @return \Illuminate\Http\Response
		 */
		public function show($id, $rate_id)
		{
				$this->authorize('view', $this->section);

				return view('exchangerates::admin.exchangerates.show', [
					'section' => $this->section,
					'relevant' => $rate_id,
					'records' => $this->getRates($rate_id)
				]);
		}

		/**
		 * Форма открытия записи на редактирование
		 * @param  int $id      Номер раздела
		 * @param  int $rate_id Номер записи
		 * @return \Illuminate\Http\Response
		 */
		public function edit($id, $rate_id)
		{
				$this->authorize('update', $this->section);

				return view('exchangerates::admin.exchangerates.edit', [
					'section' => $this->section,
					'relevant' => $rate_id,
					'records' => $this->getRates($rate_id)
				]);
		}

		/**
		 * Метод для обновления определенной записи
		 * @param  Request $request
		 * @param  int  $id      Номер раздела
		 * @param  int  $news_id Номер записи
		 * @return redirect to index method
		 */
		public function update($id, $rate_id, Request $request)
		{
				$this->authorize('update', $this->section);

				$validator = Validator::make($request->input(), [
					'rates' => 'required',
					'rates.*.unit' => 'required',
					'rates.*.title_kz' => 'required',
					'rates.*.title_ru' => 'required',
					'rates.*.title_en' => 'required',
					'rates.*.amount' => 'required|numeric',
					'rates.*.course' => '',
				], [
					'rates.*.unit.required' => 'Проверьте, все ли поля заполнены',
					'rates.*.title_kz.required' => 'Проверьте, все ли поля заполнены',
					'rates.*.title_ru.required' => 'Проверьте, все ли поля заполнены',
					'rates.*.title_en.required' => 'Проверьте, все ли поля заполнены',
					'rates.*.amount.required' => 'Проверьте, все ли поля в столбце <b>Значение</b> заполнены',
					'rates.*.amount.numeric' => 'Значение в поле <b>Значение</b> должно быть числовым'
				]);

				if (!$validator->fails()) {

					// foreach ($request->input('rates') as $r_id => $postRate) {
					// 	$rateData = ExchangeRatesData::findOrFail($r_id);
					// 	$rateData->unit = $postRate['unit'];
					// 	$rateData->amount = $postRate['amount'];
					// 	$rateData->course = $postRate['course'];
					//
					// 	$rate = $rateData->rate;
					// 	foreach ($this->langs as $lang) {
					// 		$rate->{'title_' . $lang->key} = $postRate['title_' . $lang->key];
					// 	}
					// 	$rate->save();
					//
					// 	if ($rateData->save()) {
					// 		return redirect()->route('adminexchangerates::sections.exchangerates.index', ['id' => $this->section->id])->with(['success' => ['Сохранение прошло успешно!']]);
					// 	}
					// }

				}

				return redirect()->route('adminexchangerates::sections.exchangerates.edit', [ 'id' => $this->section->id, 'exchange' => $rate_id ])
												->withInput()
												->withErrors($validator);

		}

		/**
		 * Удаление записи и всех медиа файлов
		 * @param  int $id      Номер раздела
		 * @param  int $news_id Номер записи
		 * @return json
		 */
		public function destroy($id, $news_id, Request $request)
		{
			$this->authorize('delete', $this->section);

			// TODO: ???

			return ['errors' => ['Ошибка удаления.']];
		}

		/**
		 * Метод для добавления новой записи в базу
		 * @param  Request $request
		 * @param  int  $id      номер раздела
		 * @return redirect to index or create method
		 */
		public function upload(Request $request, $id)
		{
			$this->authorize('create', $this->section);

			$this->validate(request(), [
					'relevant' => 'required|date_format:"Y-m-d"',
					'docfile' => 'required|mimes:xls,xlsx'
			]);

			$spreadsheet = IOFactory::load($request->file('docfile'));

			$sheetData = $spreadsheet->getActiveSheet(1)->toArray(null, true, true, true);

			if (count($sheetData) > 0) {
				$records = [];

				foreach ($sheetData as $data) {
					if (!is_null($data['A'])) {
						$rate = ExchangeRates::where('section_id', $this->section->id)->whereCode($data['A'])->first();
						$records[] = [
							'code' => $data['A'],
							'unit' => $data['B'],
							'title_kz' => $rate->title_kz ?? $data['C'],
							'title_ru' => $rate->title_ru ?? $data['C'],
							'title_en' => $rate->title_en ?? $data['C'],
							'amount' => $data['D'],
							'course' => $data['E']
						];
					}
				}

				return view('exchangerates::admin.exchangerates.create', [
					'section' => $this->section,
					'records' => $records
				]);
			}

			$validator = new Validator();
			return redirect()->route('adminexchangerates::sections.exchangerates.create', ['id' => $this->section->id])->with(['errors' => $validator->errors()->add('docfile', 'asdasd') ]);
		}

		/**
		 * Получить данные из nbportal
		 * @param  integer  $id
		 * @param  Request $request
		 * @return Response
		 */
		public function receiveNSI ($id, Request $request)
		{
			if ($this->isDomainAvailible(config('exchangerates.WSDL_NSI_COURSE'))) {
				$client = new SoapClient(config('exchangerates.WSDL_NSI_COURSE'));

				$result = $client->GET_GUIDE([
					'guideCode' => 'NSI_NBRK_CRCY_COURSE',
					'type' => 'CHAD',
					'beginDate' => Carbon::parse($request->input('date'))->format('Y-m-d\TH:i:s.uP'),
					'endDate' => Carbon::parse($request->input('date'))->format('Y-m-d\TH:i:s.uP')
				]);

				$xml = simplexml_load_string($result->return->result);
				$json = json_encode($xml);
				$responseArray = json_decode($json, true);

				if ($responseArray !== false) {
					$i = 1;
					foreach ($responseArray['Body']['Entity'] as $entity) {

						$rate = ExchangeRates::whereCode($entity['EntityCustom']['CurrCode'])->first();
						$records[$i] = [
							"code" => $entity['EntityCustom']['CurrCode'],
							"unit" => $entity['EntityCustom']['Corellation'],
							"rate_id" => $rate->id ?? 0,
							"title_kz" => $rate->title_kz ?? '',
							"title_ru" => $rate->title_ru ?? '',
							"title_en" => $rate->title_en ?? '',
							"amount" => str_replace(',', '.', $entity['EntityCustom']['Course']),
							"course" => "ТЕНГЕ"
						];
						$i++;
					}
				}

				return [
					'success' => true,
					'html' => view('exchangerates::admin.exchangerates.snippets.create', [
						'records' => $records ?? []
					])->render()
				];
			}

			return ['errors' => ['Сервис не доступен.']];
		}

		/**
		 * Check WSDL url avaliable
		 * @param  string  $url
		 * @return boolean
		 */
		protected function isDomainAvailible($url) {
			$handle = curl_init($url);
			curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

			/* Get the HTML or whatever is linked in $url. */
			$response = curl_exec($handle);

			/* Check for 404 (file not found). */
			$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
			curl_close($handle);

			/* If the document has loaded successfully without any redirection or error */
			if ($httpCode === 200) {
					return true;
			} else {
					return false;
			}
		}

}
