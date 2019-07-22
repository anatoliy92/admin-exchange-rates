<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Langs;

class CreateExchangeRatesTable extends Migration
{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
				Schema::create('exchange-rates', function (Blueprint $table) {
						$table->bigIncrements('id');
						$table->integer('section_id');
						$table->string('code', 30)->comment('Код валюты');
						$table->boolean('home')->default(false)->comment('Показать на главной');

						$langs = Langs::all();
						foreach ($langs as $lang) { $table->string('title_' . $lang->key)->nullable(); }

						$table->timestamps();
				});
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
				Schema::dropIfExists('exchange-rates');
		}
}
