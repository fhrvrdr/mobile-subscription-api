<?php

declare(strict_types=1);

namespace App\Services\Payment\Factory;

use App\DTO\Request\IosPaymentRequest;
use App\DTO\Response\PurchaseResponse;
use App\DTO\Response\VerificationResponse;
use App\Exceptions\PaymentServiceException;
use App\Facade\ApplicationSettings;
use App\Services\Payment\Client\IosClient;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

final readonly class IosPayment implements PaymentGatewayInterface
{
    private IosClient $client;

    const string PROVIDER = 'ios';

    public function __construct()
    {
        $device = app('authDevice');
        $credentials = ApplicationSettings::getProviderCredentials($device->app_id, self::PROVIDER);

        $this->client = app(IosClient::class, ['credentials' => $credentials]);
    }

    public function pay(string $receipt): PurchaseResponse
    {
        try {
            $rawResponse = $this->sendRequest(new IosPaymentRequest($receipt, config('services.ios.purchase_url')));

            return $this->processPurchaseResponse($rawResponse);
        } catch (GuzzleException $e) {
            Log::channel('payment')->error($e->getMessage(), [
                'data' => [
                    'receipt' => $receipt,
                    'device' => app('authDevice')->id,
                    'trace' => $e->getTraceAsString(),
                ],
            ]);

            throw new PaymentServiceException('An error occurred while processing the payment');
        }
    }

    public function checkPaymentStatus(string $receipt): VerificationResponse
    {
        try {
            $rawResponse = $this->sendRequest(new IosPaymentRequest($receipt, config('services.ios.verify_url')));

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
            Log::channel('payment')->error('IosPayment - processVerificationRequest response is failed', [
                'data' => [
                    'device' => app('authDevice')->id,
                    'status_code' => $response->getStatusCode(),
                    'response' => $rawResponse,
                ],
            ]);

            throw new PaymentServiceException('An error occurred while processing the payment');
        }

        return new VerificationResponse(
            isExpired: $rawResponse['status'],
        );
    }

    private function processPurchaseResponse(ResponseInterface $response): PurchaseResponse
    {
        $rawResponse = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== 200) {
            Log::channel('payment')->error('IosPayment - processPurchaseRequest response is failed', [
                'data' => [
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
    private function sendRequest(IosPaymentRequest $request): ResponseInterface
    {
        return $this->client->post($request->url, $request->forRequest());
    }
}
