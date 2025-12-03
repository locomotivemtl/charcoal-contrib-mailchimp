<?php

namespace Charcoal\Mailchimp\ServiceProvider;

use Charcoal\Mailchimp\Resources\Lists;
use Charcoal\Mailchimp\Service\Mailchimp;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

/**
 * Mailchimp Service Provider
 */
class MailchimpServiceProvider
{
    /**
     * @param  Container $container Pimple DI Container.
     * @return void
     */
    public function register(ContainerInterface $container)
    {
        /**
         * Helps dealing with the mailchimp api.
         *
         * @param  Container $container Pimple DI Container.
         * @return Mailchimp  Mailchimp object.
         */
        $container->set('mailchimp', function (ContainerInterface $container) {
            $cfg       = $container->get('config');
            $key       = $cfg->get('apis.mailchimp.key');
            $mailchimp = new Mailchimp();
            $mailchimp->setApiKey($key);

            return $mailchimp;
        });

        /**
         * Mailchimp List facade
         *
         * @param Container $container
         * @return Lists
         */
        $container->set('mailchimp/lists', function (ContainerInterface $container) {
            return new Lists(
                ['mailchimp' => $container->get('mailchimp')]
            );
        });

        /**
         * Mailchimp Members facade
         *
         * @param Container $container
         * @return Lists\Members
         */
        $container->set('mailchimp/lists/members', function (ContainerInterface $container) {
            return new Lists\Members(
                ['mailchimp' => $container->get('mailchimp')]
            );
        });


    }
}
