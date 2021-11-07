![alt text](https://marshmallow.dev/cdn/media/logo-red-237x46.png "marshmallow.")

# Laravel Nova Spotlight

This package customizes Laravel Nova with Spotlight. This is a WIP.

### Installation

```bash
composer require marshmallow/nova-spotlight
```

### Vendor Publish

This command publishes a copy of the Nova Layout to the vendor folder, copied from (nova.resources.views.layout)
```
php artisan vendor:publish --provider="Marshmallow\NovaSpotlight\NovaSpotlightServiceProvider" --tag="views" --force
```

It adds these script:

```
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireScripts
    @livewireStyles
```

```
@livewire('livewire-ui-spotlight')
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email stef@marshmallow.dev instead of using the issue tracker.

## Credits

-   [All Contributors](../../contributors)

Spotlight is made by [Philo Hermans](https://github.com/philoNL)
See https://github.com/wire-elements/spotlight

-   [Wire Elements](https://github.com/wire-elements/)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
