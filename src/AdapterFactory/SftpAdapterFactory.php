<?php

/*
 * This file is part of the tbcd/cas project.
 *
 * (c) Thomas Beauchataud <thomas.beauchataud@yahoo.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Author Thomas Beauchataud
 * From 02/04/2023
 */

namespace TBCD\Messenger\FileTransport\AdapterFactory;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;

final class SftpAdapterFactory implements FilesystemAdapterFactoryInterface
{

    public function support(string $dsn, array $options): bool
    {
        $urlData = parse_url($dsn);
        return isset($urlData['scheme']) && $urlData['scheme'] === 'sftp';
    }

    public function create(string $dsn, array $options): FilesystemAdapter
    {
        $urlData = parse_url($dsn);
        $path = $options['path'] ?? parse_url($dsn)['path'] ?? '.';
        return new SftpAdapter(new SftpConnectionProvider($urlData['host'], $urlData['user'], $urlData['pass'], null, null, $urlData['port']), $path);
    }
}