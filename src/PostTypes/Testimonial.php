<?php

namespace Fhoke\Bluetown\PostTypes;

class Testimonial extends Post
{
    public string $logo;
    public string $logoHtml;
    public string $quote;
    public string $quoteAttribution;

    public function __construct(int $post_id)
    {
        parent::__construct($post_id);
    }

    public function withAll()
    {
        $this->withLogo();
        $this->withQuote();
        $this->withQuoteAttribution();

        return $this;
    }

    public function withLogo()
    {
        $this->logo = $this->getMeta('logo');

        $this->logoHtml = \bto_cms_image($this->logo, 'Logo');

        return $this;
    }

    public function withQuote()
    {
        $this->quote = $this->getMeta('quote');

        return $this;
    }

    public function withQuoteAttribution()
    {
        $this->quoteAttribution = $this->getMeta('quote_attribution');

        return $this;
    }
}
