# Plugin Loader for Shopware
Required Minimum Shopware Version 5.2.2

### Installation
* Checkout Plugin in /custom/plugins/RawPluginLoader
* Install the Plugin with the Plugin Manager

### Command

install/activate or uninstall plugins:
```
php bin/console raw:plugin-loader
```

### Pluginconfig
To install and activate the shopware-plugin, please create file "pluginconfig.php" in plugin-folder with code:

```php
<?php
return [
    'active' => true,
];
```

for uninstall:

```php
<?php
return [
    'active' => false,
];
```

#### Dist-Pluginconfig

- pluginconfig.dist.php is low-level config file
- pluginconfig.dist.php is merged with pluginconfig.php

##### Example

we have two pluginconfig in plugin

File: /custom/plugins/MyPlugin/pluginconfig.dist.php
```php
<?php
return [
    'active' => true,
    'clearcache' => true
];
```

File: /custom/plugins/MyPlugin/pluginconfig.php
```php
<?php
return [
    'active' => false
];
```

Result for plugin-loader:
```php
<?php
return [
    'active' => false,
    'clearcache' => true,
];
```

#### Info

Work with new and legacy plugin system. 

##### Options:
- **active**
  - _true_ - install and activate plugins
  - _false_ - deactivate and uninstall plugin
- **clearcache**
  - _true_ - remove folder production and testing in /var/cache and generate attributes
  - _false_ - do nothing 

#### ToDo

* Set plugin config
* Create plugin for shopware 5.1 and shopware 4.3

