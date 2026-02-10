<?php

namespace Charcoal\Admin\Property\Input;

/**
 * Mailchimp List Sign-up property input.
 *
 * Allows one to select an available Mailchimp List's sign-up form.
 */
class MailchimpFormInput extends MailchimpInput
{
    /** @var array<string, mixed> */
    protected ?array $mailchimpOptions = null;

    protected ?string $mailchimpListId = null;

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

    public function mailchimpListId(): ?string
    {
        return $this->mailchimpListId;
    }

    public function setMailchimpListId(?string $mailchimpListId): self
    {
        $this->mailchimpListId = $this->renderTemplate($mailchimpListId);

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
            'title_pattern'   => '{{header.text}}',
            'value_pattern'   => '{{signup_form_url}}',
            'label_pattern'   => '{{header.text}}',
            'subtext_pattern' => 'Form URL: {{signup_form_url}}',
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

        // Get the available list from the Mailchimp API.
        $forms = $this->mailchimp()->get($endpoint);

        foreach ($forms->signup_forms as $form) {
            $title   = $this->view()->renderTemplate($opts['title_pattern'], $form);
            $label   = $this->view()->renderTemplate($opts['label_pattern'], $form);
            $value   = $this->view()->renderTemplate($opts['value_pattern'], $form);
            $subtext = $this->view()->renderTemplate($opts['subtext_pattern'], $form);

            $out = [
                'id'      => $form->signup_form_url,
                'value'   => $value,
                'title'   => $title,
                'label'   => $label,
                'subtext' => $subtext,
            ];

            yield $this->parseChoice($form->signup_form_url, $out);
        }
    }
}
