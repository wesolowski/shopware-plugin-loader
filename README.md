# Plugin Loader for Shopware
Required Minimum Shopware Version 5.2.2

### Installation
* Checkout Plugin in /custom/plugins/RawPluginLoader
```
git clone  https://github.com/wesolowski/shopware-plugin-loader.git RawPluginLoader
```
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
    'clearcache' => true,
    'prio' => 1
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
    'prio' => 1
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
- **reinstall**
  - _true_ - reinstall and activate plugins - always whenever this command is executed
  - _false_ - do nothing
- **prio**
  - _#_ - set priority, in which order the plugins should be loaded (can be a negative and a positive number)
  

Config with all options
```php
<?php
return [
  'active' => true,
  'clearcache' => true,
  'reinstall' => false,
  'prio' => 1,
];
```
  
  
#### ToDo
* Update plugin
* Composer install
* Set plugin config
* Export plugin config
* Create plugin for shopware 5.1 or lower

