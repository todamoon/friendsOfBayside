<?php

namespace Craft;

class StoreHoursVariable
{
    public function isOpen($hours, $day = 'today')
    {
      return craft()->storeHours->isOpen($hours, $day);
    }

    public function getToday($hours)
    {
      return craft()->storeHours->getToday($hours);
    }

    public function getNextOpen($hours)
    {
      return craft()->storeHours->getNextOpen($hours);
    }



    public function getDaysList($hours) {
      return craft()->storeHours->getDaysList($hours);
    }

    public function openEntries($entries, $day = 'today') {
      return craft()->storeHours->openEntries($entries, $day);
    }
}
