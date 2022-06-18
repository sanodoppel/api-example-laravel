<?php

namespace App\Validation\Messages;

enum FormMessageEnum
{
    case REQUIRED;
    case NOT_EMAIL;
    case WRONG_FORMAT;
    case WRONG_LENGTH;
    case NOT_UNIQUE;
    case NOT_EXIST;
    case NOT_STRING;
    case DIGIT_WRONG_LENGTH;
    case NOT_UUID;
    case NOT_IMAGE;
    case WRONG_SIZE;
}
