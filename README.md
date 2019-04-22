WebSocket Communication for Laravel
===================

[![Build Status](https://travis-ci.org/katsana/swarm.svg?branch=master)](https://travis-ci.org/katsana/swarm)
[![Latest Stable Version](https://poser.pugx.org/katsana/swarm/v/stable)](https://packagist.org/packages/katsana/swarm)
[![Total Downloads](https://poser.pugx.org/katsana/swarm/downloads)](https://packagist.org/packages/katsana/swarm)
[![Latest Unstable Version](https://poser.pugx.org/katsana/swarm/v/unstable)](https://packagist.org/packages/katsana/swarm)
[![License](https://poser.pugx.org/katsana/swarm/license)](https://packagist.org/packages/katsana/swarm)
[![Coverage Status](https://coveralls.io/repos/github/katsana/swarm/badge.svg?branch=master)](https://coveralls.io/github/katsana/swarm?branch=master)

## Installation

Swarm can be installed via composer:

```
composer require "katsana/swarm"
```

### Configuration

The package will automatically register a service provider.

Next, you need to publish the Swarm configuration file:

```
php artisan vendor:publish --provider="Swarm\SwarmServiceProvider" --tag="config"
```

