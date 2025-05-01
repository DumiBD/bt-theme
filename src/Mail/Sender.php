<?php

namespace Fhoke\Bluetown\Mail;

interface Sender
{
    public function setRecipients(array $recipients);

    public function setCC(array $recipients);

    public function setFrom(array $sender);

    public function setSubject(string $subject = '');

    public function setContent(string $content = '');

    public function setLogo(string $src = '', array $size = []);

    public function send();
}
