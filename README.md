# Unyson-CLI

## [Unyson WordPress Framework](https://github.com/ThemeFuse/Unyson) terminal interface

## Table of contents
 -  [Installation](#installation)
 -  [Get Started](#get-started)
 -  [Commands list](#commands-list)
    -  [Unyson commands](#unyson-commands)
        -  [Install](#install)
        -  [Uninstall](#uninstall)
        -  [Version](#version)
        -  [Deactivate](#deactivate)
        -  [Get](#get)
        -  [Is Installed](#is-installed)
        -  [Path](#path)
        -  [Toggle](#toggle)
        -  [Update](#update)
        -  [Versions](#versions)

## Installation
 1. Make sure you have [WP-CLI](http://wp-cli.org/) installed
    
    Here you can find instructions: https://make.wordpress.org/cli/handbook/installing/
  
 2. Update **WP-CLI**: `wp cli update`.
    
 3. Install **Unyson-CLI** package.
    
    `wp package install https://github.com/ThemeFuse/Unyson-CLI unyson-cli`
    
## Get Started
All **Unyson-CLI** commands starts with `wp unyson` followed by command name.

Use `wp unyson install` to install the [Unyson](https://wordpress.org/plugins/unyson/) plugin.
Unyson `wp unyson install` uses the `wp plugin install <plugin-name>` command, so it is and alias for 
`wp plugin install unyson` command. 


## Commands list

### Unyson commands

Provided commands below refer to unyson plugin management, the commands are required to be executed in a directory
with WordPress installation available or use the global parameter `--path` to specify in what directory where the
command will be executed.

```
wp unyson install --path=/var/www/wordpress/
```

#### Install `wp unyson install`
##### Command
```
wp unyson install [--version=<version>] [--force] [--activate] [--activate-network]
```
    
##### Description
Install unyson.

##### Options

 -  `[--version=<version>]`
 
    If set, get that particular version from wordpress.org, instead of the stable version.
    
 -  `[--force]`
 
    If set, the command will overwrite any installed version of unyson, without prompting for confirmation.
    
 -  `[--activate-network]`
    
    If set, unyson will be network activated immediately after install
    
##### EXAMPLES

Install the latest version from wordpress.org and activate
```
$ wp unyson install --activate
Installing unyson (2.6.16)
Downloading install package from https://downloads.wordpress.org/plugin/unyson.2.6.16.zip...
Using cached file '/home/vagrant/.wp-cli/cache/plugin/unyson-2.6.16.zip'...
Unpacking the package...
Installing the plugin...
Plugin installed successfully.
Activating 'unyson'...
Plugin 'unyson' activated.
Success: Installed 1 of 1 plugins.
```

---

#### Uninstall `wp unyson uninstall`
##### Command
```
wp unyson uninstall [--deactivate] [--skip-delete]
```
    
##### Description
Uninstall Unyson.

##### Options

 -  `[--deactivate]`
 
    Deactivate unyson before uninstalling. Default behavior is to warn and skip if unyson is active.
    
 -  `[--skip-delete]`
 
    If set, the unyson files will not be deleted. Only the uninstall procedure will be run.
    
##### EXAMPLES

Install the latest version from wordpress.org and activate
```
$ wp unyson uninstall
Uninstalled and deleted 'unyson' plugin.
Success: Uninstalled 1 of 1 plugins.
```

---

#### Version `wp unyson version`
##### Command
```
wp unyson version
```
    
##### Description
Prints Unyson current version.
    
##### EXAMPLES

Print current version
```
$wp unyson version
2.6.15
```

---

#### Activate `wp unyson activate`
##### Command
```
wp unyson activate [--network]
```
    
##### Description
Activate Unyson.

##### Options

 -  `[--network]`
 
    If set, unyson will be activated for the entire multisite network.
    
##### EXAMPLES

Activate unyson
```
$ wp unyson activate
Plugin 'unyson' activated.
Success: Activated 1 of 1 plugins.
```

Activate plugin in entire multisite network
```
$ wp unyson activate --network
Plugin 'unyson' network activated.
Success: Network activated 1 of 1 plugins.
```

---

#### Deactivate `wp unyson deactivate`
##### Command
```
wp unyson deactivate [--uninstall] [--network]
```
    
##### Description
Deactivate unyson.

##### Options

 -  `[--uninstall]`
 
    Uninstall unyson after deactivation.
   
 -  `[--network]`
 
    If set, unyson will be deactivated for the entire multisite network.
    
##### EXAMPLES

Deactivate unyson
```
$ wp unyson deactivate
Plugin 'unyson' deactivated.
Success: Deactivated 1 of 1 plugins.
```

---

#### Get `wp unyson get`
##### Command
```
wp unyson get [--field=<field>] [--fields=<fields>] [--format=<format>]
```
    
##### Description
Get details about current unyson installation.

##### Options

 -  `[--field=<field>]`
 
    Instead of returning the whole unyson data, returns the value of a single field.
   
 -  `[--fields=<fields>]`
 
    Limit the output to specific fields. Defaults to all fields.
    
 -  `[--format=<format>]`
 
    Render output in a particular format.
    
    Formats: `table`, `csv`, `json`, `yaml`
    
    Default: `table`
    
##### EXAMPLES

Get Unyson data
```
+-------------+------------------------------------------------------------------------------------------------------------------------------------+
| Field       | Value                                                                                                                              |
+-------------+------------------------------------------------------------------------------------------------------------------------------------+
| name        | unyson                                                                                                                             |
| title       | Unyson                                                                                                                             |
| author      | ThemeFuse                                                                                                                          |
| version     | 2.6.15                                                                                                                             |
| description | A free drag & drop framework that comes with a bunch of built in extensions that will help you develop premium themes fast & easy. |
| status      | active                                                                                                                             |
+-------------+------------------------------------------------------------------------------------------------------------------------------------+
```

Get Unyson data as `json`
```
$ wp unyson get --format=json
{"name":"unyson","title":"Unyson","author":"ThemeFuse","version":"2.6.15","description":"A free drag & drop framework that comes with a bunch of built in extensions that will help you develop premium themes fast & easy.","status":"active"}
```

---

#### Is Installed `wp unyson is_installed`
##### Command
```
wp unyson is_installed
```
    
##### Description
Check if unyson is installed.
    
##### EXAMPLES

Check whether unyson is installed; exit status 0 if installed, otherwise 1
```
$ wp unyson is-installed
$ echo $?
1
```

---

#### Path `wp unyson path`
##### Command
```
wp unyson path [--dir]
```
    
##### Description
Get the path to unyson or to the unyson directory.

##### Options

 -  `[--dir]`
 
    If set, get the path to the closest parent directory, instead of the unyson file.
    
##### EXAMPLES

Get Unyson plugin path
```
$ wp unyson path
/var/www/wordpress/wp-content/plugins/unyson/unyson.php
```

Get Unyson plugin directory
```
$ wp unyson path --dir
/var/www/wordpress/wp-content/plugins/unyson
```

---

#### Status `wp unyson status`
##### Command
```
wp unyson status
```
    
##### Description
See unyson status.
    
##### EXAMPLES

Display unyson status
```
$ wp unyson status
     Plugin unyson details:
         Name: Unyson
         Status: Active
         Version: 2.6.16
         Author: ThemeFuse
         Description: A free drag & drop framework that comes with a bunch of built in extensions that will help you develop premium themes fast & easy.
```

---

#### Toggle `wp unyson toggle`
##### Command
```
wp unyson toggle [--network]
```
    
##### Description
If unyson is active, then it will be deactivated. If unyson is inactive, then it will be activated.

##### Options

 -  `[--network]`
 
    If set, unyson will be toggled for the entire multisite network.
    
##### EXAMPLES

Unyson is currently activated
```
$ wp unyson toggle
     Plugin 'unyson' deactivated.
     Success: Toggled 1 of 1 plugins.
```

Unyson is currently deactivated
```
$ wp plugin toggle unyson
     Plugin 'unyson' activated.
     Success: Toggled 1 of 1 plugins.
```

---

#### Update `wp unyson update`
##### Command
```
wp unyson update [--format=<format>] [--version=<version>]
```
    
##### Description
Update unyson.

##### Options

 -  `[--format=<format>]`
 
    Output summary as table or summary. Defaults to `table`.
 
 -  `[--version=<version>]`
 
    If set, unyson will be updated to the specified version.
    
##### EXAMPLES

Update unyson to version **2.5.4**
```
$ wp unyson update --version=2.5.4
     Installing unyson
     Downloading install package from https://downloads.wordpress.org/plugin/unyson-2.5.4.zip...
     Unpacking the package...
     Installing the plugin...
     Removing the old version of the plugin...
     Plugin updated successfully.
     Success: Updated 1 of 1 plugins.
```

---

#### Upgrade `wp unyson upgrade`
##### Command
```
wp unyson upgrade
```
    
##### Description
Upgrades unyson to the next available version.
    
##### EXAMPLES

Upgrade unyson **2.5.4**
```
$ wp unyson upgrade
     Installing unyson
     Downloading install package from https://downloads.wordpress.org/plugin/unyson-2.5.5.zip...
     Unpacking the package...
     Installing the plugin...
     Removing the old version of the plugin...
     Plugin updated successfully.
     Success: Updated 1 of 1 plugins.
```

---

#### Downgrade `wp unyson dowbgrade`
##### Command
```
wp unyson dowbgrade
```
    
##### Description
Downgrades unyson to the previous version.
    
##### EXAMPLES

Downgrade unyson **2.5.4**
```
$ wp unyson downgrade
     Installing unyson
     Downloading install package from https://downloads.wordpress.org/plugin/unyson-2.5.3.zip...
     Unpacking the package...
     Installing the plugin...
     Removing the old version of the plugin...
     Plugin updated successfully.
     Success: Updated 1 of 1 plugins.
```

---

#### Versions `wp unyson versions`
##### Command
```
wp unyson versions
```
    
##### Description
List all unyson available versions.
    
##### EXAMPLES

List all Unyson versions
```
$ wp unyson versions
  ...
  2.1.6
  2.1.7
* 2.1.8
  2.1.9
  2.1.10
  2.1.11
  ...
```