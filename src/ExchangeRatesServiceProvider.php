<?php namespace Avl\ExchangeRates;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Config;

class ExchangeRatesServiceProvider extends ServiceProvider
{

		/**
		 * Bootstrap the application services.
		 *
		 * @return void
		 */
		public function boot()
		{

				$this->publishes([
						__DIR__ . '/../public' => public_path('vendor/exchangerates'),
				], 'public');

				$this->loadRoutesFrom(__DIR__ . '/routes.php');

				$this->loadViewsFrom(__DIR__ . '/../resources/views', 'exchangerates');
		}

		/**
		 * Register the application services.
		 *
		 * @return void
		 */
		public function register()
		{
				// Добавляем в глобальные настройки системы новый тип раздела
				Config::set('avl.sections.exchangerates', 'Курсы валют');

				// объединение настроек с опубликованной версией
				$this->mergeConfigFrom(__DIR__ . '/../config/exchangerates.php', 'exchangerates');

				// migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
		}

}
