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
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

final class FileReceiver implements ReceiverInterface, ListableReceiverInterface, MessageCountAwareInterface
{

    private FilesystemOperator $filesystemOperator;
    private ?SerializerInterface $serializer;

    public function __construct(FilesystemOperator $filesystemOperator, SerializerInterface $serializer)
    {
        $this->filesystemOperator = $filesystemOperator;
        $this->serializer = $serializer;
    }


    public function get(): iterable
    {
        try {
            foreach ($this->filesystemOperator->listContents('/') as $file) {
                if (false === $file->isFile()) {
                    continue;
                }
                return [$this->getEnvelopeFromFilepath($file->path())];
            }
            return [];
        } catch (FilesystemException $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function ack(Envelope $envelope): void
    {
        $fileReceivedStamp = $envelope->last(FileReceivedStamp::class);
        if (null === $fileReceivedStamp) {
            return;
        }
        try {
            $this->filesystemOperator->delete($fileReceivedStamp->getFilepath());
        } catch (FilesystemException $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function reject(Envelope $envelope): void
    {
        $fileReceivedStamp = $envelope->last(FileReceivedStamp::class);
        if (null === $fileReceivedStamp) {
            return;
        }
        try {
            $this->filesystemOperator->delete($fileReceivedStamp->getFilepath());
        } catch (FilesystemException $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function all(int $limit = null): iterable
    {
        try {
            foreach ($this->filesystemOperator->listContents('/') as $file) {
                if (false === $file->isFile()) {
                    continue;
                }
                yield $this->getEnvelopeFromFilepath($file->path());
            }
        } catch (FilesystemException $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function find(mixed $id): ?Envelope
    {
        try {
            foreach ($this->filesystemOperator->listContents('/') as $file) {
                if (false === $file->isFile() || $file->path() !== $id) {
                    continue;
                }
                return $this->getEnvelopeFromFilepath($file->path());
            }
            return null;
        } catch (FilesystemException $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getMessageCount(): int
    {
        try {
            return count(iterator_to_array($this->filesystemOperator->listContents('/')));
        } catch (FilesystemException $e) {
            throw new TransportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function getEnvelopeFromFilepath(string $filepath): Envelope
    {
        $fileContent = $this->filesystemOperator->read($filepath);
        $envelope = $this->serializer->decode(['body' => $fileContent, 'headers' => []]);
        return $envelope->with(new FileReceivedStamp($filepath));
    }
}