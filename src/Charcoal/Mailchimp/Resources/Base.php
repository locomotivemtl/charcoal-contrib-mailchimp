<?php

namespace Charcoal\Mailchimp\Resources;

use Charcoal\Mailchimp\Mixin\MailchimpAwareTrait;

class Base
{
    use MailchimpAwareTrait;

    /**
     * Members constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->setMailchimp($data['mailchimp']);
    }
}
