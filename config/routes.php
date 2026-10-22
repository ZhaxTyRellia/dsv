<?php

declare(strict_types=1);

use App\Action\Dashboard\DashboardAction;
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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

return function (App $app) {
    // 1. Dashboard Main View
    $app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
        $queryParams = $request->getQueryParams();
        $action = $queryParams['action'] ?? null;

        // Backward compatibility for legacy query actions on root URL
        if ($action === 'delete') {
            return $app->getContainer()->get(DeleteValasAction::class)($request, $response);
        }
        if ($action === 'edit') {
            return $app->getContainer()->get(UpdateValasAction::class)($request, $response);
        }
        if ($action === 'delete_deposit') {
            return $app->getContainer()->get(DeleteDepositAction::class)($request, $response);
        }
        if ($action === 'delete_video') {
            return $app->getContainer()->get(DeleteVideoAction::class)($request, $response);
        }

        return $app->getContainer()->get(DashboardAction::class)($request, $response);
    })->setName('dashboard');

    // Handle legacy form submissions directly posted to /
    $app->post('/', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
        $body = (array)($request->getParsedBody() ?? []);

        if (isset($body['update_titles'])) {
            return $app->getContainer()->get(UpdateTitlesAction::class)($request, $response);
        }
        if (isset($body['submit_kurs'])) {
            return $app->getContainer()->get(CreateValasAction::class)($request, $response);
        }
        if (isset($body['submit_deposit'])) {
            return $app->getContainer()->get(CreateDepositAction::class)($request, $response);
        }
        if (isset($body['update_deposit_item'])) {
            return $app->getContainer()->get(UpdateDepositAction::class)($request, $response);
        }
        if (isset($body['submit_video'])) {
            return $app->getContainer()->get(UploadVideoAction::class)($request, $response);
        }
        if (isset($body['save_theme_setting'])) {
            return $app->getContainer()->get(UpdateThemeAction::class)($request, $response);
        }
        if (isset($body['change_identity_btn'])) {
            return $app->getContainer()->get(UpdateIdentityAction::class)($request, $response);
        }
        if (isset($body['set_identity_option'])) {
            return $app->getContainer()->get(SetOptionAction::class)($request, $response);
        }
        if (isset($body['activate_license'])) {
            return $app->getContainer()->get(ActivateLicenseAction::class)($request, $response);
        }

        return $app->getContainer()->get(DashboardAction::class)($request, $response);
    });

    $app->get('/dashboard', DashboardAction::class)->setName('dashboard.alias');
    $app->get('/index.php', function (ServerRequestInterface $request, ResponseInterface $response) {
        return $response->withHeader('Location', './')->withStatus(302);
    });

    // 2. Titles Management
    $app->post('/titles', UpdateTitlesAction::class)->setName('titles.update');

    // 3. Valas Management
    $app->post('/valas', CreateValasAction::class)->setName('valas.create');
    $app->post('/valas/edit', UpdateValasAction::class)->setName('valas.edit');
    $app->get('/valas/edit', UpdateValasAction::class);
    $app->get('/valas/delete/{id}', DeleteValasAction::class)->setName('valas.delete');
    $app->post('/valas/{id}/delete', DeleteValasAction::class);

    // 4. Deposit Management
    $app->post('/deposit', CreateDepositAction::class)->setName('deposit.create');
    $app->post('/deposit/edit', UpdateDepositAction::class)->setName('deposit.edit');
    $app->get('/deposit/delete/{index}/{id}', DeleteDepositAction::class)->setName('deposit.delete');
    $app->post('/deposit/{index}/{id}/delete', DeleteDepositAction::class);

    // 5. Themes Management
    $app->post('/themes', UpdateThemeAction::class)->setName('themes.update');

    // 6. Video Management
    $app->post('/videos', UploadVideoAction::class)->setName('videos.upload');
    $app->get('/videos/delete/{id}', DeleteVideoAction::class)->setName('videos.delete');
    $app->post('/videos/{id}/delete', DeleteVideoAction::class);

    // 7. Identity & Option Settings
    $app->post('/identity', UpdateIdentityAction::class)->setName('identity.update');
    $app->post('/option', SetOptionAction::class)->setName('option.update');

    // 8. Signage Display Screen
    $app->get('/show', ShowSignageAction::class)->setName('signage.show');
    $app->get('/show.php', ShowSignageAction::class);

    // 9. License Activation
    $app->get('/license', LicenseAction::class)->setName('license.view');
    $app->post('/license/activate', ActivateLicenseAction::class)->setName('license.activate');

    // 10. REST / AJAX APIs
    $app->get('/api/identity', GetIdentityApiAction::class)->setName('api.identity');
    $app->get('/app/control/get_identity.php', GetIdentityApiAction::class);

    $app->get('/api/themes', GetThemeApiAction::class)->setName('api.themes');
    $app->get('/app/control/get_themes.php', GetThemeApiAction::class);
};
