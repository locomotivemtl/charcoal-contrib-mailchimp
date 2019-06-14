<?php

namespace Charcoal\Mailchimp\Mixin;

use Charcoal\Mailchimp\Resources\Lists;
use Charcoal\Mailchimp\Service\Mailchimp;

trait MailchimpAwareTrait
{

    /**
     * @var Mailchimp
     */
    protected $mailchimp;

    /**
     * @var Lists
     */
    protected $mailchimpLists;

    /**
     * @var Lists\Members
     */
    protected $mailchimpListsMembers;

    /**
     * @return mixed
     */
    public function mailchimp()
    {
        return $this->mailchimp;
    }

    /**
     * @param Mailchimp $mailchimp
     * @return MailchimpAwareTrait
     */
    public function setMailchimp(Mailchimp $mailchimp)
    {
        $this->mailchimp = $mailchimp;
        return $this;
    }

    /**
     * @return Lists
     */
    public function mailchimpLists()
    {
        return $this->mailchimpLists;
    }

    /**
     * @param Lists $mailchimpLists
     * @return MailchimpAwareTrait
     */
    public function setMailchimpLists($mailchimpLists)
    {
        $this->mailchimpLists = $mailchimpLists;
        return $this;
    }

    /**
     * @return Lists\Members
     */
    public function mailchimpListsMembers()
    {
        return $this->mailchimpListsMembers;
    }

    /**
     * @param Lists\Members $mailchimpListsMembers
     * @return MailchimpAwareTrait
     */
    public function setMailchimpListsMembers($mailchimpListsMembers)
    {
        $this->mailchimpListsMembers = $mailchimpListsMembers;
        return $this;
    }
}
