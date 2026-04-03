<?php

namespace App\Enums;

enum PollStatus: string
{
    case Open = 'open';
    case Closed = 'closed';
}
