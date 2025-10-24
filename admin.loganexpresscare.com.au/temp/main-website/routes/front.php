<?php

use Examyou\RestAPI\Facades\ApiRoute;

ApiRoute::group(['namespace' => 'App\Http\Controllers\Api'], function () {
    ApiRoute::post('front-application-form', ['as' => 'api.extra.front-application-form', 'uses' => 'FrontController@frontApplicationForm']);
});
