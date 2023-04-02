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
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\SetupableTransportInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

final class FileTransport implements TransportInterface, MessageCountAwareInterface, ListableReceiverInterface, SetupableTransportInterface
{

    private FilesystemOperator $filesystemOperator;
    private SerializerInterface $serializer;
    private FileReceiver $receiver;
    private FileSender $sender;

    public function __construct(FilesystemOperator $filesystemOperator, SerializerInterface $serializer = new PhpSerializer())
    {
        $this->filesystemOperator = $filesystemOperator;
        $this->serializer = $serializer;
    }

    public function get(): iterable
    {
        return $this->getReceiver()->get();
    }

    public function ack(Envelope $envelope): void
    {
        $this->getReceiver()->ack($envelope);
    }

    public function reject(Envelope $envelope): void
    {
        $this->getReceiver()->reject($envelope);
    }

    public function getMessageCount(): int
    {
        return $this->getReceiver()->getMessageCount();
    }

    public function all(int $limit = null): iterable
    {
        return $this->getReceiver()->all($limit);
    }

    public function find(mixed $id): ?Envelope
    {
        return $this->getReceiver()->find($id);
    }

    public function send(Envelope $envelope): Envelope
    {
        return $this->getSender()->send($envelope);
    }

    public function setup(): void
    {
        try {
            $this->filesystemOperator->createDirectory('/');
        } catch (FilesystemException) {
            return;
        }
    }

    private function getReceiver(): FileReceiver
    {
        return $this->receiver ??= new FileReceiver($this->filesystemOperator, $this->serializer);
    }

    private function getSender(): FileSender
    {
        return $this->sender ??= new FileSender($this->filesystemOperator, $this->serializer);
    }
}