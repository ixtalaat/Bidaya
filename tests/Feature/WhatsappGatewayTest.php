<?php

namespace Tests\Feature;

use App\Contracts\WhatsAppGateway;
use App\Services\MetaWhatsAppGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsappGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_text_messages_through_the_meta_api(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.test123']]], 200),
        ]);

        $gateway = new MetaWhatsAppGateway('12345', 'test-token', true);

        $result = $gateway->send('201001234567', 'Hello from Bidaya.');

        $this->assertTrue($result->sent);
        $this->assertSame('wamid.test123', $result->providerMessageId);

        Http::assertSent(fn ($request): bool => $request->url() === 'https://graph.facebook.com/v22.0/12345/messages'
            && $request['to'] === '201001234567'
            && $request['text']['body'] === 'Hello from Bidaya.'
            && $request->header('Authorization') === ['Bearer test-token']);
    }

    public function test_api_errors_are_reported_without_exceptions(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(['error' => ['message' => 'Invalid phone number.']], 400),
        ]);

        $gateway = new MetaWhatsAppGateway('12345', 'test-token', true);

        $result = $gateway->send('201001234567', 'Hello.');

        $this->assertFalse($result->sent);
        $this->assertSame('Invalid phone number.', $result->error);
    }

    public function test_disabled_gateway_never_calls_the_api(): void
    {
        Http::fake();

        $gateway = new MetaWhatsAppGateway('', '', false);

        $this->assertFalse($gateway->enabled());

        $result = $gateway->send('201001234567', 'Hello.');

        $this->assertFalse($result->sent);
        Http::assertNothingSent();
    }

    public function test_container_resolves_the_configured_gateway(): void
    {
        $gateway = app(WhatsAppGateway::class);

        $this->assertInstanceOf(MetaWhatsAppGateway::class, $gateway);
        $this->assertFalse($gateway->enabled());
    }
}
