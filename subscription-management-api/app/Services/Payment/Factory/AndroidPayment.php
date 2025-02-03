<?php

declare(strict_types=1);

namespace App\Services\Payment\Factory;

use App\DTO\Request\GooglePaymentRequest;
use App\DTO\Response\PurchaseResponse;
use App\DTO\Response\VerificationResponse;
use App\Exceptions\PaymentServiceException;
use App\Facade\ApplicationSettings;
use App\Services\Payment\Client\GoogleClient;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

final readonly class AndroidPayment implements PaymentGatewayInterface
{
    private GoogleClient $client;

    const string PROVIDER = 'google';

    public function __construct()
    {
        $device = app('authDevice');
        $credentials = ApplicationSettings::getProviderCredentials($device->app_id, self::PROVIDER);

        $this->client = app(GoogleClient::class, ['credentials' => $credentials]);
    }

    public function pay(string $receipt): PurchaseResponse
    {
        try {
            $rawResponse = $this->sendRequest(new GooglePaymentRequest($receipt, config('services.google.purchase_url')));

            return $this->processPurchaseRequest($rawResponse);
        } catch (GuzzleException $e) {
            Log::channel('payment')->error($e->getMessage(), [
                'data' => [
                    'receipt' => $receipt,
                    'device' => app('authDevice')->id,
                    'trace' => $e->getTraceAsString(),
                ],
            ]);

            throw new \Exception('An error occurred while processing the payment');
        }
    }

    public function checkPaymentStatus(string $receipt): VerificationResponse
    {
        try {
            $rawResponse = $this->sendRequest(new GooglePaymentRequest($receipt, config('services.google.verify_url')));

            return $this->processVerificationResponse($rawResponse);
        } catch (GuzzleException $e) {
            Log::channel('payment')->error($e->getMessage(), [
                'data' => [
                    'receipt' => $receipt,
                    'device' => app('authDevice')->id,
                    'trace' => $e->getTraceAsString(),
                ],

            ]);

            throw new PaymentServiceException('An error occurred while checking the payment status');
        }
    }

    private function processVerificationResponse(ResponseInterface $response): VerificationResponse
    {
        $rawResponse = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== 200) {
            Log::channel('payment')->error('AndroidPayment - processVerificationResponse response is failed', [
                'data' => [
                    'receipt' => $rawResponse['receipt'],
                    'device' => app('authDevice')->id,
                ],
            ]);

            throw new PaymentServiceException('An error occurred while checking the payment status');
        }

        return new VerificationResponse(
            isExpired: $rawResponse['status'],
        );
    }

    private function processPurchaseRequest(ResponseInterface $response): PurchaseResponse
    {
        $rawResponse = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== 200) {
            Log::channel('payment')->error('AndroidPayment - processPurchaseRequest response is failed', [
                'data' => [
                    'receipt' => $rawResponse['receipt'],
                    'device' => app('authDevice')->id,
                    'status_code' => $response->getStatusCode(),
                    'response' => $rawResponse,
                ],
            ]);

            throw new PaymentServiceException('An error occurred while processing the payment');
        }

        return new PurchaseResponse(
            status: $rawResponse['status'],
            expireDate: $rawResponse['expire_date'],
        );
    }

    /**
     * @throws GuzzleException
     */
    private function sendRequest(GooglePaymentRequest $googlePaymentRequest): ResponseInterface
    {
        return $this->client->post($googlePaymentRequest->url, $googlePaymentRequest->forRequest());
    }
}
