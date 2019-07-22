<?php namespace Avl\ExchangeRates\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;
use LaravelLocalization;

class ExchangeRates extends Model
{
		use ModelTrait;

		protected $table = 'exchange-rates';

		protected $guarded = [];

		protected $modelName = __CLASS__;

		protected $lang = null;

		public function __construct ()
		{
			$this->lang = LaravelLocalization::getCurrentLocale();
		}

		public function section ()
		{
			return $this->belongsTo('App\Models\Sections', 'section_id', 'id');
		}

		public function rates ()
		{
			return $this->hasMany(ExchangeRatesData::class, 'rate_id', 'id');
		}

		public function scopeGood ($query)
		{
			return $query->whereGood(true);
		}

		public function getTitleAttribute ($value, $lang = null) {
			$title = (!is_null($lang)) ? $lang : $this->lang;

			return ($this->{'title_' . $title}) ? $this->{'title_' . $title} : $this->title_ru ;
		}
}
