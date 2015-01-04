<?php
function controller($shortName)
{
    list($shortClass, $shortMethod) = explode('/', $shortName, 2);
    return sprintf('%sController::%sAction', ucfirst($shortClass), $shortMethod);
}

// Routes
$app->get('/', controller('auth/index'));
$app->get('/login', controller('auth/login'));
$app->get('/logout', controller('auth/logout'));
$app->get('/auth/{provider}', controller('auth/auth'));
$app->get('getMyApps', controller('auth/get_my_apps'))->before($authorize);
$app->get('/userDetails/{appName}', controller('auth/user_details'))->before($authorize);;

// Apps
$app->get('/appList', controller('app/index'));
$app->get('/api/appList', controller('app/app_list'))->before($authorize);
$app->post('/api/appList', controller('app/app_create'))->before($authorize);
$app->delete('/api/appList', controller('app/app_delete'))->before($authorize);

// Users
$app->get('/users', controller('users/index'));
$app->get('api/usersList', controller('users/users_list'))->before($authorize);
$app->get('api/appList', controller('users/app_list'))->before($authorize);
$app->get('api/appPermissions/{appName}/{id}', controller('users/get_permissions'))->before($authorize);
$app->post('api/appPermissions/{appName}/{id}', controller('users/set_permissions'))->before($authorize);

// Quotes
$app->get('/quotes-app', controller('category/index'));
$app->get('api/category', controller('category/read'))->before($authorize);
$app->post('api/category', controller('category/create'))->before($authorize);
$app->put('api/category/{id}', controller('category/update'))->before($authorize);
$app->delete('api/category/{id}', controller('category/delete'))->before($authorize);
$app->get('api/quote/{ctg}', controller('quote/read'))->before($authorize);
$app->post('api/quote', controller('quote/create'))->before($authorize);
$app->put('api/quote/{id}', controller('quote/update'))->before($authorize);
$app->delete('api/quote/{id}', controller('quote/delete'))->before($authorize);