<?php

namespace Charcoal\Mailchimp;

use Charcoal\App\Module\AbstractModule;
use Charcoal\Mailchimp\ServiceProvider\MailchimpServiceProvider;

/**
 * Charcoal Mailchimp Module
 */
class MailchimpModule extends AbstractModule
{
    public const APP_CONFIG = 'vendor/locomotivemtl/charcoal-contrib-mailchimp/config/config.json';

    public function setup()
    {
        $container = $this->app()->getContainer();

        $mailchimpServiceProvider = new MailchimpServiceProvider();
        $mailchimpServiceProvider->register($container);

        return $this;
    }
}
