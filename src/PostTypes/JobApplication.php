<?php

namespace Fhoke\Bluetown\PostTypes;

class JobApplication extends Post
{
    public string $first_name    = '';
    public string $last_name     = '';
    public string $email         = '';
    public string $phone         = '';
    public string $cv_url        = '';
    public string $cover_message = '';
    public int $job_id           = 0;
    public $job;

    public function __construct(int $post_id)
    {
        parent::__construct($post_id);

        $this->first_name    = $this->getMeta('first_name') ?: '';
        $this->last_name     = $this->getMeta('last_name') ?: '';
        $this->email         = $this->getMeta('email') ?: '';
        $this->phone         = $this->getMeta('phone') ?: '';
        $this->cv_url        = $this->getMeta('cv_url') ?: '';
        $this->job_id        = $this->getMeta('job_id') ?: 0;
        $this->cover_message = $this->getMeta('cover_message') ?: '';

        if ($this->job_id) {
            $this->job = new Job($this->job_id);
        }
    }

    public function cvPath()
    {
        return \wp_get_upload_dir()['basedir'] . \substr($this->cv_url, \strrpos($this->cv_url, '/uploads') + \strlen('/uploads'));
    }

    public function emailData()
    {
        return [
            [
                'label' => __('Applicant Name', 'bluetown'),
                'value' => \trim($this->first_name . ' ' . $this->last_name),
            ],
            [
                'label' => __('Applicant Email', 'bluetown'),
                'value' => $this->email,
            ],
            [
                'label' => __('Applicant Phone', 'bluetown'),
                'value' => $this->phone,
            ],
            [
                'label' => __('Job Reference', 'bluetown'),
                'value' => $this->job ? $this->job->reference : '',
            ],
            [
                'label' => __('CV', 'bluetown'),
                'value' => "<a href='{$this->cv_url}'>View &rarr;</a>",
            ],
        ];
    }
}
