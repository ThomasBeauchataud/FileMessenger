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

class MessageTest
{

    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }


    public function getContent(): string
    {
        return $this->content;
    }
}