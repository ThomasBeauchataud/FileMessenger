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

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

final class FileSender implements SenderInterface
{

    private FilesystemOperator $filesystemOperator;
    private SerializerInterface $serializer;

    public function __construct(FilesystemOperator $filesystemOperator, SerializerInterface $serializer)
    {
        $this->filesystemOperator = $filesystemOperator;
        $this->serializer = $serializer;
    }


    public function send(Envelope $envelope): Envelope
    {
        try {

            $filenameStamp = $envelope->last(FilenameStamp::class);

            if (null === $filenameStamp || $this->filesystemOperator->has($filenameStamp->getFilename())) {
                $filename = uniqid() . '.msg';
            } else {
                $filename = $filenameStamp->getFilename();
            }

            $data = $this->serializer->encode($envelope);
            $fileContent = $data['body'];
            $this->filesystemOperator->write($filename, $fileContent);

            $envelope->with(new TransportMessageIdStamp($filename));
            return $envelope;

        } catch (FilesystemException $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }
}