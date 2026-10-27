<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable/Disable MongoDB Query Commenter
    |--------------------------------------------------------------------------
    |
    | When set to true, Eloquent queries executed by MongoDB models will
    | automatically have SQLCommenter-style metadata attached to query options.
    |
    */
    'enabled' => env('MONGO_COMMENTER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Custom Application Name (Optional)
    |--------------------------------------------------------------------------
    |
    | If null, it will default to config('app.name').
    |
    */
    'app_name' => env('MONGO_COMMENTER_APP_NAME', null),

    /*
    |--------------------------------------------------------------------------
    | Included Comment Metadata Tags
    |--------------------------------------------------------------------------
    |
    | Configure which contextual tags should be included in the comment string.
    |
    */
    'include_app_name' => env('MONGO_COMMENTER_INCLUDE_APP_NAME', true),
    'include_controller' => env('MONGO_COMMENTER_CONTROLLER', true),
    'include_route' => env('MONGO_COMMENTER_ROUTE', true),
    'include_user' => env('MONGO_COMMENTER_USER', true),
    'include_command' => env('MONGO_COMMENTER_COMMAND', true),
];
