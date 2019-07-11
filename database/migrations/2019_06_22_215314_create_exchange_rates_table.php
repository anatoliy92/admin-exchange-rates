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
						$table->boolean('good')->default(false);

						$table->jsonb('rates');

						// $langs = Langs::all();
						// foreach ($langs as $lang) { $table->string('title_' . $lang->key)->nullable(); }
						// $table->string('code')->comment('Код валюты');
						// $table->string('unit')->nullable();
						// $table->double('amount', 8, 2);
						// $table->boolean('direction')->default(true)->comment('true - to top, false - to bottom');
						// $table->string('course')->default('KZT')->comment('В какую валюту - Тенге, KZT');

						$table->date('relevant')->comment('Дата актуальности:');
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
