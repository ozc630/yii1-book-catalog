<?php

declare(strict_types=1);

final class AuthorSubscriptionTest extends IntegrationTestCase
{
    public function testPhoneValidationRejectsInvalidPhone(): void
    {
        $author = $this->createAuthor();

        $subscription = new AuthorSubscription();
        $subscription->author_id = (int) $author->id;
        $subscription->phone = '12345';

        self::assertFalse($subscription->validate());
        self::assertTrue($subscription->hasErrors('phone'));
    }

    public function testDuplicateSubscriptionIsRejectedByUniqueConstraint(): void
    {
        $author = $this->createAuthor();
        $phone = $this->generatePhone();

        $this->createSubscription((int) $author->id, $phone);

        $duplicate = new AuthorSubscription();
        $duplicate->author_id = (int) $author->id;
        $duplicate->phone = $phone;

        $this->expectException(CDbException::class);
        $duplicate->save();
    }
}
