<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use \Extension;

class AppExtension extends AbstractExtension{

    public function dateTimeFormat(Object $datetime){

        setLocale(LC_TIME, 'fr_FR.tf8', 'fra');
        return strftime("%A %e %B %Y, %Hh %M", date_timestamp_get($datetime));

    }

    public function dateTimeFormatInteger(Object $datetime){

        return strftime("%d/%m/%G, %Hh%M", date_timestamp_get($datetime));

    }

    public function getFilters(){

        return array(
            new TwigFilter('dateTimeFormat', array($this, 'dateTimeFormat')),
            new TwigFilter('dateTimeFormatInteger', array($this, 'dateTimeFormatInteger'))
        );

    }
}