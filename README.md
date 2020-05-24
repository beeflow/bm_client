# Zadanie rekrutacyjne Blue Services - część Kliencka

## Instalacja i konfiguracja
 
```bash
$ composer require "blue-recruitment/bm-clent-bundle @dev"
```

Potem do pliku `config/bundles.php` należy dodać kod:
```php
return [
    ...
    BMClientBundle\Client\BMClientBundle::class => ['all' => true],
];
```

zaś do `config/routes/annotations.yaml`:
```yaml
bm-client-bundle:
    prefix: /items
    resource: "@BMClientBundle/Controller/ItemController.php"
    type: annotation
```

Do parametrów konfiguracyjnych należy dodać jeszcze podstawowy url do API:
```yaml
parameters:
  bm_api_url: 'http://example.com'
```
## Testy

Statyczna analiza kodu opiera się o PHPMess detector i PHP Code Sniffer (dla PSR12).

```bash
$ composer test
```

Aby naprawić kod za pomocą `phpcbf` (tam gdzie się da) wystarczy uruchomić

```bash
$ composer fix
```
