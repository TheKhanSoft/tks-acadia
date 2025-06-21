<?php

namespace App\Enums;

enum SubjectType: string
{
    use EnumToSelectArray;

    case CORE = 'core';
    case ELECTIVE = 'elective';
    case SPECIALIZATION = 'specialization';
    case OPTIONAL = 'optional';
    case FOUNDATION = 'foundation';
    case PRACTICAL = 'practical';
    case PROJECT = 'project';
    case THESIS = 'thesis';
    case INTERNSHIP = 'internship';
    case SEMINAR = 'seminar';
    case WORKSHOP = 'workshop';
    case LABORATORY = 'laboratory';
    case RESEARCH = 'research';
    case CAPSTONE = 'capstone';
    case AUDIT = 'audit';
    case INDEPENDENT_STUDY = 'independent study';
    case FIELD_WORK = 'field work';

    public function label(): string
    {
        return ucwords(strtolower(str_replace('_', ' ', $this->name)));
    }
}
