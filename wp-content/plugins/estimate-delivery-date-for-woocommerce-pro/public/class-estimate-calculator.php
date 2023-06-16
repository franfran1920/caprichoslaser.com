<?php

class pisol_calculate_estimates{

    function __construct($shipping_method_settings, $preparation_days = 0, $preparation_days_max = 0, $first_date = null){

        global $pisol_edd_plugin_settings;
        if(isset($pisol_edd_plugin_settings) && !empty($pisol_edd_plugin_settings)){
            $this->settings = $pisol_edd_plugin_settings;
        }else{
            $this->settings = pisol_edd_plugin_settings::init();
        }
        
        $this->first_date = $first_date;

        $this->preparation_days = (int)$preparation_days;
        $this->preparation_days_max = (int)$preparation_days_max;
        $this->min_days = $shipping_method_settings['min_days'];
        $this->max_days = $shipping_method_settings['max_days'];
        
        $this->holiday_overwrite = $shipping_method_settings['holiday_overwrite'];
        $this->holidays = $shipping_method_settings['holidays'];

        $this->overwrite_global_holiday_dates = $shipping_method_settings['overwrite_global_shipping_off_dates'];
        $this->shipping_method_holiday_dates = $shipping_method_settings['holiday_dates'];

        //$this->cutoff_overwrite = $shipping_method_settings['cutoff_overwrite'];
        $this->method_shipping_cutoff_time = $shipping_method_settings['shipping_cutoff_time'];


        $this->current_date = current_time('Y/m/d');
        $this->current_time = current_time('H:i');
        
    }

    function estimate(){
        if(!empty($this->preparation_days_max) && (int)$this->preparation_days_max > (int)$this->preparation_days){
            
            $min_estimate =  $this->estimateExtend($this->preparation_days);

            
            $max_estimate =  $this->estimateExtend($this->preparation_days_max);

            return $this->minMaxEstimateForProduct($min_estimate, $max_estimate);
        }else{
            
            return $this->estimateExtend($this->preparation_days);
        }
    }

    function minMaxEstimateForProduct($min_estimate, $max_estimate){
        $final_estimate = array();

        $dates = array();
        $days = array();
        if(!empty($min_estimate['min_date'])){
            $dates[] = $min_estimate['min_date'];
            $days[] = $min_estimate['min_days'];
        }

        if(!empty($min_estimate['max_date'])){
            $dates[] = $min_estimate['max_date'];
            $days[] = $min_estimate['max_days'];
        }

        if(!empty($max_estimate['min_date'])){
            $dates[] = $max_estimate['min_date'];
            $days[] = $max_estimate['min_days'];
        }

        if(!empty($max_estimate['max_date'])){
            $dates[] = $max_estimate['max_date'];
            $days[] = $max_estimate['max_days'];
        }

        

        $final_estimate['min_date'] = $this->getSmallestDate($dates);
        $final_estimate['max_date'] = $this->getLargestDate($dates);
        
        $final_estimate['min_days'] = is_array($days) && !empty($days) ? min($days) : "";
        $final_estimate['max_days'] = is_array($days) && !empty($days) ? max($days) : "";
        return $final_estimate;
    }

    function getLargestDate($date_array){
        $estimates = $date_array;
        $longest = 0;
        $longestDate = "";
        if(is_array($estimates) && !empty($estimates)){
        foreach($estimates as $key => $date){
            if(!$date) continue;

            $curDate = strtotime($date);
            if ($curDate > $longest) {
                $longest = $curDate;
                $longestDate = $date;
            }
        }
        }
        return $longestDate;
    }

    function getSmallestDate($date_array){
        $estimates = $date_array;
        
        $smallestDate = "";
        if(is_array($estimates) && !empty($estimates)){
        $smallest = strtotime($estimates[0]);
        $smallestDate = $estimates[0];
        foreach($estimates as $key => $date){
            if(!$date) continue;

            $curDate = strtotime($date);
            if ($curDate < $smallest) {
                $smallest = $curDate;
                $smallestDate = $date;
            }
        }
        }
        return $smallestDate;
    }


    function estimateExtend($preparation_days){
        if($this->min_days == null && $this->max_days == null) return null;
        
        $estimate['min_date'] = $this->estimateCalculator($this->min_days, $preparation_days);
        $estimate['min_days'] = $this->daysAwayFromToday($estimate['min_date']);
        
        if($this->min_days == $this->max_days){
            $estimate['max_date'] =  $estimate['min_date'];
            $estimate['max_days'] =  $estimate['min_days'];
        }else{
            $estimate['max_date'] = $this->estimateCalculator($this->max_days, $preparation_days);
            $estimate['max_days'] =  $this->daysAwayFromToday($estimate['max_date']);
        }
        
        return $estimate;
    }

    function estimateCalculator($shipping_days, $preparation_days){
        $working_days = $this->workingDaysNeeded($shipping_days, $preparation_days );
        return $this->getExactDate($working_days);
    }

    function workingDaysNeeded($shipping_days, $preparation_days ){
        if(empty($this->first_date)){
            $delivery_in_working_days = $shipping_days + $preparation_days;
        }else{
            $delivery_in_working_days = $shipping_days + $preparation_days;
        }
        return $delivery_in_working_days;
    }

     /**
     * it adds holidays, wek off , cut off and give exact date
     */
    function getExactDate($working_days){
        if($this->checkTodayShippingPossible()){
            $first_day = $this->current_date;
        }else{
            $first_day = date('Y/m/d', strtotime($this->current_date."+1 days"));
        }

        if($this->first_date != null){
            $first_day = $this->first_date;
            if($first_day == $this->current_date){
                if( !$this->checkTodayShippingPossible()){
                    $first_day = date('Y/m/d', strtotime($this->current_date."+1 days"));
                }
            }
        }
        /* Shop closed adjustment */
        $first_day = $this->adjustFirstDayIfShopClosed( $first_day );

        $working_days_array = array();
        $count = 0;
        while(count($working_days_array) < $working_days){
            $date = date('Y/m/d', strtotime($first_day."+".$count." days"));
            if($this->todayShippingWorking($date)){
                $working_days_array[] = $date;
            }
            $count++;
        }

        return end($working_days_array);
    }

    /**
     * check for cut off time, check for holiday, check for week day off
     */
    function checkTodayShippingPossible(){

        $shipping_cutoff_time = $this->getShippingCutoffTime();
        /**
         * if there is not cut off time set, we return false
         */
        if(empty($shipping_cutoff_time) || $shipping_cutoff_time == ""){
            return false;
        }

        
        $now = $this->current_time;
        $cut_off_time = $shipping_cutoff_time;
        
        /**
         * If today is in holiday we return false as we cant to shipping today
         */
        $today = current_time('Y/m/d');
        
        if ( $this->isHoliday( $today ) ){
            return false;
        }


        if ( $this->isSkipDay( $today ) ){
            return false;
        }

        /**
         * if present time is below cut off time will return true
         */
        if(strtotime($cut_off_time) > strtotime($now)){
			return true;
        }
        
        return false;
    }

    function isHoliday($date){
        $holidays = $this->settings['holidays'];

        /**
         * if you have a holiday date set for a shipping method then that 
         * will overwrite the global holiday date
         */
        if(!empty($this->shipping_method_holiday_dates)){
            if(!empty($this->overwrite_global_holiday_dates)){
                $holidays = $this->shipping_method_holiday_dates;
            }else{
                $holidays = array_merge($holidays, $this->shipping_method_holiday_dates);
            }
        }else{
            if(!empty($this->overwrite_global_holiday_dates)){
                $holidays = [];
            }
        }

        if(is_array($holidays)){
            return in_array($date, $holidays) ? true : false;
        }
        return false;
    }

    function isSkipDay($date){
        $day = date('N',strtotime($date));
        $skip_days_of_the_week = $this->shippingClosedOnDays();
        $skip_days_of_the_week = is_array($skip_days_of_the_week) ? $skip_days_of_the_week : array();
        
        if(in_array($day, $skip_days_of_the_week)){
            return true;
        }
        return false;
    }

    /**
     * If shop is closed on certain day and that day is the first date to ship the product then that wont be counted for first date and we will shift the first date to next date
     */
    function adjustFirstDayIfShopClosed( $first_day ){
        $first_day_week_day = date('N',strtotime($first_day));
        $shop_closed_days = $this->settings['shop_closed_on_days'];
        $shop_holidays_date = $this->settings['shop_holidays'];

        if((is_array($shop_closed_days) && in_array($first_day_week_day, $shop_closed_days)) || !$this->todayShippingWorking( $first_day ) || (is_array($shop_holidays_date) && in_array($first_day, $shop_holidays_date))){
            $first_day = date('Y/m/d', strtotime($first_day."+1 days"));
            $first_day = $this->adjustFirstDayIfShopClosed( $first_day );
        }

        return $first_day;
    }

    function shippingClosedOnDays(){
        if(!empty($this->holiday_overwrite)){
            return !empty($this->holidays) ? $this->holidays : array();
        }

        return $this->settings['shipping_closed_on_days'];
    }

    function getShippingCutoffTime(){

        
        return !empty($this->method_shipping_cutoff_time) ? $this->method_shipping_cutoff_time : $this->settings['shipping_cutoff_time'];
        
    }

    function todayShippingWorking($date){
        if($this->isHoliday($date) || $this->isSkipDay($date)){
            return false;
        }
        return true;
    }

    function daysAwayFromToday($estimate){
        if(empty($estimate)) return null;

        $today = current_time("Y/m/d");
        $datetime1 = date_create($today); 
        $datetime2 = date_create($estimate); 
  
        // Calculates the difference between DateTime objects 
        $interval = date_diff($datetime1, $datetime2);
        return $interval->days;
    }

}
