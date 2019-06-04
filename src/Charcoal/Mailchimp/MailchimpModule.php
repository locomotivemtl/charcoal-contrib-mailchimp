<?php

namespace Charcoal\Mailchimp;

use Charcoal\App\Module\AbstractModule;
use Charcoal\Mailchimp\ServiceProvider\MailchimpServiceProvider;

/**
 * Mailchimp Module
 */
class MailchimpModule extends AbstractModule
{
    const APP_CONFIG = 'vendor/locomotivemtl/charcoal-contrib-mailchimp/config/config.json';

    /**
     * Setup the module's dependencies.
     *
     * @return AbstractModule
     */
    public function setup()
    {
        $container = $this->app()->getContainer();

        $mailchimpServiceProvider = new MailchimpServiceProvider();
        $container->register($mailchimpServiceProvider);

        return $this;
    }
}
