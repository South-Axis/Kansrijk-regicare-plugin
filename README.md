# Kansrijk-regicare-plugin

## Prerequisites

* php 8.1
* composer (latest or `^2.3` is preferred)

## Installation

```bash
composer install
```

## Distribution

The following command generates a `regicare_api_plugin_wp.zip` archive that has a vendor without development dependencies.

```bash
composer install -o --no-dev
composer archive --format=zip --file=regicare_api_plugin_wp
```

