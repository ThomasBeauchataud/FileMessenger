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

namespace TBCD\Messenger\FileTransport\Tests;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class SerializerTest implements SerializerInterface
{

    public function decode(array $encodedEnvelope): Envelope
    {
        return new Envelope(new MessageTest($encodedEnvelope['body']));
    }

    public function encode(Envelope $envelope): array
    {
        return [
            'body' => $envelope->getMessage()->getContent(),
            'headers' => []
        ];
    }
}