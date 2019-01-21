<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'PagesController@login');

Route::get('login', array('uses' => 'Auth\LoginController@showLogin'))->name('access.login');

Route::post('login', array('uses' => 'Auth\LoginController@doLogin'))->name('login');
Route::match(['get','post'],'logout', 'Auth\LoginController@logout')->name('logout');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', 'PagesController@index')->name('dashboard');
    #EDCoin Rate Route
    Route::get('/pages/rate/index','EdcoinRateController@index')->name('rate.index');
    Route::match(['get','post'],'/pages/rate/create','EdcoinRateController@registerRate')->name('rate.create');
    Route::match(['get','post'],'/pages/rate/validate','EdcoinRateController@validateEDCoinRate')->name('rate.validate');
    Route::match(['get','post'],'/pages/rate/latest/data','EdcoinRateController@getLatestRecordData')->name('rate.latest.data');

    #Currency Rate Route
    Route::match(['get','post'],'/pages/currency/list','CurrencyRateController@recordList')->name('currency.list');

    #Transaction Route
    Route::get('/pages/transaction/index','TransactionController@index')->name('transaction.index');
    Route::match(['get', 'post'], '/pages/transaction/list', 'TransactionController@recordList')->name('transaction.list');
    Route::get('/bonus/show/data/list', 'BonusController@setMemberLine')->name("board.record");
    Route::match(['get', 'post'], '/bonus/activity', 'BonusActivityController@distribute')->name('compute.bonus');
    Route::match(['get', 'post'], 'pages/family/level', 'FamilyTreeController@updateFamilyTreeLevel');
    Route::get('/pages/bonus/activity', 'BonusController@activity')->name('bonus.activity');
    Route::match(['get', 'post'], '/pages/bonus/activity/list', 'BonusActivityController@recordList')->name('bonus.activity.list');

    Route::get('/pages/edcoin/rate/activity', 'EdcoinRateController@activity')->name('edcoin.activity');
    Route::match(['get', 'post'], '/pages/edcoin/rate/list', 'EdcoinRateController@recordList')->name('edcoin.activity.list');
    Route::get('/pages/currency/rate/activity', 'CurrencyRateController@activity')->name('currency.activity');
    Route::match(['get', 'post'], '/pages/currency/rate/list', 'CurrencyRateController@recordLogs')->name('currency.activity.list');

    #Profile route
    Route::match(['get','post'],'/pages/profile/update','UsersController@updateProfileInformation')->name('profile.update.name');
    Route::match(['get','post'],'/pages/profile/password/update','UsersController@updateProfilePassword')->name('profile.update.password');

    #User Route
    Route::get('/pages/users', 'UsersController@index')->name('users.index');
    Route::post('/pages/users/list', 'UsersController@recordList')->name('users.list');
    Route::match(['get', 'post'], '/pages/users/create', 'UsersController@create')->name('users.create');
    Route::match(['get', 'post'], '/pages/users/modify', 'UsersController@modify')->name('users.modify');
    Route::match(['get', 'post'], '/pages/users/remove', 'UsersController@remove')->name('users.remove');
    Route::match(['get', 'post'], '/pages/users/getData', 'UsersController@getData')->name('users.data');

    #FamilyTree Route
    Route::get('pages/familyTree', 'FamilyTreeController@index')->name('tree.view');

    Route::match(['get', 'post'], 'pages/familyList', 'FamilyTreeController@purchaserFamilyList')->name('tree.member.view');

    #Bonus Route
    Route::get('/pages/bonus', 'BonusController@index')->name('bonus.index');
    Route::match(['get', 'post'], '/pages/bonus/create', 'BonusController@create')->name('bonus.create');
    Route::match(['get', 'post'], '/pages/bonus/modify', 'BonusController@modify')->name('bonus.modify');
    Route::match(['get', 'post'], '/pages/bonus/remove', 'BonusController@remove')->name('bonus.remove');
    Route::match(['get', 'post'], '/pages/bonus/list', 'BonusController@recordList')->name('bonus.list');
    Route::match(['get', 'post'], '/pages/bonus/getData', 'BonusController@getData')->name('bonus.data');

    #Permission Route
    Route::get('/pages/permission', 'PermissionController@index')->name('permission.index');
    Route::match(['get', 'post'], '/pages/permission/create', 'PermissionController@create')->name('permission.create');
    Route::match(['get', 'post'], '/pages/permission/modify', 'PermissionController@modify')->name('permission.modify');
    Route::match(['get', 'post'], '/pages/permission/remove', 'PermissionController@remove')->name('permission.remove');
    Route::match(['get', 'post'], '/pages/permission/list', 'PermissionController@recordList')->name('permission.list');
    Route::match(['get', 'post'], '/pages/permission/getData', 'PermissionController@getData')->name('permission.data');

    #Menu Route
    Route::get('/pages/menu', 'MenuController@index')->name('menu.index');
    Route::match(['get', 'post'], '/pages/menu/create', 'MenuController@create')->name('menu.create');
    Route::match(['get', 'post'], '/pages/menu/modify', 'MenuController@modify')->name('menu.modify');
    Route::match(['get', 'post'], '/pages/menu/remove', 'MenuController@remove')->name('menu.remove');
    Route::match(['get', 'post'], '/pages/menu/permissionList', 'MenuController@permissionRecords')->name('menu.permission.record');
    Route::match(['get', 'post'], '/pages/menu/list', 'MenuController@recordList')->name('menu.list');
    Route::match(['get', 'post'], '/pages/menu/getData', 'MenuController@getData')->name('menu.data');

    #Role Route
    Route::get('/pages/role', 'RoleController@index')->name('role.index');
    Route::post('/pages/role/create', 'RoleController@create')->name('role.create');
    Route::match(['get', 'post'], '/pages/role/modify', 'RoleController@modify')->name('role.modify');
    Route::match(['get', 'post'], '/pages/role/remove', 'RoleController@remove')->name('role.remove');
    Route::match(['get', 'post'], '/pages/role/list', 'RoleController@recordList')->name('role.list');
    Route::match(['get', 'post'], '/pages/role/getData', 'RoleController@getData')->name('role.data');
    Route::match(['get', 'post'], '/pages/role/menuRecords', 'RoleController@menuRecords')->name('menus.active.list');

    # Purchaser Routes
    Route::resource('/pages/purchaser', 'Purchaser\PurchaserController')->except('create', 'edit');
    Route::post('/pages/purchaser/table', 'Purchaser\PurchaserController@table')->name('purchaser.table');
    Route::post('/pages/purchaser/validate/store', 'Purchaser\PurchaserController@validate_store')->name('purchaser.validate.store');
    Route::post('/pages/purchaser/validate/update/{id}', 'Purchaser\PurchaserController@validate_update')->name('purchaser.validate.update');
    Route::post('/pages/purchaser/list/purchaser', 'Purchaser\PurchaserController@list_purchaser');
    Route::post('/pages/purchaser/email/wallet-code/{id}', 'Purchaser\PurchaserController@resend_wallet_code');
    Route::post('/pages/purchaser/list/country', 'Purchaser\PurchaserController@list_country');
    Route::get('/pages/purchaser/download/csv','Purchaser\PurchaserController@download_csv');

    # Package Purchaser Routes
    Route::resource('/pages/purchaser-package', 'Purchaser\PurchaserPackageController')->only(['store', 'show']);
    Route::post('/pages/purchaser-package/table', 'Purchaser\PurchaserPackageController@table')->name('purchaser-package.table');
    Route::post('/pages/purchaser-package/validate/store', 'Purchaser\PurchaserPackageController@validate_store')->name('purchaser-package.validate.store');
    Route::post('/pages/purchaser-package/transact/post', 'Purchaser\PurchaserPackageController@package_post');
    Route::post('/pages/purchaser-package/transact/reject', 'Purchaser\PurchaserPackageController@package_reject');
    Route::post('/pages/purchaser-package/compute/token','Purchaser\PurchaserPackageController@compute_token');

    # Wallet Purchaser Routes
    Route::resource('/pages/purchaser-wallet', 'Purchaser\PurchaserWalletController')->only(['show', 'store']);

    # Country Routes
    Route::resource('/pages/country','Country\CountryController');
    Route::post('/pages/country/table','Country\CountryController@table')->name('country.table');
    Route::post('/pages/country/validate/store', 'Country\CountryController@validate_store')->name('country.validate.store');
    Route::post('/pages/country/validate/update/{id}', 'Country\CountryController@validate_update')->name('country.validate.update');
    Route::get('/pages/country/download/csv','Country\CountryController@download_csv');

    Route::get('/page/download/report','Download\CSVPackageController@index')->name('download.report');
    Route::match(['get','post'],'/download/package/member','Download\CSVPackageController@download');
    Route::match(['get','post'],'/download/distributor/details','Download\CSVPackageController@downloadContributorDetails');
});
