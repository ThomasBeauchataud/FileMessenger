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
use TBCD\Messenger\FileTransport\FileReceivedStamp;
use TBCD\Messenger\FileTransport\FileReceiver;

class FileReceiverTest extends TestCase
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

    public function testGet(): void
    {
        $tmpDirectory = sys_get_temp_dir() . '/symfony-test';
        $filename = uniqid();
        $filepath = "$tmpDirectory/$filename";
        $content = uniqid();
        file_put_contents($filepath, $content);

        $filesystem = new Filesystem(new LocalFilesystemAdapter($tmpDirectory));
        $serializer = new SerializerTest();
        $fileReceiver = new FileReceiver($filesystem, $serializer);
        $envelopes = $fileReceiver->get();
        $this->assertCount(1, $envelopes);
        $envelopes = is_array($envelopes) ? $envelopes : [...$envelopes];
        $envelope = array_shift($envelopes);
        $message = $envelope->getMessage();
        $this->assertEquals($content, $message->getContent());

        $fileReceivedStamp = $envelope->last(FileReceivedStamp::class);
        $this->assertNotNull($fileReceivedStamp);
        $this->assertEquals($filename, $fileReceivedStamp->getFilepath());
    }

    public function testAck(): void
    {
        $tmpDirectory = sys_get_temp_dir() . '/symfony-test';
        $filename = uniqid();
        $filepath = "$tmpDirectory/$filename";
        $content = uniqid();
        file_put_contents($filepath, $content);

        $filesystem = new Filesystem(new LocalFilesystemAdapter($tmpDirectory));
        $serializer = new SerializerTest();
        $fileReceiver = new FileReceiver($filesystem, $serializer);
        $envelopes = $fileReceiver->get();
        $this->assertCount(1, $envelopes);
        $envelopes = is_array($envelopes) ? $envelopes : [...$envelopes];
        $envelope = array_shift($envelopes);

        $fileReceiver->ack($envelope);
        $fileContent = file_get_contents($filepath);
        $this->assertFalse($fileContent);
    }

    public function testReject(): void
    {
        $tmpDirectory = sys_get_temp_dir() . '/symfony-test';
        $filename = uniqid();
        $filepath = "$tmpDirectory/$filename";
        $content = uniqid();
        file_put_contents($filepath, $content);

        $filesystem = new Filesystem(new LocalFilesystemAdapter($tmpDirectory));
        $serializer = new SerializerTest();
        $fileReceiver = new FileReceiver($filesystem, $serializer);
        $envelopes = $fileReceiver->get();
        $this->assertCount(1, $envelopes);
        $envelopes = is_array($envelopes) ? $envelopes : [...$envelopes];
        $envelope = array_shift($envelopes);

        $fileReceiver->reject($envelope);
        $fileContent = file_get_contents($filepath);
        $this->assertFalse($fileContent);
    }

    public function testAll(): void
    {
        $tmpDirectory = sys_get_temp_dir() . '/symfony-test';
        $filename = uniqid();
        $filepath = "$tmpDirectory/$filename";
        $content = uniqid();
        file_put_contents($filepath, $content);
        $filename = uniqid();
        $filepath = "$tmpDirectory/$filename";
        $content = uniqid();
        file_put_contents($filepath, $content);
        $filename = uniqid();
        $filepath = "$tmpDirectory/$filename";
        $content = uniqid();
        file_put_contents($filepath, $content);

        $filesystem = new Filesystem(new LocalFilesystemAdapter($tmpDirectory));
        $serializer = new SerializerTest();
        $fileReceiver = new FileReceiver($filesystem, $serializer);
        $envelopes = $fileReceiver->all();
        $this->assertCount(3, [...$envelopes]);
    }

    public function testFind(): void
    {
        $tmpDirectory = sys_get_temp_dir() . '/symfony-test';
        $filename = uniqid();
        $filepath = "$tmpDirectory/$filename";
        $content = uniqid();
        file_put_contents($filepath, $content);

        $filesystem = new Filesystem(new LocalFilesystemAdapter($tmpDirectory));
        $serializer = new SerializerTest();
        $fileReceiver = new FileReceiver($filesystem, $serializer);
        $envelope = $fileReceiver->find($filename);
        $this->assertNotNull($envelope);
        $envelope = $fileReceiver->find(uniqid());
        $this->assertNull($envelope);
    }

    public function testGetMessageCount(): void
    {
        $tmpDirectory = sys_get_temp_dir() . '/symfony-test';
        $filename = uniqid();
        $filepath = "$tmpDirectory/$filename";
        $content = uniqid();
        file_put_contents($filepath, $content);
        $filename = uniqid();
        $filepath = "$tmpDirectory/$filename";
        $content = uniqid();
        file_put_contents($filepath, $content);
        $filename = uniqid();
        $filepath = "$tmpDirectory/$filename";
        $content = uniqid();
        file_put_contents($filepath, $content);

        $filesystem = new Filesystem(new LocalFilesystemAdapter($tmpDirectory));
        $serializer = new SerializerTest();
        $fileReceiver = new FileReceiver($filesystem, $serializer);
        $count = $fileReceiver->getMessageCount();
        $this->assertEquals(3, $count);
    }
}