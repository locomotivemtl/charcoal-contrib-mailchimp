<?php

namespace Charcoal\Admin\Property\Input;

/**
 * Mailchimp List property input.
 *
 * Allows one to select an available Mailchimp List.
 */
class MailchimpListInput extends MailchimpInput
{
    /** @var array<string, mixed> */
    protected ?array $mailchimpOptions = null;

    /**
     * Mailchimp options
     *
     * Defaults to defaultOptions when none given.
     *
     * Merged with default options in setMailchimpOptions.
     *
     * @return array<string, mixed>
     */
    protected function mailchimpOptions(): array
    {
        if ($this->mailchimpOptions === null) {
            return $this->defaultOptions();
        }

        return $this->mailchimpOptions;
    }

    /**
     * Set Mailchimp options
     *
     * You can set the `api_key` at this point, which is useful if
     * you have multiple API keys you need to set on multiple properties.
     *
     * Default API key is set in the `ServiceProvider` that includes
     * Mailchimp and finds its source in the config of the site (apis.mailchimp.key).
     *
     * @param array<string, mixed> $options
     */
    public function setMailchimpOptions(array $options = []): self
    {
        $this->mailchimpOptions = array_merge($this->defaultOptions(), $options);

        return $this;
    }

    /**
     * Default options for the plugin, such as patterns.
     *
     * @return array<string, mixed>
     */
    protected function defaultOptions(): array
    {
        return [
            'title_pattern'    => '{{name}}',
            'value_pattern'    => '{{id}}',
            'label_pattern'    => '{{name}}',
            'subtext_pattern'  => 'Web ID: {{id}}',
            'query_parameters' => [],
        ];
    }

    /**
     * Formats response from Mailchimp as seen here:
     *
     * {@link http://developer.mailchimp.com/documentation/mailchimp/reference/lists/}
     *
     * Value, title, label and subtext are rendered on the response object. You
     * can use any properties from the `Response body parameters` defined in
     * the previous link.
     *
     * @return iterable<array{
     *     id:       string,
     *     value:    string,
     *     title:    string,
     *     label:    string,
     *     subtext:  string,
     *     icon:     string,
     *     disabled: bool,
     * }>
     */
    public function choices()
    {
        if ($this->p()->getAllowNull() && !$this->p()->getMultiple()) {
            $prepend = $this->parseChoice('', $this->emptyChoice());

            yield $prepend;
        }

        $opts = $this->mailchimpOptions();

        // Get the available list from the Mailchimp API.
        $list = $this->mailchimp()->get('lists', $opts['query_parameters']);

        foreach ($list->lists as $list) {
            $title   = $this->view()->renderTemplate($opts['title_pattern'], $list);
            $label   = $this->view()->renderTemplate($opts['label_pattern'], $list);
            $value   = $this->view()->renderTemplate($opts['value_pattern'], $list);
            $subtext = $this->view()->renderTemplate($opts['subtext_pattern'], $list);

            $out = [
                'id'      => $list->id,
                'value'   => $value,
                'title'   => $title,
                'label'   => $label,
                'subtext' => $subtext,
            ];

            yield $this->parseChoice($list->id, $out);
        }
    }
}
