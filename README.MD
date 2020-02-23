## GitWatcher

GitWatcher is a assembly of git hook script.

If you want to use it, just add some script in your composer.json file.

```bash
composer require phpple/gitwatcher --dev
```

```
"scripts": {
    "post-autoload-dump": [
      "@php -r \"file_exists('vendor/phpple/gitwatcher/bin/hooks/pre-commit') && (is_dir('.git/hooks')) && copy('vendor/phpple/gitwatcher/bin/hooks/pre-commit', '.git/hooks/pre-commit') && chmod('.git/hooks/pre-commit', 0755);\""
    ]
}
```