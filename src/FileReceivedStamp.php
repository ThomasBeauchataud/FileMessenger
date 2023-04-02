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

use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;

final class FileReceivedStamp implements StampInterface, NonSendableStampInterface
{

    private string $filepath;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }


    public function getFilepath(): string
    {
        return $this->filepath;
    }
}