<?php

namespace Fhoke\Slate\Templating\Templates;

use Fhoke\Slate\Templating\Views\Views;
use Fhoke\Slate\Utilities;

class CoreTemplate
{
    protected $postID;
    protected $fields;
    protected $sectionFields;
    protected $views;

    protected $options = [
        'default_section_class' => 'core-section',
        'sections_classes'      => [],
    ];

    public function __construct($field_name = 'core', $post_id = null)
    {
        $this->postID        = $post_id ?: Utilities::currentPostID();
        $this->fields        = \get_field($field_name, $this->postID) ?: [];
        $this->sectionFields = $this->getSectionFields();
        $this->views         = new Views();
    }

    /**
     * Allow options to be set
     *
     * @param string $option
     * @param string|array $value
     * @return $this
     */
    public function setOption(string $option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Allow sections classes to be set
     *
     * @param array $classes
     * @param string $section_name
     * @return $this
     */
    public function setSectionClasses(array $classes = [], string $section_name = null)
    {
        if (!$section_name) {
            \array_map(function ($section) use ($classes) {
                $this->options['sections_classes'][$section['name']] = $classes;
            }, $this->sectionFields);
        } else {
            $this->options['sections_classes'][$section_name] = $classes;
        }

        return $this;
    }

    /**
     * Format a string to be a valid filename for a view
     *
     * @param string $string
     * @return string
     */
    protected static function formatViewFilename(string $string)
    {
        //---- Replace underscores with hyphens
        return \str_replace('_', '-', $string);
    }

    /**
     * Create CSS classes for each section
     *
     * @return string
     */
    protected function setHtmlClassesForSections()
    {
        \array_map(function ($section_key, $section) {
            $classes = & $this->sectionFields[$section_key]['args']['html_classes'];

            $classes         = $this->options['default_section_class'];
            $section_classes = & $this->options['sections_classes'][$section['name']] ?? [];

            if ($section_classes) {
                $section_classes[] = $classes;
                $section_classes[] = "core-section--{$this->sectionFields[$section_key]['filename']}";
            }

            $classes = \implode(' ', $section_classes);
        }, array_keys($this->sectionFields), $this->sectionFields);
    }

    /**
     * Create the arguments to pass to the view
     *
     * @param int $section_number
     * @param array $section_fields
     * @return array
     */
    protected static function createViewArgs(int $section_number, array $section_fields = [])
    {
        return \array_merge([
            'html_id' => "section-" . ($section_number + 1),
        ], $section_fields);
    }

    /**
     * Get data for sections
     *
     * @return array
     */
    protected function getSectionFields()
    {
        if (!$this->fields) {
            return [];
        }

        if(isset($this->fields['core'])) {
            $this->fields = $this->fields['core'];
        }

        return \array_map(function ($section_number, $section_fields) {
            return [
                'name'     => $section_fields['acf_fc_layout'],
                'filename' => self::formatViewFilename($section_fields['acf_fc_layout']),
                'args'     => self::createViewArgs($section_number, $section_fields),
            ];
        }, \array_keys($this->fields), $this->fields);
    }

    /**
     * Render the HTML
     *
     * @return string
     */
    public function render()
    {
        if (!$this->sectionFields) {
            return '';
        }

        $this->setHtmlClassesForSections();

        $html = '';

        \array_map(function ($section) use (&$html) {
            $html .= $this->views->render("sections/{$section['filename']}.twig", $section['args']);
        }, $this->sectionFields);

        return <<< EOT
            <main>
                $html
            </main>
        EOT;
    }
}
