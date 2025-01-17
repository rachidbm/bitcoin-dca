<?php

declare(strict_types=1);

/*
 * This file is part of the Bitcoin-DCA package.
 *
 * (c) Jorijn Schrijvershof <jorijn@jorijn.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jorijn\Bitcoin\Dca\Model;

class NotificationEmailConfiguration
{
    protected string $to;
    protected string $from;
    protected string $subjectPrefix;

    public function __construct(string $to, string $from, string $subjectPrefix)
    {
        $this->to = $to;
        $this->from = $from;
        $this->subjectPrefix = $subjectPrefix;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getSubjectPrefix(): string
    {
        return $this->subjectPrefix;
    }
}
