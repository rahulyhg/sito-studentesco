<?php

$app->group('/teachers', function () use ($app) {
    $app->get('/{id:[0-9]+}', 'App\Controllers\TeacherController:datail')->setName('teacher');

    $app->get('/edit/{id:[0-9]+}', 'App\Controllers\TeacherController:form')->setName('edit-teacher');
    $app->post('/edit/{id:[0-9]+}', 'App\Controllers\TeacherController:formPost');

    $app->group('', function () use ($app) {
        $app->get('', 'App\Controllers\TeacherController:index')->setName('teachers');

        $app->get('/new', 'App\Controllers\TeacherController:form')->setName('new-teacher');
        $app->post('/new', 'App\Controllers\TeacherController:formPost');

        $app->get('/delete/{id:[0-9]+}', 'App\Controllers\TeacherController:delete')->setName('delete-teacher');
        $app->post('/delete/{id:[0-9]+}', 'App\Controllers\TeacherController:deletePost');
    })->add('App\Middlewares\Authorization\AdminMiddleware');
})->add('App\Middlewares\Authorization\UserMiddleware');
