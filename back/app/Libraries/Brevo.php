<?php

namespace App\Libraries;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Brevo
{
    protected $client;
    protected $apiKey;
    protected $senderName;
    protected $senderEmail;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.brevo.api_key');
        $this->senderName = config('services.brevo.sender_name');
        $this->senderEmail = config('services.brevo.sender_email');
    }

    public function sendQuotationEmail($toEmail, $toName, $subject, $htmlContent)
    {
        try {
            $response = $this->client->post('https://api.brevo.com/v3/smtp/email', [
                'headers' => [
                    'accept' => 'application/json',
                    'api-key' => $this->apiKey,
                    'content-type' => 'application/json',
                ],
                'json' => [
                    'sender' => [
                        'name'  => $this->senderName,
                        'email' => $this->senderEmail,
                    ],
                    'to' => [
                        ['email' => $toEmail, 'name' => $toName]
                    ],
                    'subject' => $subject,
                    'htmlContent' => $htmlContent
                ],
            ]);

            return [
                'success' => true,
                'response' => json_decode($response->getBody(), true)
            ];
        } catch (ClientException $e) {
            return [
                'success' => false,
                'status_code' => $e->getResponse()->getStatusCode(),
                'response' => json_decode($e->getResponse()->getBody(), true)
            ];
        }
    }
}
