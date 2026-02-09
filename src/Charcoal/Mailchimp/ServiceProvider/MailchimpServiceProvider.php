<?php

namespace Charcoal\Mailchimp\ServiceProvider;

use Charcoal\Mailchimp\Resources\Lists;
use Charcoal\Mailchimp\Resources\Lists\Members;
use Charcoal\Mailchimp\Service\Mailchimp;
use DI\Container;

class MailchimpServiceProvider
{
    public function register(Container $container)
    {
        /**
         * Mailchimp API client.
         */
        $container->set('mailchimp', function (Container $container) {
            $mailchimp = new Mailchimp();
            $mailchimp->setApiKey(
                $container->get('config')->get('apis.mailchimp.key')
            );

            return $mailchimp;
        });

        /**
         * Mailchimp Lists API facade
         */
        $container->set('mailchimp/lists', function (Container $container) {
            return new Lists([
                'mailchimp' => $container->get('mailchimp'),
            ]);
        });

        /**
         * Mailchimp Members API facade
         */
        $container->set('mailchimp/lists/members', function (Container $container) {
            return new Members([
                'mailchimp' => $container->get('mailchimp'),
            ]);
        });
    }
}
