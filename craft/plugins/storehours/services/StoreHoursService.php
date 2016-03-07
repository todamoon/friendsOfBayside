<?php
namespace Craft;

class StoreHoursService extends BaseApplicationComponent
{


  public function isOpen($data, $day = 'today') {
    if (is_array($data))
    {
      if ($day == 'weekend') {


        if ($data[0]['open']) {
          return true;
        } else if ($data[5]['open']) {
          return true;
        } else if ($data[6]['open']) {
          return true;
        } else {
          return false;
        }

      } else if ($day == 'today') {
        $today = $data[date('w')];
      } else {
        $today = $data[$day];
      }



      // $closeTime = $today['close'] -> format ('H:i');
      // $openTime =  $today['open'] -> format('H:i');

      if ($today['open']) {
        return true;
      } else {
        return false;
      }

    } else {
      return null;
    }

  }

  public function getToday($data) {
    if (is_array($data))
    {
      $dayIndex = date('w');
      $today = $data[$dayIndex];

      if ($today['close'] != '' || $today['close'] != null) {
        $close = $today['close']->format("H:i:s");
        $open =  $today['open']->format("H:i:s");
        $date = new DateTime();
        $now = $date->format("H:i:s");



        if ($now> $open && $now < $close) {
          $today['currently'] = 'Open';
        } else {
          $today['currently'] = 'Closed';
        }


        return $today;
      } else {
        return null;
      }


    } else {
      return null;
    }

  }

  public function getNextOpen($data) {


    $dayIndex = date('w')+1;
    $nextDay = null;

    for($i=$dayIndex; $i<count($data); $i++){
      if ($data[$i]['open']) {
        $nextDay = $data[$i];
        break;
      }
    }
    // if you get to the end loop back the the begining
    if ($nextDay == null) {
      for($i=0; $i<$dayIndex; $i++){
        if ($data[$i]['open']) {
          $nextDay = $data[$i];
          break;
        }
      }
    }

    if ($i == $dayIndex) {
      $nextDay['day'] = 'tomorrow';

    } else {
      $nextDay['day'] = date('l', strtotime("Sunday +".$i." days"));

    }

    return $nextDay;

  }

  public function openEntries($entries, $day = 'today') {


    if (!is_array($entries)){
      $entries = $entries->find();
    }

    foreach ($entries as $key => $entry) {
      if(!craft()->storeHours->isOpen($entry->hours, $day)) {
        unset($entries[$key]);
      }
    }

    return $entries;
  }
  public function getDaysList($data) {

    $days = array('Sundays', 'Mondays', 'Tuesdays', 'Wednesdays', 'Thursdays', 'Fridays', 'Saturdays');

    $return = '';
    $count = 0;
    $everyday = false;

    if (is_array($data))
    {
      foreach ($data as $key => $day)
      {
        if ($day['open'] != '')
				{
          if ($count > 0) {
            $return .= ', ';
          }
          $return .= $days[$key];
          $count ++;
        }
      }

    }

    if ($count == 7) {
      return 'Everyday';

    } else {
      return $return;

    }
  }

  public function convertTimes($value) {

    $timezone = craft()->getTimeZone();

    if (is_array($value))
		{
			foreach ($value as &$day)
			{
				if ((is_string($day['open']) && $day['open']) || (is_array($day['open']) && $day['open']['time']))
				{
					$day['open'] = DateTime::createFromString($day['open'], $timezone);
				}
				else
				{
					$day['open'] = '';
				}

				if ((is_string($day['close']) && $day['close']) || (is_array($day['close']) && $day['close']['time']))
				{
					$day['close'] = DateTime::createFromString($day['close'], $timezone);
				}
				else
				{
					$day['close'] = '';
				}
			}
		}

    return $value;

  }

}
