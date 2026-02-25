<?php

class SubscriptionNotifier
{
    private $smsPilotClient;

    public function __construct($smsPilotClient = null)
    {
        if ($smsPilotClient !== null) {
            $this->smsPilotClient = $smsPilotClient;
            return;
        }

        $this->smsPilotClient = new SmsPilotClient(
            Yii::app()->params['smsPilotApiKey'],
            Yii::app()->params['smsPilotSender']
        );
    }

    public function notifyNewBook(Book $book, array $authorIds): array
    {
        $authorIds = $this->normalizeAuthorIds($authorIds);
        if (empty($authorIds)) {
            return [];
        }

        $criteria = new CDbCriteria();
        $criteria->select = 'DISTINCT phone';
        $criteria->addInCondition('author_id', $authorIds);

        $subscriptions = AuthorSubscription::model()->findAll($criteria);
        if (empty($subscriptions)) {
            return [];
        }

        $authorNames = [];
        foreach ($book->authors as $author) {
            $authorNames[] = $author->name;
        }

        $authorsText = empty($authorNames) ? 'selected authors' : implode(', ', $authorNames);
        $message = sprintf(
            'New book: "%s" (%d). Authors: %s.',
            $book->title,
            (int) $book->published_year,
            $authorsText
        );

        $results = [];
        foreach ($subscriptions as $subscription) {
            $result = $this->smsPilotClient->send($subscription->phone, $message);
            $results[] = [
                'phone' => $subscription->phone,
                'success' => $result->success,
                'error' => $result->error,
            ];

            $this->logSmsResponse($subscription->phone, $result);
        }

        return $results;
    }

    private function normalizeAuthorIds(array $authorIds): array
    {
        $normalized = [];
        foreach ($authorIds as $authorId) {
            $authorId = (int) $authorId;
            if ($authorId > 0) {
                $normalized[] = $authorId;
            }
        }

        return array_values(array_unique($normalized));
    }

    private function logSmsResponse(string $phone, SmsResult $result): void
    {
        $level = $result->success ? CLogger::LEVEL_INFO : CLogger::LEVEL_WARNING;
        $payload = $this->encodeLogPayload($result->response);
        $error = $result->error !== null ? $result->error : '';

        Yii::log(
            sprintf(
                'SMSPilot response phone=%s success=%s error=%s payload=%s',
                $this->maskPhone($phone),
                $result->success ? 'true' : 'false',
                $error,
                $payload
            ),
            $level,
            'sms'
        );
    }

    private function encodeLogPayload($payload): string
    {
        if ($payload === null) {
            return 'null';
        }

        if (is_scalar($payload)) {
            return (string) $payload;
        }

        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded !== false) {
            return $encoded;
        }

        return '[unserializable payload]';
    }

    private function maskPhone(string $phone): string
    {
        $digitsOnly = preg_replace('/\D+/', '', $phone);
        if (!is_string($digitsOnly) || strlen($digitsOnly) < 4) {
            return '***';
        }

        return str_repeat('*', max(0, strlen($digitsOnly) - 4)) . substr($digitsOnly, -4);
    }
}
