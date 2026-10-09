<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HealthControllerTest extends WebTestCase
{
    public function testReportsOkWhenDatabaseIsReachable(): void
    {
        $client = static::createClient();

        $client->request('GET', '/health');

        self::assertResponseIsSuccessful();
        self::assertJsonStringEqualsJsonString(
            '{"status":"ok","checks":{"database":"ok"}}',
            (string) $client->getResponse()->getContent(),
        );
    }

    public function testReportsUnavailableWhenDatabaseIsDown(): void
    {
        $client = static::createClient();
        $connection = $this->createStub(Connection::class);
        $connection->method('executeQuery')->willThrowException(new \RuntimeException('Connection refused'));
        static::getContainer()->set('doctrine.dbal.default_connection', $connection);

        $client->request('GET', '/health');

        self::assertResponseStatusCodeSame(503);
        self::assertJsonStringEqualsJsonString(
            '{"status":"fail","checks":{"database":"fail"}}',
            (string) $client->getResponse()->getContent(),
        );
    }

    public function testRejectsNonGetMethods(): void
    {
        $client = static::createClient();

        $client->request('POST', '/health');

        self::assertResponseStatusCodeSame(405);
    }
}
