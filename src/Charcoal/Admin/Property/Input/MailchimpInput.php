<?php

namespace Charcoal\Admin\Property\Input;

use Charcoal\Admin\Property\Input\SelectInput;
use Charcoal\Mailchimp\Service\Mailchimp;
use Psr\Container\ContainerInterface;

/**
 * Mailchimp property input
 */
class MailchimpInput extends SelectInput
{
    private ?Mailchimp $mailchimp = null;

    public function setDependencies(ContainerInterface $container): void
    {
        parent::setDependencies($container);

        $this->mailchimp = $container['mailchimp'];
    }

    protected function setMailchimp(Mailchimp $mailchimp): self
    {
        $this->mailchimp = $mailchimp;
        return $this;
    }

    protected function mailchimp(): Mailchimp
    {
        return $this->mailchimp;
    }
}
