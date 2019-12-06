<?php

namespace Charcoal\Admin\Property\Input;

use Generator;

/**
 * MailchimpList input.
 * Finds the available list for a given account (api key).
 */
class MailchimpListInput extends MailchimpInput
{
    /**
     * @var array $mailchimOptions
     */
    protected $mailchimpOptions;

    /**
     * Mailchimp options
     * Defaults to defaultOptions when none given.
     * Merged with default options in setMailchimpOptions.
     *
     * @return array Options.
     */
    protected function mailchimpOptions()
    {
        if (!$this->mailchimpOptions) {
            return $this->defaultOptions();
        }

        return $this->mailchimpOptions;
    }

    /**
     * Set mail chimp options
     * You can set the `api_key` at this point, which is useful if
     * you have multiple api keys you need to set on multiple properties.
     * Default api key is set in the `ServiceProvider` that includes
     * Mailchimp and finds its source in the config of the site (apis.mailchimp.key).
     *
     * @param array $options Mailchimp property input options.
     * @return self
     */
    public function setMailchimpOptions(array $options = [])
    {
        $this->mailchimpOptions = array_merge($this->defaultOptions(), $options);

        return $this;
    }

    /**
     * Default options for the plugin, such as patterns.
     *
     * @return array Options.
     */
    protected function defaultOptions()
    {
        return [
            'title_pattern'    => '{{name}}',
            'value_pattern'    => '{{id}}',
            'label_pattern'    => '{{name}}',
            'subtext_pattern'  => 'Web ID: {{id}}',
            'query_parameters' => []
        ];
    }

    /**
     * Formats response from mailchimp as seen here:
     * http://developer.mailchimp.com/documentation/mailchimp/reference/lists/
     * Value, title, label and subtext are rendered on the response object. You
     * can use any properties from the `Response body parameters` defined in
     * the previous link.
     *
     * @return Generator Formatted choices.
     */
    public function choices()
    {
        if ($this->p()->getAllowNull() && !$this->p()->getMultiple()) {
            $prepend = $this->parseChoice('', $this->emptyChoice());

            yield $prepend;
        }

        $opts = $this->mailchimpOptions();

        // Get the available list from the mailchimp api.
        $list = $this->mailchimp()->get('lists', $opts['query_parameters']);

        foreach ($list->lists as $l) {
            // Render the templates.
            $title   = $this->view()->renderTemplate($opts['title_pattern'], $l);
            $label   = $this->view()->renderTemplate($opts['label_pattern'], $l);
            $value   = $this->view()->renderTemplate($opts['value_pattern'], $l);
            $subtext = $this->view()->renderTemplate($opts['subtext_pattern'], $l);

            $out = [
                'id'      => $l->id,
                'value'   => $value,
                'title'   => $title,
                'label'   => $label,
                'subtext' => $subtext
            ];

            yield $this->parseChoice($l->id, $out);
        }
    }
}
