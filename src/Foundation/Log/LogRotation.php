<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Log;

enum LogRotation: string
{
    case Single = 'single';
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';
}
