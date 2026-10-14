<?php

namespace App\Enums;

enum InquiryStatus: string
{
    case New = 'new';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Closed = 'closed';
}
