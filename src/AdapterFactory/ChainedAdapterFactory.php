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
use Symfony\Component\Messenger\Exception\InvalidArgumentException;

final class ChainedAdapterFactory implements FilesystemAdapterFactoryInterface
{

    private array $filesystemAdapterFactories;

    public function __construct()
    {
        $this->filesystemAdapterFactories = [
            new LocalAdapterFactory(),
            new SftpAdapterFactory(),
            new FtpAdapterFactory()
        ];
    }


    public function support(string $dsn, array $options): bool
    {
        foreach($this->filesystemAdapterFactories as $filesystemAdapterFactory) {
            if ($filesystemAdapterFactory->support($dsn, $options)) {
                return true;
            }
        }

        return false;
    }

    public function create(string $dsn, array $options): FilesystemAdapter
    {
        foreach($this->filesystemAdapterFactories as $filesystemAdapterFactory) {
            if ($filesystemAdapterFactory->support($dsn, $options)) {
                return $filesystemAdapterFactory->create($dsn, $options);
            }
        }

        throw new InvalidArgumentException('Unable to find a filesystem adapter supporting the given dsn');
    }
}