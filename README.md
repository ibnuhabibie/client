# Laracatch Client

Laracatch is a customizable error page for applications running on Laravel 5.5 and higher. It allows you to catch both browser and console exceptions, and optionally share them on [Laracatch](https://laracatch.com), or a self-hosted version of the [Laracatch Server](https://github.com/laracatch/server).

## Documentation

### Installation

Laracatch can be installed via composer:

```
composer require laracatch/client
```

#### Laravel 5.5–5.8

In Laravel versions 5.5–5.7 and older versions of 5.8 you will additionally have to modify the `app/Exceptions/Handler.php` file to load Laracatch in place of the default Whoops handler. You should also remove `filp/whoops` from your `composer.json` to avoid conflicts.

To load Laracatch, add the following method to the `Handler.php` file:

```php
protected function whoopsHandler()
{
    try {
        return app(\Whoops\Handler\HandlerInterface::class);
    } catch (\Illuminate\Contracts\Container\BindingResolutionException $e) {
        return parent::whoopsHandler();
    }
}
```

#### Laravel 6.0+

In Laravel versions from 6.0 upwards, you will additionally have to disable the default Ignition error handler.

### Configuration

The behaviour of Laracatch can be adjusted via the configuration file. For example, you can exclude collection of events or queries.

If you wish to configure Laracatch you may publish the configuration file, which will be located at `config/laracatch.php`

```
php artisan vendor:publish --provider="Laracatch\Client\LaracatchServiceProvider"
```

#### Disabling Laracatch

By default, Laracatch is enabled only when the `debug` key in `config/app.php` is set to `true`.

If you wish to disable Laracatch specifically, without setting the `debug` flag, you may set the `enabled` key in `config/laracatch.php` to `false`.

#### Opening Files In Your Editor

When viewing a stack trace on the Laracatch error page, file paths will be displayed at the top of the opened entry. Next to the path will be a pencil icon. Clicking the pencil icon will attempt to open the file in your chosen editor.

The editor used will be based on your configuration. You may set your editor by adjusting the `code_editor` key in `config/laracatch.php`.

The available options for this key are `phpstorm`, `vscode`, `vscode-insiders`, `sublime`, and `atom`.

#### Remote Server Support

When developing using remote servers (including local tools such as Homestead or Docker), by default Laracatch will be unable to determine the location of files in the stack trace.

To allow Laracatch to correctly find the files, you will need to configure the file paths for your local and remote environments.

Set the `file_paths.remote` key in `config/laracatch.php` to the full path (not URL) of your project folder on the remote filesystem, i.e. Homestead, Docker, or a cloud service.

Set the `file_paths.local` key in `config/laracatch.php` to the full path (not URL) of your project folder on your local filesystem, i.e. where your IDE or editor accesses the files.

This is unnecessary and you can leave the options blank if you are serving your project from the same filesystem as you are developing on.

### Display API, Artisan and Job Exceptions

In addition to catching HTTP exceptions, Laracatch also allows you to inspect exceptions that occur in other parts of your application, such as in API routes, Artisan commands or background Jobs. To do this, you should set the `storage.enabled` key to `true` in the `config/laracatch.php` config file. Once you have done this, Laracatch will store the exceptions and display them in a separate **Navigator** webpage, which is available at `_laracatch/navigator` by default.

The available storage drivers are:

* `file`
* `pdo` (you will need to run migrations first)
* `redis` (requires [Predis](http://github.com/nrk/predis))

By default, Laracatch will retain stored exceptions for 24 hours, after which they will be automatically removed.

The retention period can be set via the `storage.retention` key in `config/laracatch.php`. The retention period is defined in hours.

To manually clear all stored errors you may use the `laracatch:clear` artisan command, or click the **Delete All** button in the Navigator.

### Sharing Errors

You can share your local error publicly via a Laracatch Server. Sharing your error with the official Laracatch Server will always be free. You may also self-host a [Laracatch Server](https://github.com/laracatch/server) to store and share your errors.

After you have shared your error, two links will be returned to you: a public link and an admin link.

The public link should be used to share errors with co-workers or others whom you wish to only have read access to the error.

The admin link offers control over the error, such as deleting it, and should be shared only with those who should have such access.

### Controlling Collected Data

You have full control over what data is collected by Laracatch. To configure this collection, you should adjust the relevant keys in `config/laracatch.php`.

#### Collectors
Collectors can be switched on and off via the following `collector` keys:

##### Dumps
When the `dumps` key is `true`, data passed into the `dump()` or `ddd()` functions will be included in the error's Debug tab.

##### Breadcrumbs
When the `breadcrumbs` key is `true`, data collected via the `Laracatch::breadcrumb()` method will be included in the error's Debug tab.

##### Logs
When the `logs` key is `true`, Laracatch will collect any logs and display them in the error's Debug tab.

##### Events
When the `events` key is `true`, Laracatch will collect any events that fire and display them in the error's Debug tab.

##### Queries
When the `queries` key is `true`, Laracatch will collect any queries that were run and display them in the error's Queries tab.

#### Additional Data
Laracatch can also collect some additional data. Configuring this is done using the `data_providers` key:

##### Git Information
When the `collect_git_information` key is `true`, Laracatch will attempt to collect information about the git repository for the project, such as its current commit and whether changes have been made.

##### Query Bindings
When the `report_query_bindings` key is `true`, Laracatch will substitute bound data in captured SQL queries, e.g. replacing `where my_column = ?` with `where my_column = 1`. This has no effect if query collection is switched off.

##### View Data
When the `report_view_data` key is `true`, Laracatch will collect any data passed into the current view.

#### Anonymizing IPs

By default, Laracatch does not collect and send information about the IP address of your application users. If you want to collect this information, you can set the `anonymize_ips` option in `data_providers` to `false`.

### Authenticated Users

When Laracatch collects data, the properties of the authenticated user will also be captured. User data is collected via the `toArray` method on your `User` model. By default `toArray` will exclude any attributes that are marked as hidden on the model.

If you need more control over which user data you want to send to Laracatch, you can customize this by adding a `toLaracatch` method to your `User` model. When the `toLaracatch` method is defined, Laracatch will use this in place of `toArray`.

```php
class User extends Model {
    // …

    public function toLaracatch(): array {
        // Only `id` will be sent to Laracatch.
        return [
            'id' => $this->id
        ];
    }
}
```

### Breadcrumbs

Laracatch supports adding "breadcrumbs" to your application. This allows you to leave a trail of useful information that can be followed in case of an error — such as what parameters were passed to a method, or which branch was taken in an `if` statement — without affecting normal execution.

When breadcrumbs are left, Laracatch will display them in chronological order in the Debug tab of the error page.

To add a breadcrumb, you can use the Laracatch facade:

```php
use Laracatch\Facades\Laracatch;
use Psr\Log\LogLevel;

Laracatch::breadcrumb('A useful message.', LogLevel::DEBUG, func_get_args());
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
