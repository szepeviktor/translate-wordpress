<!-- logo -->
<img src="https://cdn.weglot.com/logo/logo-hor.png" height="40" />

# WordPress Plugin V2 - In development

[![Weglot Slack][slack-image]][slack-url]
[![Build Status][travis-image]][travis-url]


## Requirements

You can download plugin on [WordPress.org](https://wordpress.org/plugins/weglot)

- PHP version 5.4 and later
- Weglot API Key, starting at [free level](https://dashboard.weglot.com/register)


## Developers

To install the development environment:
- `git clone https://github.com/weglot/translate-wordpress.git weglot`
- `cd weglot && composer install && npm install`


[travis-image]: https://api.travis-ci.com/weglot/weglot-wordpress.svg?branch=dev
[travis-url]: https://travis-ci.com/weglot/weglot-wordpress

[slack-image]: https://weglot-community.now.sh/badge.svg
[slack-url]: https://weglot-community.now.sh/

## Before each release : test the following

- Switcher button ( functioning and links )
- Internal links must have the language prefix
- Switch lang it with the items in the menu
