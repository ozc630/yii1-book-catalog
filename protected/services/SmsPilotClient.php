<?php

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class SmsPilotClient
{
    private $apiKey;
    private $sender;
    private $endpoint = 'https://smspilot.ru/api.php';
    private $httpClient;

    public function __construct(string $apiKey, string $sender, ?ClientInterface $httpClient = null)
    {
        $this->apiKey = trim((string) $apiKey);
        $this->sender = trim((string) $sender);
        $this->httpClient = $httpClient ?: new Client([
            'timeout' => 5,
            'connect_timeout' => 5,
            'http_errors' => false,
        ]);
    }

    public function send(string $phone, string $message): SmsResult
    {
        if ($this->apiKey === '') {
            return new SmsResult(false, null, 'SMSPilot API key is not configured');
        }

        try {
            $query = [
                'send' => $message,
                'to' => $phone,
                'apikey' => $this->apiKey,
                'format' => 'json',
            ];

            if ($this->sender !== '') {
                $query['from'] = $this->sender;
            }

            $response = $this->httpClient->request('GET', $this->endpoint, [
                'query' => $query,
            ]);
        } catch (GuzzleException $e) {
            return new SmsResult(false, null, $e->getMessage());
        }

        $statusCode = (int) $response->getStatusCode();
        $rawResponse = (string) $response->getBody();

        if ($statusCode >= 400) {
            return new SmsResult(false, $rawResponse, sprintf('SMSPilot HTTP error: %d', $statusCode));
        }

        $decoded = json_decode($rawResponse, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            return new SmsResult(false, $rawResponse, 'Invalid JSON response from SMSPilot');
        }

        if (array_key_exists('error', $decoded) && $this->hasErrorValue($decoded['error'])) {
            return new SmsResult(
                false,
                $decoded,
                $this->normalizeErrorMessage($decoded['error'], 'SMSPilot returned an error')
            );
        }

        if (isset($decoded['status']) && (int) $decoded['status'] !== 0) {
            return new SmsResult(false, $decoded, 'SMSPilot returned non-zero status');
        }

        $sendStatusError = $this->resolveSendStatusError($decoded);
        if ($sendStatusError !== null) {
            return new SmsResult(false, $decoded, $sendStatusError);
        }

        return new SmsResult(true, $decoded, null);
    }

    private function resolveSendStatusError(array $decoded): ?string
    {
        if (!isset($decoded['send'])) {
            return null;
        }

        if (!is_array($decoded['send'])) {
            return 'SMSPilot returned invalid send payload';
        }

        foreach ($decoded['send'] as $key => $sendRow) {
            if (!is_array($sendRow)) {
                continue;
            }

            $rawStatus = $sendRow['status'] ?? 0;
            $statusCode = (is_scalar($rawStatus) && is_numeric((string) $rawStatus))
                ? (int) $rawStatus
                : null;
            $statusText = $this->normalizeErrorMessage($rawStatus, 'unknown');
            $error = '';

            if (array_key_exists('error', $sendRow) && $this->hasErrorValue($sendRow['error'])) {
                $error = $this->normalizeErrorMessage($sendRow['error'], 'Unknown send error');
            }

            if ($statusCode !== 0 || $error !== '') {
                if ($error !== '') {
                    return sprintf('SMSPilot send[%s] error: %s', (string) $key, $error);
                }

                return sprintf('SMSPilot returned non-zero send[%s] status: %s', (string) $key, $statusText);
            }
        }

        return null;
    }

    private function hasErrorValue($value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (is_array($value)) {
            return !empty($value);
        }

        return true;
    }

    private function normalizeErrorMessage($value, string $fallback): string
    {
        if ($value === null) {
            return $fallback;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            return $trimmed !== '' ? $trimmed : $fallback;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (is_string($encoded) && $encoded !== '') {
            return $encoded;
        }

        return $fallback;
    }
}
