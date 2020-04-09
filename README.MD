## GitWatcher

GitWatcher is a assembly of git hook script.

If you want to use it, just add some script in your composer.json file.

```bash
composer require phpple/gitwatcher --dev
```

```
"scripts": {
    "post-autoload-dump": [
      "Phpple\\GitWatcher\\Composer::postAutoloadDump"
    ]
}
```

## Watchers

Your can find the watchers in `src/Watcher`

### git_version

Check whether the git's version greater than the custom version. The default mininum version is 2.2.0.

### committer

Check the validity of committer's information.For example, the committer's email's extension.

#### example
```json
{
  "committer": {
    "email_extension": "live.com"
  }
}
```
Inspect will be failed when your local git config about user.email is not end with live.com.


### composer

Check the validity of `composer.json`.

#### example

```json
{
  "composer": {}
}
```
Inspect will be failed when any repository's version of `require` field's is not a constant.

#### Illegal composer.json
```json
{
  "require": {
    "fideloper/proxy": "^4.3"
  }
}
``` 

#### Good composer.json
```json
{
  "require": {
    "fideloper/proxy": "4.3.0"
  }
}
```

### StandardWatcher

Check the validity of your code by phpcs.

#### example
```json
{
  "standard": {
    "phpcs": "vendor/bin/phpcs",
    "target": "app/,src/",
    "mode": "all",
    "options": {
      "standard": "{$project.root}assets/rules/phpdefault.xml",
      "colors": true,
      "s": true
    }
  }
}
```

* `phpcs`: where the phpcs bin file is
* `target`: the dir will be checked by phpcs
* `mode`: [all|update] 
    * `all`:check all the files in `target`
    * `update`:only check update files in `target`
* `options`: the options of phpcs
    * `standard`: `{$project.root}` refer to the root dir of gitwatcher.
    * `colors`: print texts with color
    * `s`: print the sniffer of the problem
    * ... see the help of phpcs

## Custom configure

If your want to custom the configure, you can add a file(`gitwatch.json`) in your site root dir. such as
```json
{
  "@extend": "default",
  "standard": {
    "options": {
      "colors": false
    }
  }
}
```