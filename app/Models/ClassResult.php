<?php

namespace App\Models;

/**
 * ClassResult Model
 *
 * This is a proxy model for CourseClass used specifically by ClassResultResource.
 * It allows Filament Shield to treat ClassResult (instructor view of class progress)
 * as a separate resource from Classes (admin management of classes).
 *
 * All functionality is inherited from CourseClass through inheritance.
 */
class ClassResult extends CourseClass
{
    protected $table = 'classes';
}
