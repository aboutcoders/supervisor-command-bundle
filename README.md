AbcSupervisorCommandBundle
==========================

A symfony bundle that provides console commands to control supervisor instances (based on the [YZSupervisorBundle](https://github.com/yzalis/SupervisorBundle)).

Build Status: [![Build Status](https://travis-ci.org/aboutcoders/supervisor-command-bundle.svg?branch=master)](https://travis-ci.org/aboutcoders/supervisor-command-bundle)

## Installation

Follow the installation and configuration instructions of the third party bundles:

* [YZSupervisorBundle](https://github.com/yzalis/SupervisorBundle)

Add the AbcSupervisorCommandBundle to your `composer.json` file

```json
{
    "require": {
        "aboutcoders/supervisor-command-bundle": "dev-master"
    }
}
```

Include the bundle in the AppKernel.php class

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Abc\Bundle\SupervisorCommandBundle\AbcSupervisorCommandBundle(),
    );

    return $bundles;
}
```

## Usage

Enter the following command to get information about the available commands:

```
php app/console list abc:supervisor
```

## ToDo

* Increase unit test coverage
* Add support for symfony >= 2.3