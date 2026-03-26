<?php
// app/Helpers/Helpers.php

if (!function_exists('generate_participant_number')) {
    function generate_participant_number()
    {
        $last = \App\Models\Applicant::latest('id')->first();
        $number = $last ? intval(substr($last->participant_numb, 1)) + 1 : 1;
        return 'P' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
