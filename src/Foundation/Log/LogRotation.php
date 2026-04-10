<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Log;

enum LogRotation: string
{
    case Daily = 'daily';
    case Monthly = 'monthly';
    case Single = 'single';
    case Weekly = 'weekly';
    case Yearly = 'yearly';
}
