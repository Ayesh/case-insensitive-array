# Case Insensitiv Array

## Synopsis
A class implementing **ArrayAccess**, **Countable**, and **Iterator** interfaces, and allows you to set, get, count, iterate, and validate while enforcing the keys to be case insensitive. 

For example, suppose you have to store a set of HTTP headers. By definition, HTTP headers are case insensitive. With this class, you can peacefully set the same array key-pair combination any number as you feel fit, but the data set will remain consistent. 

    $array = new Ayesh\CaseInsensitiveArray\Strict();
    $array['x-frame-options'] = 'DENY';
    $array['X-FRAME-options'] = 'SAMEORIGIN';
    echo $array['X-Frame-Options'] = 'SAMEORIGIN';
From the example above, notice how the array values are set two times with two keys with different case. In the `echo` line, the value is accessed in CamelCase, but you get the same value regardless of your querying keys case. 

## Prerequisites

 - PHP 5.4 or later
## Installing
The simplest way would be to install using [composer](https://getcomposer.org). 

    composer require ayesh/case-insensitive-array
If, for some reason, you can't use Composer, or don't want to (Come on!), you can integrate the class with your current `PSR-4` autoloader by mapping `Ayesh\CaseInsensitiveArray` namespace to the repository's `src` folder. 

##Usage
This class aims to take away the fact that you are using an object. Simply use it as an array. 

**Initialize with an array**
This is optional, but if you already have an array that you need to "import", instantiate the class with that array. 

    $source = [
      'x-frame-options' => 'Deny',
      'X-FRAME-OPTIONS' => 'SAMEORIGIN'
    ];
    
    $array = new Ayesh\CaseInsensitiveArray\Strict($source);
    // Your initial array is now indexed. That was optional. You can now set/get values freely, as you would do with a regular array.
    echo $array['X-Frame-OPTIONS']; // 'SAMEORIGIN'
    echo $array['X-FRAME-opTIONS']; // 'SAMEORIGIN'
    unset($array['x-frame-options']);
    var_dump(isset($array['X-Frame-Options'])); // false

**Iterate**
You can iterate the array object using foreach(). The exact key and value will be returned. 

    $array = new Ayesh\CaseInsensitiveArray\Strict($source);
    $array['x-frame-options'] = 'SameOrigin';
    $array['X-Frame-Options'] = 'Deny'; // Notice the Came Case here.
    $array['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains; preload';
    foreach ($array as $key => $value) {
      echo "{$key}: {$value}\r\n";
    }
    // Output (notice how the case is preserved in X-Frame-Options):
    // X-Frame-Options: Deny
    // Strict-Transport-Security: max-age=31536000; includeSubDomains; preload

You can also iterate the array with the same [Iterator](http://php.net/manual/en/class.iterator.php) methods. For a near-perfect array imitation, what we need is [ArrayIterator](http://php.net/manual/en/class.arrayiterator.php). However, it is not implemented in the current version. I would gladly work with you if you'd like to help. As of now, my scope is to have 2 classes, Strict and Union that gives basic array access, and `foreach()` compatibility. 

##Development and tests
All issues are PRs are welcome. Travis CI and PHPUnit tests are included. If you are adding new features, please make sure to add the test coverage.

##Credits
By [Ayesh Karunaratne](https://ayesh.me).

