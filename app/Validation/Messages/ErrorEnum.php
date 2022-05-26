<?php

namespace App\Validation\Messages;

enum ErrorEnum
{
    case ERROR;
    case FORM_ERROR;
    case UNAUTHORIZED;
    case UNAUTHENTICATED;
    case THROTTLED;
}
