FeedFinder
==================

[![GitHub license](https://img.shields.io/github/license/imelgrat/feed-finder.svg?style=flat-square)](https://github.com/imelgrat/feed-finder/blob/master/LICENSE)
[![GitHub release](https://img.shields.io/github/release/imelgrat/feed-finder.svg?style=flat-square)](https://github.com/imelgrat/feed-finder/releases)
[![Total Downloads](https://poser.pugx.org/imelgrat/feed-finder/downloads)](https://packagist.org/packages/imelgrat/feed-finder)
[![GitHub issues](https://img.shields.io/github/issues/imelgrat/feed-finder.svg?style=flat-square)](https://github.com/imelgrat/feed-finder/issues)
[![GitHub stars](https://img.shields.io/github/stars/imelgrat/feed-finder.svg?style=flat-square)](https://github.com/imelgrat/feed-finder/stargazers)

A PHP class for extracting the URLs of RSS (1.0 and 2.0) and ATOM feeds associated to a page, as well as OPML outline documents. 

Developed by [Ivan Melgrati](https://imelgrat.me) 

Requirements
------------

*   PHP >= 5.3.0

Installation
------------

### Composer

The recommended installation method is through
[Composer](http://getcomposer.org/), a dependency manager for PHP. Just add
`imelgrat/feed-finder` to your project's `composer.json` file:

```json
{
    "require": {
        "imelgrat/feed-finder": "*"
    }
}
```

[More details](http://packagist.org/packages/imelgrat/feed-finder) can
be found over at [Packagist](http://packagist.org).

### Manually

1.  Copy `src/feed-finder.php` to your codebase, perhaps to the `vendor`
    directory.
2.  Add the `FeedFinder` class to your autoloader or `require` the file
    directly.

Feedback
--------

Please open an issue to request a feature or submit a bug report. Or even if
you just want to provide some feedback, I'd love to hear. I'm also available on
Twitter as [@imelgrat](https://twitter.com/imelgrat).

Contributing
------------

1.  Fork it.
2.  Create your feature branch (`git checkout -b my-new-feature`).
3.  Commit your changes (`git commit -am 'Added some feature'`).
4.  Push to the branch (`git push origin my-new-feature`).
5.  Create a new Pull Request.
