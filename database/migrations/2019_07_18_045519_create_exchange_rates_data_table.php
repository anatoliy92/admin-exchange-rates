<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeRatesDataTable extends Migration
{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
				Schema::create('exchange-rates-data', function (Blueprint $table) {
						$table->bigIncrements('id');
						$table->integer('rate_id');
						$table->boolean('good')->default(false)->comment('Вкл/Выкл');
						$table->date('relevant')->comment('Дата актуальности:');
						$table->string('unit')->nullable();
						$table->double('amount', 8, 2)->nullable();
						$table->string('course')->default('KZT')->comment('В какую валюту - Тенге, KZT');
						$table->boolean('up')->nullable()->comment('true to up');
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
				Schema::dropIfExists('exchange-rates-data');
		}
}
