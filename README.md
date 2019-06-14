Charcoal Mailchimp
===============

[![License][badge-license]][charcoal-contrib-mailchimp]
[![Latest Stable Version][badge-version]][charcoal-contrib-mailchimp]
[![Code Quality][badge-scrutinizer]][dev-scrutinizer]
[![Coverage Status][badge-coveralls]][dev-coveralls]
[![Build Status][badge-travis]][dev-travis]

A [Charcoal][charcoal-app] service provider mailchimp implementation.



## Table of Contents

-   [Installation](#installation)
    -   [Dependencies](#dependencies)
-   [Service Provider](#service-provider)
    -   [Parameters](#parameters)
    -   [Services](#services)
-   [Configuration](#configuration)
-   [Usage](#usage)
-   [Development](#development)
    -  [API Documentation](#api-documentation)
    -  [Development Dependencies](#development-dependencies)
    -  [Coding Style](#coding-style)
-   [Credits](#credits)
-   [License](#license)



## Installation

The preferred (and only supported) method is with Composer:

```shell
$ composer require locomotivemtl/charcoal-contrib-mailchimp
```



### Dependencies

#### Required

-   [**PHP 5.6+**](https://php.net): _PHP 7_ is recommended.


## Configuration

Include the mailchimp module in the projects's config file.
This will provide everything needed for [charcoal-contrib-mailchimp] to work properly.

```json
{
    "modules": {
       "charcoal/mailchimp/mailchimp": {}
    }
}
```

Add the api key (Account > Settings > Extra > Api keys) in the config apis:

```json
"apis": {
    "mailchimp": {
        "key": "myapikey-usXX"
    }
}
```


## Usage

[charcoal-contrib-mailchimp] comes with a set of tools to help setup a newsletter subscription.

### Properties

There are 2 different property types you can use to either select an audience or a signup form for a
given audience.

#### Mailchimp List
Set your property as follow.
```json
    "type": "string",
    "input_type": "charcoal/admin/property/input/mailchimp-list",
    "mailchimp_options": {
        "query_parameters": {
            "count": 20
        }
    }
```
You can customize the displayed label, the displayed title, the value and the subtext pattern. 
It is also possible to add query parameters (see [Doc](https://developer.mailchimp.com/documentation/mailchimp/reference/lists/#%20)) Default as follow:
```json
"mailchimp_options": {
    "title_pattern": "{{name}}",
    "value_pattern": "{{id}}",
    "label_pattern": "{{name}}",
    "subtext_pattern": "Web ID: {{id}}",
    "query_parameters": []
}
```

#### Mailchimp Signup Form
```json
    "type": "string",
    "input_type": "charcoal/admin/property/input/mailchimp-form",
    "mailchimp_options": {
        [...]
    }
```
You can customize the displayed label, the displayed title, the value and the subtext pattern. 
Default as follow:
```json
"mailchimp_options": {
    "title_pattern": "{{header.text}}",
    "value_pattern": "{{signup_form_url}}",
    "label_pattern": "{{header.text}}",
    "subtext_pattern": "Form URL: {{signup_form_url}}"
}
```

### User subscription

```php

class FooBar
{
    use MailchimpAwareTrait;
    [...]

    public function setDependencies(Container $container)
    {
        $this->setMailchimpListsMembers($container['mailchimp/lists/members']);
        [...]
    }
    
    public function run()
    {
        $user = [
            'email_address' => 'email@example.com',
            'status' => 'pending',
            'merge_fields' => [
                'FNAME' => 'John',
                'LNAME' => 'Doe'
            ]
        ];
        
        // Set list ID
        $listId = 'a4029db2d';
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

The mailchimp service is standalone and can be used directly if you know the endpoints by using the post, patch, get,
put and delete methods.

```php
// Get lists members
$this->mailchimp()->get('lists/{list_id}/members');

// Add member to list
$this->mailchimp()->post('list/{list_id}/members', [ 
    'email_address' => 'email@example.com',
    'status' => 'pending'
]);
```

## Coding Style

The charcoal-contrib-mailchimp module follows the Charcoal coding-style:

-   [_PSR-1_][psr-1]
-   [_PSR-2_][psr-2]
-   [_PSR-4_][psr-4], autoloading is therefore provided by _Composer_.
-   [_phpDocumentor_](http://phpdoc.org/) comments.
-   [phpcs.xml.dist](phpcs.xml.dist) and [.editorconfig](.editorconfig) for coding standards.

> Coding style validation / enforcement can be performed with `composer phpcs`. An auto-fixer is also available with `composer phpcbf`.



## Credits

-   [Locomotive](https://locomotive.ca/)



## License

Charcoal is licensed under the MIT license. See [LICENSE](LICENSE) for details.



[charcoal-contrib-mailchimp]:  https://packagist.org/packages/locomotivemtl/charcoal-contrib-mailchimp
[charcoal-app]:             https://packagist.org/packages/locomotivemtl/charcoal-app

[dev-scrutinizer]:    https://scrutinizer-ci.com/g/locomotivemtl/charcoal-contrib-mailchimp/
[dev-coveralls]:      https://coveralls.io/r/locomotivemtl/charcoal-contrib-mailchimp
[dev-travis]:         https://travis-ci.org/locomotivemtl/charcoal-contrib-mailchimp

[badge-license]:      https://img.shields.io/packagist/l/locomotivemtl/charcoal-contrib-mailchimp.svg?style=flat-square
[badge-version]:      https://img.shields.io/packagist/v/locomotivemtl/charcoal-contrib-mailchimp.svg?style=flat-square
[badge-scrutinizer]:  https://img.shields.io/scrutinizer/g/locomotivemtl/charcoal-contrib-mailchimp.svg?style=flat-square
[badge-coveralls]:    https://img.shields.io/coveralls/locomotivemtl/charcoal-contrib-mailchimp.svg?style=flat-square
[badge-travis]:       https://img.shields.io/travis/locomotivemtl/charcoal-contrib-mailchimp.svg?style=flat-square

[psr-1]:  https://www.php-fig.org/psr/psr-1/
[psr-2]:  https://www.php-fig.org/psr/psr-2/
[psr-3]:  https://www.php-fig.org/psr/psr-3/
[psr-4]:  https://www.php-fig.org/psr/psr-4/
[psr-6]:  https://www.php-fig.org/psr/psr-6/
[psr-7]:  https://www.php-fig.org/psr/psr-7/
[psr-11]: https://www.php-fig.org/psr/psr-11/
