<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;



class Mail
{
    private $api_key = '6726850abff38f7c81211f7ed34d731f';
    private $api_key_secret = 'd8b537b434df9a809c01d3a6cae3f634';

    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "essarrar_nabil@live.fr",
                        'Name' => "Angels Voice"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 3477343,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }




}
