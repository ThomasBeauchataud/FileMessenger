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

use PHPUnit\Framework\TestCase;
use TBCD\Messenger\FileTransport\FileTransport;
use TBCD\Messenger\FileTransport\FileTransportFactory;

class FileTransportFactoryTest extends TestCase
{

    public function testLocal(): void
    {
        $dsn = 'local://./rootPath';
        $fileTransportFactory = new FileTransportFactory();
        $this->assertTrue($fileTransportFactory->supports($dsn, []));
        $transport = $fileTransportFactory->createTransport($dsn, []);
        $this->assertInstanceOf(FileTransport::class, $transport);
    }

    public function testFtp(): void
    {
        $dsn = 'ftp://foo:bar@localhost:21/rootPath';
        $fileTransportFactory = new FileTransportFactory();
        $this->assertTrue($fileTransportFactory->supports($dsn, []));
        $transport = $fileTransportFactory->createTransport($dsn, []);
        $this->assertInstanceOf(FileTransport::class, $transport);
    }

    public function testSftp(): void
    {
        $dsn = 'sftp://foo:bar@localhost:22/rootPath';
        $fileTransportFactory = new FileTransportFactory();
        $this->assertTrue($fileTransportFactory->supports($dsn, []));
        $transport = $fileTransportFactory->createTransport($dsn, []);
        $this->assertInstanceOf(FileTransport::class, $transport);
    }
}