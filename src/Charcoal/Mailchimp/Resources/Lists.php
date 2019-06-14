<?php

namespace Charcoal\Mailchimp\Resources;

class Lists extends Base
{
    const API_ENDPOINT = 'lists';

    /**
     * @var string
     */
    protected $listId;

    /**
     * @return string
     */
    public function listId()
    {
        return $this->listId;
    }

    /**
     * @param string $listId
     * @return Lists
     */
    public function setListId($listId)
    {
        $this->listId = $listId;
        return $this;
    }

    /**
     * @return string
     */
    protected function apiEndpoint()
    {
        if ($this->listId()) {
            return strtr(self::API_ENDPOINT . '/{list_id}', [
                '{list_id}' => $this->listId()
            ]);
        }
        return self::API_ENDPOINT;
    }
}
