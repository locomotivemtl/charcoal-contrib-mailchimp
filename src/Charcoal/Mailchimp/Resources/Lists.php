<?php

namespace Charcoal\Mailchimp\Resources;

/**
 * Mailchimp Lists API facade
 *
 * {@link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/}
 */
class Lists extends Base
{
    public const API_ENDPOINT = 'lists';

    protected ?string $listId = null;

    public function listId(): ?string
    {
        return $this->listId;
    }

    public function setListId(?string $listId): self
    {
        $this->listId = $listId;
        return $this;
    }

    protected function apiEndpoint(): string
    {
        if ($this->listId()) {
            return strtr(self::API_ENDPOINT . '/{list_id}', [
                '{list_id}' => $this->listId(),
            ]);
        }

        return self::API_ENDPOINT;
    }
}
