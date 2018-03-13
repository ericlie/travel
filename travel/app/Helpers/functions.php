<?php

function distance($lat1, $lon1, $lat2, $lon2) : float
{
    return \App\Helpers\LatLongCalculator::distance($lat1, $lon1, $lat2, $lon2);
}
