<?php

namespace Charcoal\Admin\Property\Input;

use Charcoal\Mailchimp\Service\Mailchimp;
use Pimple\Container;

use Charcoal\Admin\Property\Input\SelectInput;

/**
 * Mailchimp Input
 */
class MailchimpInput extends SelectInput
{
    /**
     * Mailchimp helper
     * @var Mailchimp
     */
    private $mailchimp;

    /**
     * Inject dependencies from a DI Container.
     *
     * @param Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);
        $this->mailchimp = $container['mailchimp'];
    }

    /**
     * Set mailchimp api service.
     * Simple service that helps dealing easily with the mailchimp api.
     *
     * @param Mailchimp $mailchimp Service for mailchimp.
     * @return self
     */
    protected function setMailchimp(Mailchimp $mailchimp)
    {
        $this->mailchimp = $mailchimp;
        return $this;
    }

    /**
     * Simple service that helps dealing easily with the mailchimp api.
     *
     * @return Mailchimp Service for mailchimp.
     */
    protected function mailchimp()
    {
        return $this->mailchimp;
    }
}
