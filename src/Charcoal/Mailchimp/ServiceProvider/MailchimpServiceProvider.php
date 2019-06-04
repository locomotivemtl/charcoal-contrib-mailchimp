<?php

namespace Charcoal\Mailchimp\ServiceProvider;

use Charcoal\Mailchimp\Service\Mailchimp;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Mailchimp Service Provider
 */
class MailchimpServiceProvider implements ServiceProviderInterface
{
    /**
     * @param  Container $container Pimple DI Container.
     * @return void
     */
    public function register(Container $container)
    {
        /**
         * Helps dealing with the mailchimp api.
         *
         * @param  Container $container Pimple DI Container.
         * @return Mailchimp  Mailchimp object.
         */
        $container['mailchimp'] = function (Container $container) {
            $cfg       = $container['config'];
            $key       = $cfg->get('apis.mailchimp.key');
            $mailchimp = new Mailchimp();
            $mailchimp->setApiKey($key);

            return $mailchimp;
        };
    }
}
