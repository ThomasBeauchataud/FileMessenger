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

namespace TBCD\Messenger\FileTransport;

use League\Flysystem\Filesystem;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use TBCD\Messenger\FileTransport\AdapterFactory\ChainedAdapterFactory;
use TBCD\Messenger\FileTransport\AdapterFactory\FilesystemAdapterFactoryInterface;

final class FileTransportFactory implements TransportFactoryInterface
{

    private FilesystemAdapterFactoryInterface $filesystemAdapterFactory;

    public function __construct(FilesystemAdapterFactoryInterface $filesystemAdapterFactory = new ChainedAdapterFactory())
    {
        $this->filesystemAdapterFactory = $filesystemAdapterFactory;
    }


    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $filesystemAdapter = $this->filesystemAdapterFactory->create($dsn, $options);
        $filesystem = new Filesystem($filesystemAdapter);
        return new FileTransport($filesystem, $serializer);
    }

    public function supports(string $dsn, array $options): bool
    {
        return $this->filesystemAdapterFactory->support($dsn, $options);
    }
}