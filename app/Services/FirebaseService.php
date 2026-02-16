<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\Messaging; // T

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('services.firebase.credentials'));

        $this->messaging = $factory->createMessaging();
    }

    // public function sendToToken(
    //     string $token,
    //     string $title,
    //     string $body,
    //     array $data = []
    // ) {
    //     $message = CloudMessage::withTarget('token', $token)
    //         ->withNotification([
    //             'title' => $title,
    //             'body'  => $body,
    //         ])
    //         ->withData($data);

    //     return $this->messaging->send($message);
    // }
   public function sendToTokens(
        array $tokens,
        string $title,
        string $body,
        array $data = []
    ): MulticastSendReport {

        $payload = array_merge($data, [
            'title' => $title,
            'body'  => $body,
        ]);

        $message = CloudMessage::new()
            ->withData($payload); // ✅ HANYA DATA

        return $this->messaging->sendMulticast($message, $tokens);
    }


    public function sendNotification(
        string $token,
        string $title,
        string $body,
        array $data = []
    ): void {
        $message = CloudMessage::fromArray([
            'token'        => $token,
            // 'notification' => [
            //     'title' => $title,
            //     'body'  => $body,
            // ],
            'data'         => $data,
        ]);

        $this->messaging->send($message);
    }
}
