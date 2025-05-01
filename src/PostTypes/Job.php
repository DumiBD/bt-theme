<?php

namespace Fhoke\Bluetown\PostTypes;

use DateTime;
use Fhoke\Bluetown\User;

class Job extends Post
{
    public bool $isExpired = false;
    public bool $isNew     = false;
    public bool $isSaved   = false;

    public int $daysSinceCreated;
    public string $daysSinceCreatedFormatted;

    public string $salary           = '';
    public array $industries        = [];
    public array $locations         = [];
    public array $types             = [];
    public string $applyUrl         = '';
    public string $applicationEmail = '';
    public string $reference        = '';

    public function __construct(int $post_id)
    {
        parent::__construct($post_id);

        $this->applyUrl         = $this->getMeta('_wpbb_job_broadbean_application_url');
        $this->applicationEmail = $this->getMeta('_wpbb_job_broadbean_application_email');
        $this->reference        = $this->getMeta('_wpbb_job_reference');

        $this->checkIfExpired();
        $this->getDaysSinceCreated();
        $this->checkIfSaved();

        if ($this->daysSinceCreated <= 5) {
            $this->isNew = true;
        }
    }

    public function withAll()
    {
        $this->withSalary();
        $this->withIndustries();
        $this->withLocations();

        return $this;
    }

    protected function checkIfExpired()
    {
        $this->isExpired = $this->WP_Post->post_status == 'expired' && $this->getMeta('expired_date');
    }

    protected function checkIfSaved()
    {
        $user_obj = new User(\get_current_user_id());

        $this->isSaved = \in_array($this->id(), $user_obj->savedJobsIds);
    }

    protected function getDaysSinceCreated()
    {
        $posted_date   = new DateTime($this->date);
        $todays_date   = new DateTime('now');

        $this->daysSinceCreated          = $posted_date->diff($todays_date)->days;
        $this->daysSinceCreatedFormatted = \sprintf(
            \_n(
                'Posted: %s day ago',
                'Posted: %s days ago',
                $this->daysSinceCreated,
                'bluetown'
            ),
            $this->daysSinceCreated
        );
    }

    public function withSalary()
    {
        $currency = $this->getMeta('_wpbb_job_salary_currency') ?: '£';

        $this->salary = $this->getMeta('_wpbb_job_salary_display');

        if (!$this->salary) {
            $this->salary = "{$currency}{$this->getMeta('_wpbb_job_salary_from')} - {$currency}{$this->getMeta('_wpbb_job_salary_to')}";
        } elseif (\is_numeric($this->salary)) {
            $this->salary = "{$currency}{$this->salary}";
        }

        if (!$this->salary) {
            $this->salary = 'N/A';
        }

        return $this;
    }

    public function withIndustries()
    {
        $this->industries = \get_the_terms($this->id, 'wpbb_job_industry') ?: [];

        return $this;
    }

    public function withLocations()
    {
        $this->locations = \get_the_terms($this->id, 'wpbb_job_location') ?: [];

        return $this;
    }

    public function withTypes()
    {
        $this->types = \get_the_terms($this->id, 'wpbb_job_type') ?: [];

        return $this;
    }

    public function industryLinks()
    {
        return \array_map(function (\WP_Term $term) {
            $url = \get_term_link($term);

            return "<a href='{$url}'>{$term->name}</a>";
        }, $this->industries);
    }

    public function locationLinks()
    {
        return \array_map(function (\WP_Term $term) {
            $url = \get_term_link($term);

            return "<a href='{$url}'>{$term->name}</a>";
        }, $this->locations);
    }

    public function typeLinks()
    {
        return \array_map(function (\WP_Term $term) {
            $url = \get_term_link($term);

            return "<a href='{$url}'>{$term->name}</a>";
        }, $this->types);
    }
}
