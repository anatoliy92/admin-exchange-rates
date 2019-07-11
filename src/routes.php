<?php

/**
 * Route for news module
 */

Route::group(['namespace' => 'Avl\ExchangeRates\Controllers\Admin', 'middleware' => ['web', 'admin'], 'as' => 'adminexchangerates::'], function () {

	Route::resource('sections/{id}/exchangerates', 'ExchangeRatesController', ['as' => 'sections']);
	Route::post('sections/{id}/exchangerates/upload', 'ExchangeRatesController@upload')->name('sections.exchangerates.upload');
	Route::post('sections/{id}/exchangerates/receiveNSI', 'ExchangeRatesController@receiveNSI')->name('sections.exchangerates.receiveNSI');

});


Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => [ 'localizationRedirect']], function () {

	Route::group(['namespace' => 'Avl\ExchangeRates\Controllers\Site'], function () {
		Route::get('exchangerates/{alias}', 'ExchangeRatesController@index')->name('site.exchangerates.index');
		Route::post('exchangerates/{alias}/graph', 'ExchangeRatesController@graph')->name('site.exchangerates.graph');
		Route::post('exchangerates/{alias}/report', 'ExchangeRatesController@report')->name('site.exchangerates.report');
		Route::post('exchangerates/{alias}/excel', 'ExcelRatesController@excel')->name('site.exchangerates.excel');
	});

});
