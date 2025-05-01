<?php

namespace Fhoke\Bluetown\Mail;

use Fhoke\Slate\ACF\ACF;
use Mailgun\Mailgun;
use SlateMail\Mailer;

class MailgunSender extends Mailer implements Sender
{
    protected string $domain;
    protected string $api_key;
    protected string $api_endpoint;
    protected string $template_name;
    protected Mailgun $mailgun;

    public function __construct()
    {
        parent::__construct();

        $settings = \array_filter(ACF::settingsField('mailgun_settings') ?: []);

        $this->domain        = $settings['domain'] ?? '';
        $this->api_key       = $settings['api_key'] ?? '';
        $this->api_endpoint  = $settings['api_endpoint'] ?? 'https://api.mailgun.net';
        $this->template_name = $settings['template_name'] ?? '';

        $this->mailgun = Mailgun::create($this->api_key, $this->api_endpoint);
    }

    public function send()
    {
        foreach ($this->recipients as $recipient) {
            $args = [
                'to'    => "{$recipient['name']} <{$recipient['email']}>",
                'from'  => "{$this->from['name']} <{$this->from['email']}>",
                'subject' => $this->subject,
            ];

            if ($this->template_name) {
                $args['template']              = $this->template_name;
                $args['h:X-Mailgun-Variables'] = '{"content": ' . \json_encode($this->content()) . '}';
            } else {
                $args['html'] = $this->content();
            }

            $this->mailgun->messages()->send($this->domain, $args);
        }
    }
}
