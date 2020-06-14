# Account (OrbitronDev Service)

Create ONE account where all the information is stored, only ONCE.

**Project**

| [License][license]                  | Versions ([Packagist][packagist])                                                                                                 | Downloads ([Packagist][packagist])                     | Required PHP Version                           |
|-------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------|--------------------------------------------------------|------------------------------------------------|
| [![License][license-icon]][license] | Release: [![Version][release-icon]][packagist]<br>Pre-Release: [![Version (including pre-releases)][pre-release-icon]][packagist] | [![Downloads on Packagist][downloads-icon]][packagist] | [![Required PHP version][php-icon]][packagist] |

**master**-branch (alias stable, latest)

| [Travis CI][travis]                           | [Coveralls][coveralls]                           | [Scrutinizer CI][scrutinizer]                                                                                                     | [Codacy][codacy]                              | [Read the Docs][rtfd]                   |
|-----------------------------------------------|--------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------|-----------------------------------------------|-----------------------------------------|
| [![Travis build][travis-master-icon]][travis] | [![Coveralls][coveralls-master-icon]][coveralls] | [![Scrutinizer build][scrutinizer-master-icon]][scrutinizer]<br>[![Scrutinizer quality][scrutinizer-cc-master-icon]][scrutinizer] | [![Codacy grade][codacy-master-icon]][codacy] | [![Docs build][rtfd-master-icon]][rtfd] |

**develop**-branch (alias nightly)

| [Travis CI][travis]                            | [Coveralls][coveralls]                            | [Scrutinizer CI][scrutinizer]                                                                                                       | [Codacy][codacy]                               | [Read the Docs][rtfd]                    |
|------------------------------------------------|---------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------|------------------------------------------|
| [![Travis build][travis-develop-icon]][travis] | [![Coveralls][coveralls-develop-icon]][coveralls] | [![Scrutinizer build][scrutinizer-develop-icon]][scrutinizer]<br>[![Scrutinizer quality][scrutinizer-cc-develop-icon]][scrutinizer] | [![Codacy grade][codacy-develop-icon]][codacy] | [![Docs build][rtfd-develop-icon]][rtfd] |

[license]: https://github.com/D3strukt0r/generation-2-account-api/blob/master/LICENSE.txt
[packagist]: https://packagist.org/packages/d3strukt0r/generation-2-account-api
[travis]: https://travis-ci.com/D3strukt0r/generation-2-account-api
[coveralls]: https://coveralls.io/github/D3strukt0r/generation-2-account-api
[scrutinizer]: https://scrutinizer-ci.com/g/D3strukt0r/generation-2-account-api/
[rtfd]: https://readthedocs.org/projects/generation-2-account-api/
[codacy]: https://app.codacy.com/manual/D3strukt0r/generation-2-account-api/dashboard

[license-icon]: https://img.shields.io/github/license/D3strukt0r/generation-2-account-api
[release-icon]: https://img.shields.io/packagist/v/d3strukt0r/generation-2-account-api
[pre-release-icon]: https://img.shields.io/packagist/v/d3strukt0r/generation-2-account-api?include_prereleases
[downloads-icon]: https://img.shields.io/packagist/dt/d3strukt0r/generation-2-account-api
[php-icon]: https://img.shields.io/packagist/php-v/d3strukt0r/generation-2-account-api
[travis-master-icon]: https://img.shields.io/travis/com/D3strukt0r/generation-2-account-api/master
[travis-develop-icon]: https://img.shields.io/travis/com/D3strukt0r/generation-2-account-api/develop
[coveralls-master-icon]: https://img.shields.io/coveralls/github/D3strukt0r/generation-2-account-api/master
[coveralls-develop-icon]: https://img.shields.io/coveralls/github/D3strukt0r/generation-2-account-api/develop
[scrutinizer-master-icon]: https://img.shields.io/scrutinizer/build/g/D3strukt0r/generation-2-account-api/master
[scrutinizer-develop-icon]: https://img.shields.io/scrutinizer/build/g/D3strukt0r/generation-2-account-api/develop
[scrutinizer-cc-master-icon]: https://img.shields.io/scrutinizer/quality/g/D3strukt0r/generation-2-account-api/master
[scrutinizer-cc-develop-icon]: https://img.shields.io/scrutinizer/quality/g/D3strukt0r/generation-2-account-api/develop
[rtfd-master-icon]: https://img.shields.io/readthedocs/generation-2-account-api/stable
[rtfd-develop-icon]: https://img.shields.io/readthedocs/generation-2-account-api/latest
[codacy-master-icon]: https://img.shields.io/codacy/grade/6cb0a9563bc9441b937d5246c895a082/master
[codacy-develop-icon]: https://img.shields.io/codacy/grade/6cb0a9563bc9441b937d5246c895a082/develop

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing
purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

What things you need to install the software and how to install them

```
Webserver (PHP 7.2+)
Database (e. g. MySql)
Mailserver
```

### Installing

A step by step series of examples that tell you have to get a development env running

Clone the project from github

```bash
$ git clone https://github.com/D3strukt0r/service-account
```

Setup the project with composer

```bash
$ composer install --no-dev --optimize-autoloader
```

Next, rename `.env.dist` to `.env` and change following parameters:

```
RECAPTCHA_PUBLIC_KEY=... (ReCaptcha)
RECAPTCHA_PRIVATE_KEY=... (ReCaptcha)

APP_ENV=prod
APP_SECRET=...

DATABASE_URL=... (Accessing databse)
MAILER_URL=... (To send emails)
```

## Built With

* [PHP](https://www.php.net) - Programming Language
* [Composer](https://getcomposer.org) - PHP Package manager
* [Symfony](https://symfony.com) - PHP Framework
* [SwiftMailer](https://swiftmailer.symfony.com) - PHP Mailer
* [Doctrine](https://www.doctrine-project.org) - PHP Database accessing
* [Twig](https://twig.symfony.com) - PHP Templating service
* [ReCaptcha](https://www.google.com/recaptcha) - Captcha service from Google
* [Bootstrap](https://getbootstrap.com) - Theme used in this service
* [Unify](https://wrapbootstrap.com/theme/unify-responsive-website-template-WB0412697) - Theme used in this service
* [Travis CI](https://travis-ci.com) - Automatic CI (Testing) / CD (Deployment)
* [Docker](https://www.docker.com) - Building a Container for the Server

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull
requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the
[tags on this repository](https://github.com/D3strukt0r/generation-2-account-api/tags). 

## Authors

* **Manuele Vaccari** - [D3strukt0r](https://github.com/D3strukt0r) - *Initial work*

See also the list of [contributors](https://github.com/D3strukt0r/generation-2-account-api/contributors) who
participated in this project.

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE.txt](LICENSE.txt) file for
details.

## Acknowledgments

* Hat tip to anyone whose code was used
* Inspiration
* etc
