<?php

namespace Fhoke\Bluetown\PostTypes;

class TeamMember extends Post
{
    public string $headshot;
    public string $headshotHtml;
    public string $jobTitle;

    public function __construct(int $post_id)
    {
        parent::__construct($post_id);
    }

    public function withAll()
    {
        $this->withHeadshotHtml();
        $this->withJobTitle();

        return $this;
    }

    public function withHeadshotHtml()
    {
        $this->headshot = $this->getMeta('img');

        $this->headshotHtml = \bto_cms_image($this->headshot, 'Headshot');

        return $this;
    }

    public function withJobTitle()
    {
        $this->jobTitle = $this->getMeta('job_title');

        return $this;
    }
}
