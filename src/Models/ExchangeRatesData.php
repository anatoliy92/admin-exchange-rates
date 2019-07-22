<?php namespace Avl\ExchangeRates\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;
use LaravelLocalization;

class ExchangeRatesData extends Model
{
		use ModelTrait;

		protected $table = 'exchange-rates-data';

		protected $guarded = [];

		protected $modelName = __CLASS__;

		public function rate ()
		{
			return $this->belongsTo(ExchangeRates::class, 'rate_id', 'id');
		}

		public function scopeGood ($query)
		{
			return $query->whereGood(true);
		}

}
