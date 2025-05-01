<?php

namespace Fhoke\Slate\ACF;

class Link extends Field
{
    protected $args;
    protected $options;
    protected $isEnabled;
    protected $isExternal;
    protected $isNoFollow;
    protected $isNoReferrer;
    protected $isNoOpener;
    protected $cssClasses = [];
    protected $url;
    protected $text;

    public function __construct($field, array $args = [])
    {
        parent::__construct($field);

        $this->args = $args;

        $this->options      = (array) ($this->field['options'] ?? []);
        $this->isEnabled    = (bool) ($field['is_enabled'] ?? false);
        $this->isExternal   = \in_array('external', $this->options);
        $this->isNoFollow   = \in_array('no_follow', $this->options);
        $this->isNoReferrer = \in_array('no_referrer', $this->options);
        $this->isNoOpener   = \in_array('no_opener', $this->options);
        $this->cssClasses   = (array) ($this->args['classes'] ?? []);
        $this->url          = (string) ($field['url'] ?? '');

        $this->setText($field['txt'] ?? '');
    }

    /**
     * Make sure everything is present to display the link
     *
     * @return boolean
     */
    public function isSetup()
    {
        return $this->isEnabled && $this->url && $this->text;
    }

    /**
     * Add a string before the text for the link
     *
     * @param string $text
     * @return self
     */
    public function addBeforeText(string $text)
    {
        $this->text = $text . $this->text;

        return $this;
    }

    /**
     * Add a string after the text for the link
     *
     * @param string $text
     * @return self
     */
    public function addAfterText(string $text)
    {
        $this->text = $this->text . $text;

        return $this;
    }

    /**
     * Add the text for the link
     *
     * @param string $text
     * @return self
     */
    public function setText(string $text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Create the CSS classes HTML attribute
     *
     * @return string
     */
    public function classAttribute()
    {
        if (!$this->cssClasses || !\is_array($this->cssClasses)) {
            return '';
        }

        $classes = \implode(' ', $this->cssClasses ?? []);
        $classes = \trim($classes ?? '');

        return "class='{$classes}'";
    }

    /**
     * Create the target HTML attribute
     *
     * @return string
     */
    public function targetAttribute()
    {
        if ($this->isExternal) {
            $target = '_blank';
        } else {
            return '';
        }

        return "target='$target'";
    }

    /**
     * Create the rel HTML attribute
     *
     * @return string
     */
    public function relAttribute()
    {
        $rel = [];

        $rel[] = $this->isNoFollow ? 'nofollow' : null;
        $rel[] = $this->isNoReferrer ? 'noreferrer' : null;
        $rel[] = $this->isNoOpener ? 'noopener' : null;

        $rel = \array_filter($rel);

        if (!$rel) {
            return '';
        }

        return "rel='" . \implode(' ', $rel) . "'";
    }

    /**
     * Return the text
     *
     * @return string
     */
    public function text()
    {
        return $this->text;
    }

    /**
     * Return the URL
     *
     * @return void
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * Render the HTML of the anchor link
     *
     * @return string
     */
    public function render()
    {
        if (!$this->isSetup()) {
            return '';
        }

        return "<a href='{$this->url}' {$this->classAttribute()} {$this->targetAttribute()} {$this->relAttribute()}><span>{$this->text}</span></a>";
    }
}
