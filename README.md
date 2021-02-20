# Events

A small tool to check if a password has been leaked against haveibeenpwned.com database.

## Installation
This component requires php >= 7.4. To install it, you can use composer:
```
composer require unique/was-password-pwned
```

## Usage
```php
    $checker = new PasswordChecker();
    $count = $checker->checkPassword( sha1( 'my-password' ) );
    if ( $count ) {

        echo 'Your password has been leaked and found in ' . $count . ' databases.';
    } elseif ( $count === null ) {

        if ( $checker->getLastException() ) {

            echo (string) $checker->getLastException();
        } else {

            echo 'An error has occured.';
        }
    }
```

## haveibeenpwned.com usage and license
Please read https://haveibeenpwned.com/API/v3#AcceptableUse for acceptable use of haveibeenpwned.com service and attribute it according to the author:
https://haveibeenpwned.com/API/v3#License.

