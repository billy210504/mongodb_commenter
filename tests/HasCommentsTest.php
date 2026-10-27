<?php

use Billy\MongodbCommenter\Traits\HasComments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class DummyCommentModel
{
    use HasComments;
}

it('resolves app_name in comment', function () {
    config()->set('app.name', 'CommenterTestApp');
    config()->set('mongodb-commenter.include_app_name', true);
    config()->set('mongodb-commenter.include_command', false);

    $comment = DummyCommentModel::resolveMongoComment();

    expect($comment)->toBe("app_name='CommenterTestApp'");
});

it('uses custom app_name when specified in config', function () {
    config()->set('app.name', 'DefaultApp');
    config()->set('mongodb-commenter.app_name', 'CustomMongoApp');
    config()->set('mongodb-commenter.include_app_name', true);
    config()->set('mongodb-commenter.include_command', false);

    $comment = DummyCommentModel::resolveMongoComment();

    expect($comment)->toBe("app_name='CustomMongoApp'");
});

it('resolves route and controller metadata on web routes', function () {
    config()->set('app.name', 'CommenterTestApp');
    config()->set('mongodb-commenter.include_app_name', true);
    config()->set('mongodb-commenter.include_controller', true);
    config()->set('mongodb-commenter.include_route', true);

    Route::get('/users/{id}', 'App\Http\Controllers\UserController@show')->name('users.show');

    $request = Request::create('/users/1', 'GET');
    $route = Route::getRoutes()->match($request);
    Route::shouldReceive('current')->andReturn($route);

    $comment = DummyCommentModel::resolveMongoComment();

    expect($comment)->toContain("action='show'");
    expect($comment)->toContain("controller='UserController'");
    expect($comment)->toContain("route='users.show'");
    expect($comment)->toContain("app_name='CommenterTestApp'");
});
