<?php

declare(strict_types=1);

namespace App\Action\Deposit;

use App\Action\ActionBase;
use App\Domain\Deposit\DepositRepository;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

class DeleteDepositAction extends ActionBase
{
    private DepositRepository $depositRepo;

    public function __construct(PhpRenderer $renderer, FlashMessage $flash, DepositRepository $depositRepo)
    {
        parent::__construct($renderer, $flash);
        $this->depositRepo = $depositRepo;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args = []
    ): ResponseInterface {
        $queryParams = $request->getQueryParams();

        $depIndex = (int)($args['index'] ?? $queryParams['dep_index'] ?? 1);
        $id = (int)($args['id'] ?? $queryParams['id'] ?? 0);

        if ($depIndex >= 1 && $depIndex <= 8 && $id > 0) {
            $success = $this->depositRepo->delete($depIndex, $id);
            if ($success) {
                $this->flash->add('success', "Data Deposito {$depIndex} berhasil dihapus!");
            } else {
                $this->flash->add('danger', 'Gagal menghapus data Deposito.');
            }
        } else {
            $this->flash->add('warning', 'Invalid parameters for deposit deletion.');
        }

        return $this->redirect($response, './?tab=3');
    }
}
