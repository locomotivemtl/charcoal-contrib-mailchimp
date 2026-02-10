<?php

namespace Charcoal\Mailchimp\Resources;

use Charcoal\Mailchimp\Mixin\MailchimpAwareTrait;
use Charcoal\Mailchimp\Service\Mailchimp;

/**
 * Mailchimp resource API facade
 */
class Base
{
    use MailchimpAwareTrait;

    /**
     * @param array{
     *     mailchimp: Mailchimp
     * } $data
     */
    public function __construct(array $data)
    {
        $this->setMailchimp($data['mailchimp']);
    }
}
