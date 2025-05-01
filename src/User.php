<?php

namespace Fhoke\Bluetown;

use OOPWP\User as OOPWPUser;

class User extends OOPWPUser
{
    public string $phone            = '';
    public string $cvUrl            = '';
    public string $cvFilename       = '';
    public array $interestedInTerms = [];
    public bool $emailAlertsOptOut  = false;
    public string $linkedinUrl      = '';
    public string $address          = '';
    public string $addressCounty    = '';
    public string $addressTown      = '';
    public string $addressPostcode  = '';
    public string $addressCountry   = '';

    public array $appliedJobsIds = [];
    public array $savedJobsIds   = [];

    public function __construct(int $id)
    {
        parent::__construct($id);

        if (!$this->WPUser()) {
            return;
        }

        $this->phone             = $this->meta('phone') ?: '';
        $this->cvUrl             = $this->meta('cv_url') ?: '';
        $this->cvFilename        = \basename($this->cvUrl) ?: '';
        $this->interestedInTerms = $this->meta('interested_in_terms') ?: [];
        $this->emailAlertsOptOut = $this->meta('email_alerts_opt_out') == 'on' ?: false;
        $this->linkedinUrl       = $this->meta('linkedin_url') ?: '';
        $this->address           = $this->meta('address') ?: '';
        $this->addressCounty     = $this->meta('address_county') ?: '';
        $this->addressTown       = $this->meta('address_town') ?: '';
        $this->addressPostcode   = $this->meta('address_postcode') ?: '';
        $this->addressCountry    = $this->meta('address_country') ?: '';

        $this->appliedJobsIds = $this->meta('applied_job_ids') ?: [];
        $this->savedJobsIds   = $this->meta('saved_job_ids') ?: [];
    }

    public function saveJob(array $ids)
    {
        $this->savedJobsIds = Helpers::mergeArraysUnique($this->savedJobsIds, $ids);

        \update_user_meta($this->id(), 'saved_job_ids', $this->savedJobsIds);
    }

    public function unsaveJob(array $ids)
    {
        \array_map(function (int $id) {
            $key = array_search($id, $this->savedJobsIds);

            if ($key !== false) {
                unset($this->savedJobsIds[$key]);
            }
        }, $ids);

        \update_user_meta($this->id(), 'saved_job_ids', $this->savedJobsIds);
    }

    public function addJobToApplied(int $id)
    {
        $this->appliedJobsIds = Helpers::mergeArraysUnique($this->appliedJobsIds, [$id]);

        \update_user_meta($this->id(), 'applied_job_ids', $this->appliedJobsIds);
    }
}
