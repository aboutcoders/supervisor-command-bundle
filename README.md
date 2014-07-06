Symfony Supervisor Command Bundle
==========================

A symfony bundle that provides console commands to control supervisor instances (based on the https://github.com/yzalis/SupervisorBundle).

## Configuration

Add the bundle:

``` json
{
    "require": {
        "aboutcoders/supervisor-command-bundle": "dev-master"
    }
}
```

Enable the bundles in the kernel:

``` php
# app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Abc\Bundle\SupervisorCommandBundle\AbcSupervisorCommandBundle(),
        new YZ\SupervisorBundle\YZSupervisorBundle()
        // ...
    );
}
```

Follow the installation and configuration instructions of the third party bundles:

* [YZSupervisorBundle](https://github.com/yzalis/SupervisorBundle)

## Usage

Please enter the followin command to see the get information about the available commands:

```
php app/console list abc:supervisor
```