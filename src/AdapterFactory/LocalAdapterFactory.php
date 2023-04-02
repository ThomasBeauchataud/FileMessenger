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
use League\Flysystem\Local\LocalFilesystemAdapter;

final class LocalAdapterFactory implements FilesystemAdapterFactoryInterface
{

    public function support(string $dsn, array $options): bool
    {
        $urlData = parse_url($dsn);
        return isset($urlData['scheme']) && $urlData['scheme'] === 'local';
    }

    public function create(string $dsn, array $options): FilesystemAdapter
    {
        $path = $options['path'] ?? parse_url($dsn)['path'] ?? '.';
        return new LocalFilesystemAdapter($path);
    }
}