<?php
namespace Craft;
class StoreHoursModel extends BaseModel
{
    protected function defineAttributes()
    {
        return array(
            'days' => AttributeType::Mixed
        );
    }

    public function today()
    {
      return $today = $this->days[date('w')];
    }

    public function isOpen()
    {


      // get todays day of week

      $today = $this->days[date('w')];

      $now = date('h:i');

      $now = new DateTime();
      $now = $now->format('h:i', craft()->getTimeZone());

      var_dump($now);
      exit;

      $closeTime = $today['close'] -> format ('H:i');
      $openTime =  $today['open'] -> format('H:i');




      if ($now >= $openTime && $now <= $closeTime ) {
        return true;
      } else {
        return false;
      }

      // get specific opening/closing times for that day

      // check if current time is > closing or < opening



      return 'this is a test: '.$today["open"];
    }
}
