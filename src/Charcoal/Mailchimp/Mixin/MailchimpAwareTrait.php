<?php

namespace Charcoal\Mailchimp\Mixin;

use Charcoal\Mailchimp\Resources\Lists;
use Charcoal\Mailchimp\Resources\Lists\Members;
use Charcoal\Mailchimp\Service\Mailchimp;

trait MailchimpAwareTrait
{
    protected ?Mailchimp $mailchimp = null;

    protected ?Lists $mailchimpLists = null;

    protected ?Members $mailchimpListsMembers = null;

    public function mailchimp(): Mailchimp
    {
        return $this->mailchimp;
    }

    public function setMailchimp(Mailchimp $mailchimp): self
    {
        $this->mailchimp = $mailchimp;
        return $this;
    }

    public function mailchimpLists(): Lists
    {
        return $this->mailchimpLists;
    }

    public function setMailchimpLists(Lists $mailchimpLists): self
    {
        $this->mailchimpLists = $mailchimpLists;
        return $this;
    }

    public function mailchimpListsMembers(): Members
    {
        return $this->mailchimpListsMembers;
    }

    public function setMailchimpListsMembers(Members $mailchimpListsMembers): self
    {
        $this->mailchimpListsMembers = $mailchimpListsMembers;
        return $this;
    }
}
