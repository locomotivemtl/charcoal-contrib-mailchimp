<?php

namespace Charcoal\Mailchimp\ServiceProvider;

use Charcoal\Mailchimp\Resources\Lists;
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

        /**
         * Mailchimp List facade
         *
         * @param Container $container
         * @return Lists
         */
        $container['mailchimp/lists'] = function (Container $container) {
            return new Lists(
                ['mailchimp' => $container['mailchimp']]
            );
        };

        /**
         * Mailchimp Members facade
         *
         * @param Container $container
         * @return Lists\Members
         */
        $container['mailchimp/lists/members'] = function (Container $container) {
            return new Lists\Members(
                ['mailchimp' => $container['mailchimp']]
            );
        };


    }
}
