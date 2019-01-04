# XanUtility for Concrete5
[![Build Status](https://travis-ci.org/Xanweb/xan_utility.svg?branch=master)](https://travis-ci.org/Xanweb/xan_utility)
[![](https://img.shields.io/github/license/xanweb/xan_utility.svg)](https://github.com/xanweb/xan_utility/blob/master/LICENSE)
[![](https://img.shields.io/packagist/v/xanweb/xan_utility.svg)](https://packagist.org/packages/xanweb/xan_utility)

A Collection of useful utilities for concrete5 developers.

## How To Use

Add the start instruction in your app.php under /application/bootstrap/ folder
```php
<?php
/* @var Concrete\Core\Application\Application $app */
/* @var Concrete\Core\Console\Application $console only set in CLI environment */

XanUtility\Runner::boot();
```

A Full documentation will be available soon.


## Authors

See the list of [contributors][] who participate(s) in this project.


## License

XanUtility for Concrete5 is licensed under the MIT License - see the [LICENSE][] file for details


[contributors]: https://github.com/xanweb/xan_utility/contributors
[LICENSE]: https://github.com/xanweb/xan_utility/blob/master/LICENSE
