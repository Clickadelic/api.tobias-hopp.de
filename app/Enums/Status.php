<?php

namespace App\Enums;

enum Status: string
{
	case Draft = 'draft';
	case Published = 'published';
	case Archived = 'archived';
}
