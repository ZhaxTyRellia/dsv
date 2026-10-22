<?php

declare(strict_types=1);

namespace App\Action\Deposit;

use App\Action\ActionBase;
use App\Domain\Deposit\DepositRepository;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

class UpdateDepositAction extends ActionBase
{
    private DepositRepository $depositRepo;

    public function __construct(PhpRenderer $renderer, FlashMessage $flash, DepositRepository $depositRepo)
    {
        parent::__construct($renderer, $flash);
        $this->depositRepo = $depositRepo;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = (array)($request->getParsedBody() ?? []);

        $depIndex = (int)($body['deposit_index'] ?? 1);
        if ($depIndex < 1 || $depIndex > 8) {
            $depIndex = 1;
        }

        $id = (int)($body['deposit_id'] ?? 0);
        $tenor = trim((string)($body['tenor'] ?? ''));
        $interestRate = (float)($body['interest_rate'] ?? 0);
        $minAmount = (float)($body['min_amount'] ?? 0);
        $description = trim((string)($body['description'] ?? ''));

        if ($id <= 0 || empty($tenor)) {
            $this->flash->add('warning', 'Invalid deposit data.');
            return $this->redirect($response, './?tab=3');
        }

        $success = $this->depositRepo->update($depIndex, $id, $tenor, $interestRate, $minAmount, $description);

        if ($success) {
            $this->flash->add('success', "Data Deposito {$depIndex} berhasil diperbarui!");
        } else {
            $this->flash->add('danger', 'Gagal memperbarui data Deposito.');
        }

        return $this->redirect($response, './?tab=3');
    }
}
