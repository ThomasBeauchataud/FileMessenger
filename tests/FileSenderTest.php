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

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use TBCD\Messenger\FileTransport\FileSender;

class FileSenderTest extends TestCase
{

    protected function setUp(): void
    {
        $tmpDirectory = sys_get_temp_dir() . '/symfony-test';
        foreach (scandir($tmpDirectory) as $file) {
            $filepath = "$tmpDirectory/$file";
            if (is_file($filepath)) {
                unlink($filepath);
            }
        }
    }

    public function testSend(): void
    {
        $tmpDirectory = sys_get_temp_dir() . '/symfony-test';
        $content = uniqid();
        $envelope = new Envelope(new MessageTest($content));
        $filesystem = new Filesystem(new LocalFilesystemAdapter($tmpDirectory));
        $serializer = new SerializerTest();
        $fileReceiver = new FileSender($filesystem, $serializer);
        $envelope = $fileReceiver->send($envelope);
        $transportMessageIdStamp = $envelope->last(TransportMessageIdStamp::class);
        $this->assertNotNull($transportMessageIdStamp);
        $filename = $transportMessageIdStamp->getId();
        $filepath = "$tmpDirectory/$filename";
        $this->assertEquals($content, file_get_contents($filepath));
    }
}