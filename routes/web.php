<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    // return $router->app->version();
    $version = $router->app->version();
    return $router->app->make('view')->make('welcome', compact('version'));
});

$router->get('/info', function () use ($router) {
    // return phpinfo();
    return "<center><h1>Underconstruction</h1></center>";
});

$router->get('/genpass/{key}', function($key) {
    $hash = app('hash')->make($key);
    $map = array(
        'key'=>$key,
        'hash'=>$hash,
    );

	return json_encode($map);
});

//CLIENT AUTHENTICATION
$router->group(['prefix' => 'clientauth/v1', 'namespace' => 'Account\V1'], function() use ($router) {

    $router->post('login', ['as' => 'clientauth.v1.login', 'uses' => 'ClientAuthController@login']);
    $router->post('relogin', ['as' => 'clientauth.v1.relogin', 'uses' => 'ClientAuthController@relogin']);
    $router->post('register', ['as' => 'clientauth.v1.register', 'uses' => 'ClientAuthController@register']);
    $router->post('verify', ['as' => 'clientauth.v1.verify', 'uses' => 'ClientAuthController@verify']);

    $router->group(['middleware'=> 'jwt.auth' ], function () use ($router) {
        $router->post('logout', ['as' => 'clientauth.v1.logout', 'uses' => 'ClientAuthController@logout']);
        $router->post('refresh', ['as' => 'clientauth.v1.refresh', 'uses' => 'ClientAuthController@refresh']);
        $router->get('whoami', ['as' => 'clientauth.v1.whoami', 'uses' => 'ClientAuthController@whoAmI']);
        $router->get('me', ['as' => 'clientauth.v1.me', 'uses' => 'ClientAuthController@me']);
        $router->post('changepassword', ['as' => 'clientauth.v1.changepassword', 'uses' => 'ClientAuthController@changePassword']);
        $router->post('resetpassword', ['as' => 'clientauth.v1.resetpassword', 'uses' => 'ClientAuthController@resetPassword']);
    });

});


//INTERNAL ACCOUNT AUTHENTICATION
$router->group(['prefix' => 'account/v1', 'namespace' => 'Account\V1'], function() use ($router) {

    $router->post('login', ['as' => 'account.v1.login', 'uses' => 'AuthController@login']);
    $router->post('relogin', ['as' => 'account.v1.relogin', 'uses' => 'AuthController@relogin']);
    $router->post('register', ['as' => 'account.v1.register', 'uses' => 'AuthController@register']);
    $router->post('verify', ['as' => 'account.v1.verify', 'uses' => 'AuthController@verify']);

    $router->group(['middleware'=> 'jwt.auth' ], function () use ($router) {
        $router->post('logout', ['as' => 'account.v1.logout', 'uses' => 'AuthController@logout']);
        $router->post('refresh', ['as' => 'account.v1.refresh', 'uses' => 'AuthController@refresh']);
        $router->get('whoami', ['as' => 'account.v1.whoami', 'uses' => 'AuthController@whoAmI']);
        $router->get('me', ['as' => 'account.v1.me', 'uses' => 'AuthController@me']);
        $router->post('changepassword', ['as' => 'account.v1.changepassword', 'uses' => 'AuthController@changePassword']);
        $router->post('resetpassword', ['as' => 'account.v1.resetpassword', 'uses' => 'AuthController@resetPassword']);

    });

});

//HERO Cleaning
$router->group(['prefix' => 'cleaning/v1', 'namespace' => 'Cleaning\V1'], function() use ($router) {

    //get products
    $router->get('product', ['as' => 'cleaning.products.v1.getproducts',
        'uses' => 'ProductController@getProduct']);

    //send me a product offer
    $router->post('sendmeoffer', ['as' => 'cleaning.products.v1.sendmeoffer',
        'uses' => 'ProductController@sendMeOffer']);

    $router->group(['middleware'=> 'jwt.auth' ], function () use ($router) {

        //get Spp by id
        $router->get('sppbyid', ['as' => 'cleaning.spp.v1.getsppbyid',
            'uses' => 'SppController@getSppById']);
        //get Area By Spp Id
        $router->get('areabysppid', ['as' => 'cleaning.spparea.v1.getareabysppid',
            'uses' => 'SppAreaController@getAreaBySppId']);

        //get Area By Jos Id
        $router->get('areabyjosid', ['as' => 'cleaning.josarea.v1.getareabyjosid',
            'uses' => 'JosAreaController@getAreaByJosId']);

        //get Daily Activity By
        $router->get('dacbyjosjob', ['as' => 'cleaning.dac.v1.getdailybyjosjobid',
            'uses' => 'DailyActivityController@getDailyByJosJobid']);

        //get Daily Activity Report Detail
        $router->get('darbyjos', ['as' => 'cleaning.dar.v1.darbyjos',
            'uses' => 'DailyReportController@getDailyReport']);

        //get Daily Activity Report
        $router->get('dacbyidjos', ['as' => 'cleaning.dar.v1.dacbyidjos',
            'uses' => 'DailyReportController@getDailyActivityReport']);

        //add Daily Activity Report
        $router->post('addnewdar', ['as' => 'cleaning.dar.v1.addnewdar',
            'uses' => 'DailyReportController@addDailyReport']);
        // add Daily report recommendation
        $router->post('addnewdarrec', ['as' => 'cleaning.dar.v1.addnewdarrec',
            'uses' => 'DailyReportController@addDailyReportRecommendation']);

        //delete Daily Activity Report
        $router->post('deletedar', ['as' => 'cleaning.dar.v1.deletedar',
            'uses' => 'DailyReportController@deleteDailyReport']);

        //upload images detil on Daily Activity Report
        $router->post('uploaddailyreportimage', ['as' => 'cleaning.dar.v1.uploaddailyreportimage',
            'uses' => 'DailyReportDetailImagesController@uploadDailyReportImage']);

        //get Daily Activity Report
        $router->get('dailyreportimage', ['as' => 'cleaning.dar.v1.dailyreportimage',
            'uses' => 'DailyReportDetailImagesController@getDailyReportImage']);

        //delete Daily Activity Report Image
        $router->post('deletedailyreportimage', ['as' => 'cleaning.dar.v1.deletedailyreportimage',
            'uses' => 'DailyReportDetailImagesController@deleteDailyReportImage']);



    });

});

//Cleaning Client
$router->group(['prefix' => 'cleaning/client/v1', 'namespace' => 'Cleaning\Client\V1'], function() use ($router) {

    $router->group(['middleware'=> 'jwt.auth' ], function () use ($router) {

        //get Daily Activity By
        $router->get('dacbyjosjob', ['as' => 'cleaning.dac.v1.getdailybyjosjobid',
            'uses' => 'DailyActivityController@getDailyByJosJobid']);

        //get Daily Activity Report
        $router->get('darbyjos', ['as' => 'cleaning.dar.v1.darbyjos',
            'uses' => 'DailyReportController@getDailyReport']);

        $router->get('dailyreportimage', ['as' => 'cleaning.dar.v1.dailyreportimage',
            'uses' => 'DailyReportDetailImagesController@getDailyReportImage']);

        // add Daily report Feedback
        $router->post('addnewdarfeed', ['as' => 'cleaning.dar.v1.addnewdarfeed',
            'uses' => 'DailyReportController@addDailyReportFeedback']);

        //get Daily Activity Report
        $router->get('dacbyidjos', ['as' => 'cleaning.dar.v1.dacbyidjos',
            'uses' => 'DailyReportController@getDailyActivityReport']);

    });
});

//Envidesk Client
$router->group(['prefix' => 'envidesk/client/v1', 'namespace' => 'Envidesk\Client\V1'], function() use ($router) {
    $router->group(['middleware'=> 'jwt.auth' ], function () use ($router) {
        // -------- Ticket Block ----------
        //Get available ticket
        $router->get('getticket', ['as' => 'envidesk.client.v1.getticket',
            'uses' => 'TicketController@getTicket']);
        //Get ticket detail
        $router->get('getticketdetail', ['as' => 'envidesk.client.v1.getticketdetail',
            'uses' => 'TicketController@getTicketDetail']);

        //Create new ticket
        $router->post('createticket', ['as' => 'envidesk.client.v1.createticket',
            'uses' => 'TicketController@createTicket']);

        //Cancel ticket
        $router->post('cancelticket', ['as' => 'envidesk.client.v1.cancelticket',
            'uses' => 'TicketController@cancelTicket']);

        //Rate ticket
        $router->post('rateticket', ['as' => 'envidesk.client.v1.rateticket',
            'uses' => 'TicketController@rateTicket']);

        // -------- Recomendation --------
        //Get available recomendation
        $router->get('getrekomendasi', ['as' => 'envidesk.client.recomendation.v1.getrekomendasi',
            'uses' => 'RekomendasiController@getRekomendasi']);
        //Get available recomendation detail
        $router->get('getrekomendasidetail', ['as' => 'envidesk.client.recomendation.v1.getrekomendasidetail',
            'uses' => 'RekomendasiController@getRekomendasiDetail']);
        //Update read recomendation
        $router->post('updatereadrecomendation', ['as' => 'envidesk.client.recomendation.v1.updatereadrecomendation',
            'uses' => 'RekomendasiController@updateStatusReadRecomendation']);
        //Feedback Recomendation
        $router->post('recomfeedback', ['as' => 'envidesk.client.recomendation.v1.recomfeedback',
            'uses' => 'RekomendasiController@recomFeedback']);
    });
});

//Envidesk Hero
$router->group(['prefix' => 'envidesk/hero/v1', 'namespace' => 'Envidesk\Hero\V1'], function() use ($router) {
    $router->group(['middleware'=> 'jwt.auth' ], function () use ($router) {
        //Get available ticket
        $router->get('getticket', ['as' => 'envidesk.hero.v1.getticket',
            'uses' => 'TicketController@getTicket']);

        //Get ticket detail
        $router->get('getticketdetail', ['as' => 'envidesk.hero.v1.getticketdetail',
            'uses' => 'TicketController@getTicketDetail']);

        //QC ticket In
        $router->post('qcticketin', ['as' => 'envidesk.hero.v1.qcticketin',
            'uses' => 'TicketController@qcTicketIn']);
        //QC ticket Out
        $router->post('qcticketout', ['as' => 'envidesk.hero.v1.qcticketout',
            'uses' => 'TicketController@qcTicketOut']);
        //Action ticket In
        $router->post('actionticketin', ['as' => 'envidesk.hero.v1.actionticketin',
            'uses' => 'TicketController@actionTicketIn']);
        //Action ticket Out
        $router->post('actionticketout', ['as' => 'envidesk.hero.v1.actionticketout',
            'uses' => 'TicketController@actionTicketOut']);

        //Action ticket Out
        $router->post('updatereadcomplaint', ['as' => 'envidesk.hero.v1.updatereadcomplaint',
            'uses' => 'TicketController@updateStatusReadComplaint']);

        // -------- Recomendation --------
        //Get available recomendation
        $router->get('getrekomendasi', ['as' => 'envidesk.hero.recomendation.v1.getrekomendasi',
            'uses' => 'RekomendasiController@getRekomendasi']);
        //Get available recomendation detail
        $router->get('getrekomendasidetail', ['as' => 'envidesk.hero.recomendation.v1.getrekomendasidetail',
            'uses' => 'RekomendasiController@getRekomendasiDetail']);

        //Create new recomendation
        $router->post('createrecomendation', ['as' => 'envidesk.hero.recomendation.v1.createrecomendation',
            'uses' => 'RekomendasiController@createRecommendation']);
    });
});

//Layanan
$router->group(['prefix' => 'layanan/v1', 'namespace' => 'Layanan\V1'], function() use ($router) {
    $router->get('layanan', ['as' => 'layanan.v1.layanan', 'uses' => 'LayananController@getLayanan']);
    $router->get('product_layanan', ['as' => 'layanan.v1.product_layanan', 'uses' => 'ProdukLayananController@getProdukLayanan']);

    $router->group(['middleware'=> 'jwt.auth' ], function () use ($router) {

    });

});

//Pestcontrol
$router->group(['prefix' => 'pestcontrol/v1', 'namespace' => 'Pestcontrol\V1'], function() use ($router) {

    //get products
    $router->get('product', ['as' => 'pestcontrol.products.v1.getproducts',
        'uses' => 'ProductController@getProduct']);

    //send me a product offer
    $router->post('sendmeoffer', ['as' => 'pestcontrol.products.v1.sendmeoffer',
        'uses' => 'ProductController@sendMeOffer']);

    $router->group(['middleware'=> 'jwt.auth' ], function () use ($router) {

        //get scheduled Treatment
        $router->get('schedulestreatment', ['as' => 'pestcontrol.schedulestreatment.v1.getschedulestreatment',
            'uses' => 'ScheduleTreatmentController@getSchedulesTreatment']);
        //get tehcnicians by schedule id
        $router->get('techniciansbyschedule', ['as' => 'pestcontrol.schedulestreatment.v1.gettechniciansbyschedule',
            'uses' => 'ScheduleTreatmentController@getTechniciansBySchedulesTreatmentId']);
        //get Spp by id
        $router->get('sppbyid', ['as' => 'pestcontrol.spp.v1.getsppbyid',
            'uses' => 'SppController@getSppById']);
        //get Area By Spp Id
        $router->get('areabysppid', ['as' => 'pestcontrol.spparea.v1.getareabysppid',
            'uses' => 'SppAreaController@getAreaBySppId']);
        //get Hama By Spp Id
        $router->get('hamabysppid', ['as' => 'pestcontrol.spphama.v1.gethamabysppid',
            'uses' => 'SppHamaController@getHamaBySppId']);

    });
});

//Sales
$router->group(['prefix' => 'sales/v1', 'namespace' => 'Sales\V1'], function() use ($router) {

    $router->group(['middleware'=> 'jwt.auth' ], function () use ($router) {

        //get JOS by client
        $router->get('josbyclient', ['as' => 'sales.jos.v1.josbyclient',
            'uses' => 'JosController@getJosByClient']);

    });
});

//Hero Sales
$router->group(['prefix' => 'sales/hero/v1', 'namespace' => 'Sales\Hero\V1'], function() use ($router) {

    $router->group(['middleware'=> 'jwt.auth' ], function () use ($router) {

        //get JOS by Employee Id
        $router->get('josbyemployee', ['as' => 'sales.jos.v1.josbyemployee',
            'uses' => 'HeroJosController@getJosByEmployeeId']);

        //get JOS Man Power Detail by Jos Id
        $router->get('jmpdbyjosid', ['as' => 'sales.hero.jmpd.v1.jmpdbyjosid',
            'uses' => 'HeroJosManPowerController@getJosMPDByJosId']);

    });
});

//Client Sales
$router->group(['prefix' => 'sales/v1', 'namespace' => 'Sales\V1'], function() use ($router) {

    $router->group(['middleware'=> 'jwt.auth' ], function () use ($router) {

        //get JOS by Employee Id
        $router->get('josbyemployee', ['as' => 'sales.jos.v1.josbyemployee',
            'uses' => 'HeroJosController@getJosByEmployeeId']);

        //get JOS Man Power Detail by Jos Id
        $router->get('clientjmpdbyjosid', ['as' => 'sales.jmpd.v1.clientjmpdbyjosid',
            'uses' => 'JosManPowerController@clientGetJosMPDByJosId']);

    });
});

//Master
$router->group(['prefix' => 'master/v1', 'namespace' => 'Master\V1'], function() use ($router) {

    $router->group(['middleware'=> 'jwt.auth' ], function () use ($router) {
        $router->get('referensicleaning', ['as' => 'master.v1.referensi.cleaning', 'uses' => 'ReferensiController@getReferensiCleaning']);

        $router->get('referensihelptopic', ['as' => 'master.v1.referensi.helptopic', 'uses' => 'ReferensiController@getReferensiHelpTopic']);


    });

});

$router->group(['prefix' => 'foo', 'namespace' => 'Foo\V1'], function() use ($router) {

    $router->get('foo', ['as' => 'foo.bar', 'uses' => 'FooController@index']);

    $router->get('test_email' ,['as' => 'foo.mail', 'uses' => 'TestMailController@mail']);

    $router->get('ticketno', ['as' => 'foo.ticketno', 'uses' => 'FooController@genTicketNumber']);

});
