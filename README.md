# Ector CLI

A composer CLI tool that provides helpers and tools to manage and develop with Prestashop Ector theme.

## Installation

```bash
composer require buggyzap/ector_cli
```

## Usage

```bash
vendor/bin/ector_cli
```

## Develop

Run locally to develop the CLI

### Start container

```bash
docker-compose up -d
```

### Execute commands

```bash
docker-compose exec app bin/ector_cli
```

## Available commands

### `migrate:magento`

Migrate the Magento permalinks to Prestashop urls and create 301 redirects dinamically
