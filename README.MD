## GitWatcher

GitWatcher is a assembly of git hook script.

If you want to use it, just add some script in your composer.json file.

```bash
composer require phpple/gitwatcher --dev
```

```
"scripts": {
    "post-autoload-dump": [
      "Phpple\GitWatcher\Tool\Composer::phpostAutoloadDump"
    ]
}
```