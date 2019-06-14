<?php

namespace Charcoal\Admin\Property\Input;

/**
 * MailchimpList input.
 * Finds the available list for a given account (api key).
 */
class MailchimpFormInput extends MailchimpInput
{
    /**
     * @var array $mailchimOptions
     */
    protected $mailchimpOptions;

    /**
     * @var string $mailchimpListId
     */
    protected $mailchimpListId;

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
     * @return string
     */
    public function mailchimpListId()
    {
        return $this->mailchimpListId;
    }

    /**
     * @param string $mailchimpListId MailchimpListId for MailchimpFormInput.
     * @return self
     */
    public function setMailchimpListId($mailchimpListId)
    {
        $this->mailchimpListId = $this->renderTemplate($mailchimpListId);

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
            'title_pattern'    => '{{header.text}}',
            'value_pattern'    => '{{signup_form_url}}',
            'label_pattern'    => '{{header.text}}',
            'subtext_pattern'  => 'Form URL: {{signup_form_url}}'
        ];
    }

    /**
     * Formats response from mailchimp as seen here:
     * http://developer.mailchimp.com/documentation/mailchimp/reference/lists/
     * Value, title, label and subtext are rendered on the response object. You
     * can use any properties from the `Response body parameters` defined in
     * the previous link.
     *
     * @return array Formatted choices.
     */
    public function choices()
    {
        if ($this->p()->allowNull() && !$this->p()->multiple()) {
            $prepend = $this->parseChoice('', $this->emptyChoice());

            yield $prepend;
        }

        $opts = $this->mailchimpOptions();

        // Override key at this point if you want to.
        // Defaults to config.apis.mailchimp.key
        if (isset($opts['api_key'])) {
            $this->mailchimp()->setApiKey($opts['api_key']);
        }

        // No list id -> no choices.
        if (!$this->mailchimpListId()) {
            return [];
        }

        //endpoint
        $endpoint = sprintf(
            'lists/%s/signup-forms',
            $this->mailchimpListId()
        );

        // Get the available list from the mailchimp api.
        $forms = $this->mailchimp()->get($endpoint, []);

        foreach ($forms->signup_forms as $f) {
            // Render the templates.
            $title   = $this->view()->renderTemplate($opts['title_pattern'], $f);
            $label   = $this->view()->renderTemplate($opts['label_pattern'], $f);
            $value   = $this->view()->renderTemplate($opts['value_pattern'], $f);
            $subtext = $this->view()->renderTemplate($opts['subtext_pattern'], $f);

            $out = [
                'id'      => $f->signup_form_url,
                'value'   => $value,
                'title'   => $title,
                'label'   => $label,
                'subtext' => $subtext
            ];

            yield $this->parseChoice($f->signup_form_url, $out);
        }
    }
}
