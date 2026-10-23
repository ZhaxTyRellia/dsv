<?php

declare(strict_types=1);

use App\Action\Dashboard\DashboardAction;
use App\Action\Dashboard\ResetDatabaseAction;
use App\Action\Dashboard\SetOptionAction;
use App\Action\Dashboard\UpdateIdentityAction;
use App\Action\Dashboard\UpdateTitlesAction;
use App\Action\Deposit\CreateDepositAction;
use App\Action\Deposit\DeleteDepositAction;
use App\Action\Deposit\UpdateDepositAction;
use App\Action\License\ActivateLicenseAction;
use App\Action\License\LicenseAction;
use App\Action\Signage\GetIdentityApiAction;
use App\Action\Signage\ShowSignageAction;
use App\Action\Theme\GetThemeApiAction;
use App\Action\Theme\UpdateThemeAction;
use App\Action\Valas\CreateValasAction;
use App\Action\Valas\DeleteValasAction;
use App\Action\Valas\UpdateValasAction;
use App\Action\Video\DeleteVideoAction;
use App\Action\Video\UploadVideoAction;
use Slim\App;

return function (App $app) {
    // 1. Dashboard View
    $app->get('/', DashboardAction::class)->setName('dashboard');

    // 2. Titles Management Action
    $app->post('/titles', UpdateTitlesAction::class)->setName('titles.update');

    // 3. Valas Management Actions
    $app->post('/valas', CreateValasAction::class)->setName('valas.create');
    $app->post('/valas/edit', UpdateValasAction::class)->setName('valas.edit');
    $app->get('/valas/delete/{id}', DeleteValasAction::class)->setName('valas.delete');

    // 4. Deposit Management Actions
    $app->post('/deposit', CreateDepositAction::class)->setName('deposit.create');
    $app->post('/deposit/edit', UpdateDepositAction::class)->setName('deposit.edit');
    $app->get('/deposit/delete/{index}/{id}', DeleteDepositAction::class)->setName('deposit.delete');

    // 5. Themes Management Action
    $app->post('/themes', UpdateThemeAction::class)->setName('themes.update');

    // 6. Video Management Actions
    $app->post('/videos', UploadVideoAction::class)->setName('videos.upload');
    $app->get('/videos/delete/{id}', DeleteVideoAction::class)->setName('videos.delete');

    // 7. Identity, Option & Database Settings Actions
    $app->post('/identity', UpdateIdentityAction::class)->setName('identity.update');
    $app->post('/option', SetOptionAction::class)->setName('option.update');
    $app->post('/database/reset', ResetDatabaseAction::class)->setName('database.reset');

    // 8. Signage Display Screen
    $app->get('/show', ShowSignageAction::class)->setName('signage.show');

    // 9. License Activation
    $app->get('/license', LicenseAction::class)->setName('license.view');
    $app->post('/license/activate', ActivateLicenseAction::class)->setName('license.activate');

    // 10. REST / AJAX API Endpoints
    $app->get('/api/identity', GetIdentityApiAction::class)->setName('api.identity');
    $app->get('/api/themes', GetThemeApiAction::class)->setName('api.themes');
};
