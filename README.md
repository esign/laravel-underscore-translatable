# Make Eloquent models translatable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/esign/laravel-underscore-translatable.svg?style=flat-square)](https://packagist.org/packages/esign/laravel-underscore-translatable)
[![Total Downloads](https://img.shields.io/packagist/dt/esign/laravel-underscore-translatable.svg?style=flat-square)](https://packagist.org/packages/esign/laravel-underscore-translatable)
![GitHub Actions](https://github.com/esign/laravel-underscore-translatable/actions/workflows/main.yml/badge.svg)

This package allows you to make eloquent models translatable by using separate columns for each language, e.g. `title_nl` and `title_en`. This package is heavily inspired by Spatie's [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable).

## Installation

You can install the package via composer:

```bash
composer require esign/laravel-underscore-translatable
```

## Usage

### Preparing your model

To make your model translatable you need to use the `Esign\UnderscoreTranslatable\UnderscoreTranslatable` trait on the model.
Next up, you should define which fields are translatable by adding a public `$translatable` property.

```php
use Esign\UnderscoreTranslatable\UnderscoreTranslatable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use UnderscoreTranslatable;

    public $translatable = ['title'];
}
```

Your database structure should look like the following:
```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title_nl')->nullable();
    $table->string('title_fr')->nullable();
    $table->string('title_en')->nullable();
});
```

### Retrieving translations
To retrieve a translation in the current locale you may use the attribute you have defined in the `translatable` property. Or you could use the `getTranslation` method:
```php
$post->title
$post->getTranslation('title')
```

To retrieve a translation in a specific locale you may use the fully suffixed attribute or pass the locale to the `getTranslation` method:
```php
$post->title_nl
$post->getTranslation('title', 'nl')
```

### Using a fallback
This package allows you to return the value of an attribute's `fallback_locale` defined in the `config/app.php` of your application.

The third `useFallbackLocale` parameter of the `getTranslation` method may be used to control this behaviour:
```php
$post->title_en = 'Your first translation';
$post->title_nl = null;
$post->getTranslation('title', 'nl', true); // returns 'Your first translation'
$post->getTranslation('title', 'nl', false); // returns null
```

Or you may use dedicated methods for this:
```php
$post->title_en = 'Your first translation';
$post->title_nl = null;
$post->getTranslationWithFallback('title', 'nl'); // returns 'Your first translation'
$post->getTranslationWithoutFallback('title', 'nl'); // returns null
```

### Setting translations

To set the translation for the current locale you may use the attribute you have defined in the `translatable` property. Or you could pass it immediately when creating a model:
```php
$post->title = 'Your first translation';

Post::create([
    'title' => 'Your first translation',
]);
```

You may also use the `setTranslation` method:
```php
$post->setTranslation('title', 'en', 'Your first translation');
$post->setTranslation('title', 'nl', 'Jouw eerste vertaling');
```

You could also set multiple translations at once using the `setTranslations` method or immediately passing them along when creating a model:
```php
$post->setTranslations('title', [
    'en' => 'Your first translation',
    'nl' => 'Jouw eerste vertaling',
]);

Post::create([
    'title' => [
        'en' => 'Your first translation',
        'nl' => 'Jouw eerste vertaling',
    ],
]);
```

This package does not persist translations to the database, so don't forget to save your model:
```php
$post->setTranslation('title', 'en', 'Your first translation');
$post->save();
```

### Defining accessors and mutators
You're able to define accessors just like you're used to in Laravel:
```php
public function getTitleAttribute($value): string
{
    return ucfirst($value);
}
```

The same goes for mutators:
```php
public function setTitleAttribute($value, $locale): void
{
    $this->attributes['title_' . $locale] = strtolower($value);
}
```


## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
