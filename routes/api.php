<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/wallet/balance/','ApiController\WalletController@wallet_balance');

Route::match(['get','post'],'/get/currency/rates','ApiController\CurrencyRateController@reloadCurrencyRate');
Route::match(['get','post'],'/convert/edcoin','ApiController\ConversionController@convert');
Route::match(['get','post'],'/check/coin/rate','ApiController\ConversionController@checkCoinRate');
Route::match(['get','post'],'/get/transaction/logs','ApiController\ConversionController@getTransactionLogs');
// Route::match(['get','post'],'/check/edpoint/total','ApiController\ConversionController@checkTotalEDPoint');
Route::match(['get','post'],'/check/walletcode','ApiController\ConversionController@checkWalletCode');
