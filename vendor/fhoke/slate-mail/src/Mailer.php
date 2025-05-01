<?php
/**
 * Mailer class for sending simple emails
 *
 * @package SlateMail
 * @author Fhoke <hello@fhoke.com>
 */

namespace SlateMail;

class Mailer
{
    protected $recipients;
    protected $cc          = [];
    protected $from        = [];
    protected $subject     = '';
    protected $content     = '';
    protected $attachments = [];

    protected $headers = [
        'Content-Type: text/html; charset=UTF-8',
    ];

    protected $logo_src  = '';
    protected $logo_size = [
        'width'  => 233,
        'height' => 76,
    ];

    public function __construct()
    {
        $this->from = [
            'name'  => \get_bloginfo('name'),
            'email' => \get_option('admin_email'),
        ];
    }

    public function setRecipients(array $recipients)
    {
        if (!isset($recipients) || !is_array($recipients)) {
            throw new \Exception("Recipients must be an array of arrays.");
        }

        $this->recipients = $recipients;

        return $this;
    }

    public function setCC(array $recipients)
    {
        if (!isset($recipients) || !is_array($recipients)) {
            throw new \Exception("Recipients must be an array of arrays.");
        }

        $this->cc = $recipients;

        return $this;
    }

    public function setFrom(array $sender)
    {
        $this->from['name']  = $sender['name'] ?? null;
        $this->from['email'] = $sender['email'] ?? null;

        return $this;
    }

    public function setSubject(string $subject = '')
    {
        if ($subject != '') {
            $this->subject = $subject;
        }

        return $this;
    }

    public function setContent(string $content = '')
    {
        if ($content != '') {
            $this->content = $content;
        }

        return $this;
    }

    public function setHeaders(array $headers = [])
    {
        if (!empty($headers)) {
            $this->headers = $headers;
        }

        return $this;
    }

    public function addHeaders(array $headers = [])
    {
        if (!empty($headers)) {
            $this->headers = \array_merge($this->headers, $headers);
        }

        return $this;
    }

    public function setAttachments(array $attachments = [])
    {
        if (!empty($attachments)) {
            $this->attachments = $attachments;
        }

        return $this;
    }

    public function setLogo(string $src = '', array $size = [])
    {
        $this->logo_src = $src;

        if (isset($size['width']) && isset($size['height'])) {
            $this->logo_size['width']  = $size['width'];
            $this->logo_size['height'] = $size['height'];
        }

        return $this;
    }

    /**
     * Convert array email address arrays to string
     *
     * @param array $arr
     * @return string
     */
    protected function addressesArrayToString(array $arr)
    {
        $arr = \array_map(function ($item) {
            $name  = $item['name'] ?? '';
            $email = $item['email'] ?? '';

            return "{$name} <{$email}>";
        }, $arr);

        return \implode(', ', $arr);
    }

    protected function logoHtml()
    {
        $logo_url = \home_url();

        if ($this->logo_src) {
            return  <<< EOT
                <a style='display: block;width: 100%;padding-bottom: 20px;text-align: center;' href='$logo_url' target='_blank'><img style='width: {$this->logo_size['width']}px;height: {$this->logo_size['height']}px;margin-bottom: 20px;' src='{$this->logo_src}'></a>
            EOT;
        }
    }

    protected function recipients()
    {
        return $this->recipients ? $this->addressesArrayToString($this->recipients) : '';
    }

    protected function cc()
    {
        return $this->cc ? $this->addressesArrayToString($this->cc) : '';
    }

    protected function from()
    {
        $name  = $this->from['name'] ?? '';
        $email = $this->from['email'] ?? '';

        return "{$name} <{$email}>";
    }

    protected function headers()
    {
        if ($this->from()) {
            $this->headers[] = "From: {$this->from()}";
        }

        if ($this->cc()) {
            $this->headers[] = "Cc: {$this->cc()}";
        }

        return $this->headers;
    }

    protected function attachments()
    {
        return $this->attachments;
    }

    protected function contentBefore()
    {
        return <<< EOT
            <html>
            <body style="background-color: #eee;padding: 35px 20px;font-family:'Arial', sans-serif;">
                {$this->logoHtml()}
                <div style="max-width: 600px;margin-left: auto;margin-right: auto;padding: 30px;box-shadow: 0 4px 12px rgba(0,0,0,.08);background-color: #fff;">
        EOT;
    }

    protected function contentAfter()
    {
        $name         = \get_bloginfo('name');
        $current_year = \date('Y');

        return <<< EOT
                </div>
                <div style="max-width:600px;margin-left:auto;margin-right:auto;padding-top:20px;text-align:center;">
                    <p>&copy; $name $current_year</p>
                </div>
            </body>
            </html>
        EOT;
    }

    protected function content()
    {
        return $this->contentBefore() . $this->content . $this->contentAfter();
    }

    public function send()
    {
        try {
            $sent = \wp_mail(
                $this->recipients(),
                $this->subject,
                $this->content(),
                $this->headers(),
                $this->attachments()
            );

            if ($sent == false) {
                throw new \Exception('There was an error sending the email.');
            }

            return $sent;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
