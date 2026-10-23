<?php

declare(strict_types=1);

use App\Domain\Deposit\DepositRepository;
use App\Domain\Identity\IdentityRepository;
use App\Domain\License\LicenseService;
use App\Domain\Title\TitleRepository;
use App\Domain\Valas\ValasRepository;
use App\Domain\Video\VideoRepository;
use App\Support\FileUploader;
use App\Support\FlashMessage;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Views\PhpRenderer;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },

    PDO::class => function (ContainerInterface $c) {
        return $c->get(LicenseService::class)->createDatabaseConnection($c->get('settings')['db']);
    },

    ResponseFactoryInterface::class => function () {
        return new ResponseFactory();
    },

    PhpRenderer::class => function (ContainerInterface $c) {
        $templatesPath = $c->get('settings')['paths']['templates'];
        return new PhpRenderer($templatesPath);
    },

    LicenseService::class => function () {
        return new LicenseService();
    },

    IdentityRepository::class => function (ContainerInterface $c) {
        return new IdentityRepository($c->get(PDO::class));
    },

    TitleRepository::class => function (ContainerInterface $c) {
        return new TitleRepository($c->get(PDO::class));
    },

    ValasRepository::class => function (ContainerInterface $c) {
        return new ValasRepository($c->get(PDO::class));
    },

    DepositRepository::class => function (ContainerInterface $c) {
        return new DepositRepository($c->get(PDO::class));
    },

    VideoRepository::class => function (ContainerInterface $c) {
        return new VideoRepository($c->get(PDO::class));
    },

    FlashMessage::class => function () {
        return new FlashMessage();
    },

    FileUploader::class => function () {
        return new FileUploader();
    },

    App\Action\Dashboard\UpdateIdentityAction::class => function (ContainerInterface $c) {
        return new App\Action\Dashboard\UpdateIdentityAction(
            $c->get(PhpRenderer::class),
            $c->get(FlashMessage::class),
            $c->get(RouteParserInterface::class),
            $c->get(IdentityRepository::class),
            $c->get(FileUploader::class),
            $c->get('settings')
        );
    },

    App\Action\Video\UploadVideoAction::class => function (ContainerInterface $c) {
        return new App\Action\Video\UploadVideoAction(
            $c->get(PhpRenderer::class),
            $c->get(FlashMessage::class),
            $c->get(RouteParserInterface::class),
            $c->get(VideoRepository::class),
            $c->get(FileUploader::class),
            $c->get('settings')
        );
    },

    App\Action\Video\DeleteVideoAction::class => function (ContainerInterface $c) {
        return new App\Action\Video\DeleteVideoAction(
            $c->get(PhpRenderer::class),
            $c->get(FlashMessage::class),
            $c->get(RouteParserInterface::class),
            $c->get(VideoRepository::class),
            $c->get('settings')
        );
    },
]);

return $containerBuilder->build();
