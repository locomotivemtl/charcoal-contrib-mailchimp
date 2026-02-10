# Charcoal Mailchimp

[![License][badge-license]](LICENSE)
[![Latest stable version][badge-version]](tags)
[![Supported PHP version][badge-php]](composer.json)

A [Charcoal][charcoal-pkg] service provider for the [Mailchimp Marketing API](https://mailchimp.com/developer/marketing/api/).

## Installation

The preferred (and only supported) method is with Composer:

```shell
$ composer require locomotivemtl/charcoal-contrib-mailchimp
```

### Dependencies

#### Required

* [PHP](https://php.net) 8.1 or later

## Configuration

Include the Mailchimp module in the projects's configuration file.

This will merge the module's configuration file and register container services:

```json
{
    "modules": {
       "charcoal/mailchimp/mailchimp": {}
    }
}
```

Add the API key (Account > Settings > Extra > API keys) in the project's configuration files:

```json
"apis": {
    "mailchimp": {
        "key": "myapikey-usXX"
    }
}
```

## Usage

The Mailchimp module comes with a set of tools to help setup a newsletter subscription.

### Properties

The Mailchimp module provides two different input property types to select an audience or a sign-up form for a given audience.

#### Mailchimp List

The input property for selecting a List/Audience:

```jsonc
"type": "string",
"input_type": "charcoal/admin/property/input/mailchimp-list",
// Default options
"mailchimp_options": {
    "title_pattern": "{{name}}",
    "value_pattern": "{{id}}",
    "label_pattern": "{{name}}",
    "subtext_pattern": "Web ID: {{id}}",
    "query_parameters": {}
}
```

The `mailchimp_options` setting allows you to customize how the Lists are rendered as options.

The `query_parameters` setting allows you to customize which Lists to retrieve from the API. See the [Lists API reference for available parameters](https://developer.mailchimp.com/documentation/mailchimp/reference/lists/#%20)).

#### Mailchimp Sign-up Form

The input property for selecting a Sign-up Form for a given List:

```jsonc
"type": "string",
"input_type": "charcoal/admin/property/input/mailchimp-form",
// Default options
"mailchimp_options": {
    "title_pattern": "{{header.text}}",
    "value_pattern": "{{signup_form_url}}",
    "label_pattern": "{{header.text}}",
    "subtext_pattern": "Form URL: {{signup_form_url}}"
}
```

The `mailchimp_options` setting allows you to customize how the Sign-up Forms are rendered as options.

### User subscription

```php
class Foobar
{
    use MailchimpAwareTrait;

    public function setDependencies(\Psr\Container\ContainerInterface $container): void
    {
        $this->setMailchimpListsMembers($container['mailchimp/lists/members']);
    }

    public function run(): void
    {
        $user = [
            'email_address' => 'email@example.com',
            'status'        => 'pending',
            'merge_fields'  => [
                'FNAME' => 'John',
                'LNAME' => 'Doe',
            ],
        ];

        // Set list ID
        $listId = 'XYZ';
        $this->mailchimpListsMembers()->setListId($listId);

        // Add/Create user
        $results = $this->mailchimpListsMembers()->add($user);

        // Add or Update user
        $results = $this->mailchimpListsMembers()->addOrUpdate($user);

        // Get a user's informations
        $results = $this->mailchimpListsMembers()->get('email@example.com');

        // Delete a user from a list
        $results = $this->mailchimpListsMembers()->remove('email@example.com');
    }
}
```

The Mailchimp API client can be used directly if you are familiar with that service's API's endpoints and methods.

```php
$mailchimp = new \Charcoal\Mailchimp\Service\Mailchimp();
$mailchimp->setApiKey('AAA-XYZ');

// Get lists members
$mailchimp->get('lists/{list_id}/members');

// Add member to list
$mailchimp->post('list/{list_id}/members', [
    'email_address' => 'email@example.com',
    'status'        => 'pending',
]);
```

## Contributing

Everyone interacting with Charcoal is expected to follow the [code of conduct](https://github.com/charcoalphp/.github/blob/main/CODE_OF_CONDUCT.md).

Please see our [contribution guide](https://github.com/charcoalphp/.github/blob/main/CONTRIBUTING.md) on how to contribute to Charcoal.

If you are tying to report a possible security vulnerability in Charcoal, please see our [security policy](https://github.com/charcoalphp/charcoal/security/policy) for more information.

## Authors

* [Locomotive](https://locomotive.ca/) 🚂

## License

The Charcoal Mailchimp module is licensed under the MIT license. See [LICENSE](LICENSE) for details.

[charcoal-contrib-mailchimp]: https://packagist.org/packages/locomotivemtl/charcoal-contrib-mailchimp
[charcoal-org]:               https://github.com/charcoalphp
[charcoal-pkg]:               https://packagist.org/packages/charcoal/charcoal

[badge-license]: https://img.shields.io/packagist/l/locomotivemtl/charcoal-contrib-mailchimp.svg?style=flat-square
[badge-php]:     https://img.shields.io/packagist/dependency-v/locomotivemtl/charcoal-contrib-mailchimp/php.svg?style=flat-square&logo=php
[badge-version]: https://img.shields.io/packagist/v/locomotivemtl/charcoal-contrib-mailchimp.svg?style=flat-square&logo=packagist
