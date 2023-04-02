# FileMessenger

This package is an extension of symfony/messenger component providing transport for files using league/flysystem
component

## Installation

```
composer require tbcd/file-messenger
```

## Usage

This transport can be configured with any of the league/flysystem adapters.
> For now only the sftp, the ftp and the local adapters are implemented

Configure the transport as expected in the messenger config file

```
# config/packages/messenger.yaml

framework:
    messenger:
        transports:
            # https://symfony.com/doc/current/messenger.html#transport-configuration
            sftp:
                dsn: 'sftp://foo:bar@localhost:22/rootPath'
            ftp:
                dsn: 'ftp://foo:bar@localhost:21/rootPath'
            local:
                dsn: 'local://./rootPath'
```
