<?php

declare(strict_types=1);

namespace App\Action;

use App\Domain\Identity\IdentityRepository;
use App\Support\FlashMessage;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\PhpRenderer;

abstract class ActionBase
{
    private const PUBKEY_SHA256 = 'fdf64f76ac69ba333d91c616a4a443ce59d15301bdb4924330da4c6af3ea53cf';

    protected PhpRenderer $renderer;
    protected FlashMessage $flash;
    protected RouteParserInterface $routeParser;

    public function __construct(
        PhpRenderer $renderer,
        FlashMessage $flash,
        RouteParserInterface $routeParser
    ) {
        $this->renderer = $renderer;
        $this->flash = $flash;
        $this->routeParser = $routeParser;
    }

    protected function getRuntime(): IdentityRepository
    {
        $runtime = $this->renderer->getAttribute('__runtime');
        if (!($runtime instanceof IdentityRepository)) {
            throw new RuntimeException('Fatal System Error: Core runtime instance missing from execution context.');
        }

        return $runtime;
    }

    protected function verifyRuntimeGuard(): void
    {
        $runtime = $this->getRuntime();
        $proof = $this->renderer->getAttribute('__rt_proof');

        if (
            !is_array($proof)
            || empty($proof['seal']) || !is_string($proof['seal'])
            || empty($proof['nonce']) || !is_string($proof['nonce'])
            || empty($proof['device_id']) || !is_string($proof['device_id'])
            || empty($proof['key']) || !is_string($proof['key'])
        ) {
            throw new RuntimeException('Fatal System Error: Runtime authorization proof missing or middleware bypassed.');
        }

        $pubPem = str_replace("\r\n", "\n", trim($runtime->getPublicKeyPem()));
        if ($pubPem === '' || !hash_equals(self::PUBKEY_SHA256, hash('sha256', $pubPem))) {
            throw new RuntimeException('Fatal System Error: Master public key integrity verification failed.');
        }

        $liveId = (string)$runtime->getDeviceUUID();
        if ($liveId === '' || !hash_equals($liveId, $proof['device_id'])) {
            throw new RuntimeException('Fatal System Error: Live hardware device identity mismatch.');
        }

        $expectedSeal = hash_hmac(
            'sha256',
            $liveId . '::RUNTIME_SEAL::' . $proof['key'] . '::' . self::PUBKEY_SHA256,
            $proof['nonce']
        );
        if (!hash_equals($expectedSeal, $proof['seal'])) {
            throw new RuntimeException('Fatal System Error: Runtime authorization seal invalid.');
        }

        $trimmedKey = trim($proof['key']);
        if (preg_match('/^[A-Za-z0-9\-_]+$/', $trimmedKey) !== 1) {
            throw new RuntimeException('Fatal System Error: Invalid activation key encoding.');
        }

        $rem = strlen($trimmedKey) % 4;
        $padded = $rem !== 0 ? ($trimmedKey . str_repeat('=', 4 - $rem)) : $trimmedKey;
        $rawSig = base64_decode(strtr($padded, '-_', '+/'), true);

        if (
            $rawSig === false
            || strlen($rawSig) < 64
            || strlen($rawSig) > 128
            || !function_exists('openssl_verify')
            || @openssl_verify(strtoupper(trim($liveId)), $rawSig, $pubPem, OPENSSL_ALGO_SHA256) !== 1
        ) {
            throw new RuntimeException('Fatal System Error: Cryptographic ECDSA signature check failed.');
        }

        $runtime->assertRuntimeAuthorized();
    }

    protected function render(ResponseInterface $response, string $template, array $data = []): ResponseInterface
    {
        $isActivationGate = ($template === 'dashboard/index.php' && !empty($data['__activation_gate']));
        if (!$isActivationGate) {
            $this->verifyRuntimeGuard();
        }

        return $this->getRuntime()->renderView($this->renderer, $response, $template, $data);
    }

    protected function json(ResponseInterface $response, array $data, int $status = 200): ResponseInterface
    {
        $this->verifyRuntimeGuard();

        return $this->getRuntime()->sendJson($response, $data, $status);
    }

    protected function urlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        return $this->getRuntime()->routeUrl($this->routeParser, $routeName, $data, $queryParams);
    }

    protected function redirect(ResponseInterface $response, string $url, int $status = 302): ResponseInterface
    {
        return $response
            ->withHeader('Location', $url)
            ->withStatus($status);
    }

    protected function redirectToRoute(
        ResponseInterface $response,
        string $routeName,
        array $data = [],
        array $queryParams = [],
        int $status = 302
    ): ResponseInterface {
        $url = $this->urlFor($routeName, $data, $queryParams);
        return $this->redirect($response, $url, $status);
    }
}
