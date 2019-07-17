<?php namespace Avl\ExchangeRates\Controllers\Admin;

use App\Http\Controllers\Avl\AvlController;
use App\Models\{ Langs, Sections };
use Illuminate\Http\Request;
use Avl\ExchangeRates\Models\ExchangeRates;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use SoapClient;
use Validator;
use Auth;
use File;

class ExchangeRatesController extends AvlController
{

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
					'rates' => ExchangeRates::orderBy('relevant', 'DESC')->paginate(30)
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

				return view('exchangerates::admin.exchangerates.create', [
					'section' => $this->section,
					'records' => ExchangeRates::where('section_id', $this->section->id)->orderBy('relevant', 'DESC')->first() ?? []
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
				'relevant' => 'required|date_format:"Y-m-d"|unique:exchange-rates,relevant',
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
				$rates = new ExchangeRates();
				$rates->section_id = $this->section->id;
				$rates->rates = $request->input('rates');
				$rates->relevant = $request->input('relevant');

				if ($rates->save()) {
					return redirect()->route('adminexchangerates::sections.exchangerates.index', ['id' => $this->section->id])->with(['success' => ['Сохранение прошло успешно!']]);
				}

			}

			return redirect()->route('adminexchangerates::sections.exchangerates.create', [ 'id' => $this->section->id, 'date' => $request->input('relevant') ])
											->with(['records' => $request->input()])
											->withErrors($validator);
		}

		/**
		 * Отобразить запись на просмотр
		 * @param  int $id      Номер раздела
		 * @param  int $news_id Номер записи
		 * @return \Illuminate\Http\Response
		 */
		public function show($id, $rate_id)
		{
				$this->authorize('view', $this->section);

				return view('exchangerates::admin.exchangerates.show', [
					'section' => $this->section,
					'records' => ExchangeRates::findOrFail($rate_id)
				]);
		}

		/**
		 * Форма открытия записи на редактирование
		 * @param  int $id      Номер раздела
		 * @param  int $news_id Номер записи
		 * @return \Illuminate\Http\Response
		 */
		public function edit($id, $rate_id)
		{
				$this->authorize('update', $this->section);

				return view('exchangerates::admin.exchangerates.edit', [
					'section' => $this->section,
					'records' => ExchangeRates::findOrFail($rate_id)
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
					$rates = ExchangeRates::findOrFail($rate_id);

					$rates->rates = $request->input('rates');

					if ($rates->save()) {
						return redirect()->route('adminexchangerates::sections.exchangerates.index', ['id' => $this->section->id])->with(['success' => ['Сохранение прошло успешно!']]);
					}

				}

				return redirect()->route('adminexchangerates::sections.exchangerates.edit', [ 'id' => $this->section->id, 'exchange' => $rate_id ])
												->with(['records' => $request->input()])
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
				$rates = ExchangeRates::where('section_id', $this->section->id)->orderBy('relevant', 'DESC')->first();

				foreach ($sheetData as $data) {
					if (!is_null($data['A'])) {
						$records[$data['A']] = [
							'unit' => $data['B'],
							'title_kz' => (array_key_exists($data['A'], $rates->rates ?? [])) ? $rates->rates[$data['A']]['title_kz'] : $data['C'],
							'title_ru' => (array_key_exists($data['A'], $rates->rates ?? [])) ? $rates->rates[$data['A']]['title_ru'] : $data['C'],
							'title_en' => (array_key_exists($data['A'], $rates->rates ?? [])) ? $rates->rates[$data['A']]['title_en'] : $data['C'],
							'amount' => $data['D'],
							'course' => $data['E']
						];
					}
				}

				return view('exchangerates::admin.exchangerates.create', [
					'section' => $this->section,
					'records' => ['rates' => $records]
				]);
			}

			$validator = new Validator();
			return redirect()->route('adminexchangerates::sections.exchangerates.create', ['id' => $this->section->id])->with(['errors' => $validator->errors()->add('docfile', 'asdasd') ]);
		}


		public function receiveNSI ($id, Request $request)
		{
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

			$rate = ExchangeRates::where('section_id', $id)->orderBy('relevant', 'DESC')->first()->toArray();

			$records = $rate;

			if ($responseArray !== false) {
				foreach ($responseArray['Body']['Entity'] as $entity) {
					$records['rates'][$entity['EntityCustom']['CurrCode']]['unit'] = $entity['EntityCustom']['Corellation'];
					$records['rates'][$entity['EntityCustom']['CurrCode']]['amount'] = str_replace(',', '.', $entity['EntityCustom']['Course']);
				}
			}
			// dd($responseArray['Body']['Entity']);

			return [
				'success' => true,
				'html' => view('exchangerates::admin.exchangerates.snippets.create', [
					'records' => $records
				])->render()
			];
		}

}
