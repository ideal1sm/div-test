<?php

namespace App\Enums;

enum UserRequestStatus: string
{
    case Active = "Active";
    case Resolved = "Resolved";
}
