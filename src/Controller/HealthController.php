<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

use function in_array;

final readonly class HealthController
{
    public function __construct(
        private Connection $connection,
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/health', name: 'health', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $checks = ['database' => $this->checkDatabase()];
        $healthy = !in_array('fail', $checks, true);

        return new JsonResponse(
            ['status' => $healthy ? 'ok' : 'fail', 'checks' => $checks],
            $healthy ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE,
        );
    }

    /**
     * A real round-trip, not just "the process is up": catches a wrong DATABASE_URL or a dead Postgres.
     */
    private function checkDatabase(): string
    {
        try {
            $this->connection->executeQuery('SELECT 1')->fetchOne();

            return 'ok';
        } catch (Throwable $e) {
            $this->logger->error('Health check: database unreachable', ['exception' => $e]);

            return 'fail';
        }
    }
}
