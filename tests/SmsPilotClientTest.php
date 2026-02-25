<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Middleware;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

final class SmsPilotClientTest extends IntegrationTestCase
{
    public function testSendHandlesStructuredTopLevelError(): void
    {
        $payload = json_encode([
            'status' => 1,
            'error' => [
                'code' => 42,
                'message' => 'Invalid destination',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $client = new SmsPilotClient('emulator', 'TEST', $this->mockHttpClient(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $payload
        )));

        $result = $client->send('+79990000000', 'hello');

        self::assertFalse($result->success);
        self::assertIsString($result->error);
        self::assertStringContainsString('code', (string) $result->error);
        self::assertStringContainsString('Invalid destination', (string) $result->error);
    }

    public function testSendHandlesInvalidSendStatusPayload(): void
    {
        $payload = json_encode([
            'status' => 0,
            'send' => [
                [
                    'status' => ['bad'],
                    'error' => '',
                ],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $client = new SmsPilotClient('emulator', 'TEST', $this->mockHttpClient(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $payload
        )));

        $result = $client->send('+79990000001', 'hello');

        self::assertFalse($result->success);
        self::assertSame('SMSPilot returned non-zero send[0] status: ["bad"]', $result->error);
    }

    public function testSendUsesExpectedSmsPilotQueryFormat(): void
    {
        $history = [];
        $handlerStack = HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"status":0}'),
        ]));
        $handlerStack->push(Middleware::history($history));

        $httpClient = new Client(['handler' => $handlerStack]);
        $client = new SmsPilotClient('emulator', 'INFORM', $httpClient);

        $result = $client->send('+79991234567', 'hello sms');

        self::assertTrue($result->success);
        self::assertCount(1, $history);

        $request = $history[0]['request'];
        parse_str($request->getUri()->getQuery(), $query);

        self::assertSame('hello sms', $query['send'] ?? null);
        self::assertSame('+79991234567', $query['to'] ?? null);
        self::assertArrayNotHasKey('text', $query);
    }

    private function mockHttpClient(Response ...$responses): ClientInterface
    {
        $handler = HandlerStack::create(new MockHandler($responses));
        return new Client(['handler' => $handler]);
    }
}
