<?php

namespace Fhoke\Bluetown\Mail;

use Fhoke\Bluetown\PostTypes\Job;
use Fhoke\Bluetown\PostTypes\JobApplication;
use Fhoke\Slate\ACF\ACF;
use Fhoke\Slate\Templating\Views\View;

class Mail
{
    public static Sender $sender;
    protected array $logo = [];

    protected function setLogo()
    {
        $this->logo = [
            'src'  => \BTO_IMG_URL . '/logo-emails.png',
            'size' => [
                'width'  => 220,
                'height' => 73,
            ],
        ];
    }

    public static function setUp()
    {
        self::$sender = new Mailer();

        return new self();
    }

    public static function setSender(Sender $sender)
    {
        self::$sender = $sender;

        return new self();
    }

    public function sendPasswordResetEmail(string $name, string $email, string $username, string $key)
    {
        $this->setLogo();

        $login_page = ACF::settingsField('endpoint_login');
        $reset_url  = \add_query_arg([
            'action' => 'reset_password',
            'key'    => $key,
            'login'  => \rawurlencode($username),
        ], \get_permalink($login_page));

        self::$sender
            ->setLogo($this->logo['src'], $this->logo['size'])
            ->setRecipients([
                [
                    'name'  => $name,
                    'email' => $email,
                ],
            ])
            ->setCC([])
            ->setSubject(__('Password reset link', 'bluetown'))
            ->setContent("Someone has requested a password reset for this email address.<br><br>If it wasn't you, please ignore this email, otherwise click the following link: <a href='{$reset_url}'>{$reset_url}</a>")
            ->send();
    }

    public function sendWeeklyJobs(string $name, string $email, string $content)
    {
        $this->setLogo();

        $from = ACF::settingsField('mailgun_from');

        self::$sender
            ->setLogo($this->logo['src'], $this->logo['size'])
            ->setFrom([
                'name'  => $from['name'] ?? '',
                'email' => $from['email'] ?? '',
            ])
            ->setRecipients([
                [
                    'name'  => $name,
                    'email' => $email,
                ],
            ])
            ->setCC([])
            ->setSubject(__('Your weekly jobs digest', 'bluetown'))
            ->setContent($content)
            ->send();
    }

    public function sendWelcomeEmail(string $name, string $email)
    {
        $this->setLogo();

        $account_url = \home_url('account');

        self::$sender
            ->setLogo($this->logo['src'], $this->logo['size'])
            ->setRecipients([
                [
                    'name'  => $name,
                    'email' => $email,
                ],
            ])
            ->setCC([])
            ->setSubject(__('Welcome to Bluetown!', 'bluetown'))
            ->setContent("Thanks for creating an account on Bluetown.<br><br>Please go to <a href='{$account_url}'>your account</a> to finish filling out the rest of your profile.")
            ->send();
    }

    public function sendJobApplicationEmail(Job $job, JobApplication $job_application)
    {
        $this->setLogo();

        self::$sender
            ->setLogo($this->logo['src'], $this->logo['size'])
            ->setFrom([
                'name'  => \trim("{$job_application->first_name} {$job_application->last_name}"),
                'email' => $job_application->email,
            ])
            ->setRecipients([
                [
                    'email' => $job->applicationEmail,
                ],
            ])
            ->setCC([])
            ->setSubject(__('New Job Application Submitted', 'bluetown'))
            ->setContent(View::render('emails/job-application.twig', [
                'job'             => $job,
                'job_application' => $job_application,
            ]))
            ->setAttachments([
                $job_application->cvPath(),
            ])
            ->send();
    }
}
