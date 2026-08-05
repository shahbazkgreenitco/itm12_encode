(function($) {
  $.fn.work_hour_count_down = function(config) {
      var $t = $(this);
      var now = moment();

      var expire = moment($t.attr('data-expire'));
      var created_at = moment($t.attr('data-created_at'));
      
      created_at.set({second: 0});
      expire.set({second: 0});
      
      if( now.isSameOrAfter(expire) == true ) {
        return '<span class="label label-overdue">Overdue ' + expire.format('DD MMM YYYY hh:mm A') + '</span>';
      }

      /* work hr based calc */
      [work_start_hour, work_start_minute, work_start_second] = config.default_work_start.split(":"); 
      [work_end_hour, work_end_minute, work_end_second] = config.default_work_end.split(":"); 
      var x = moment(now);

      var day_start = moment( x.format('YYYY-MM-DD'), 'YYYY-MM-DD');
      day_start.set({hour: work_start_hour, minute: work_start_minute, second: work_start_second});
      if( x.isBefore(day_start) == true ) {
        x.set({hour: work_start_hour, minute: work_start_minute, second: work_start_second});
      }

      var minute_counter = 0;
      var work_start, work_end, diffInMinutes, diffWithExpire;
      do {
          if( x.isBusinessDay() == false ) {
              x = x.nextBusinessDay();
              x.set({hour: work_start_hour, minute: work_start_minute, second: work_start_second});
          }

          if( x.isHoliday() == true ) {
              x = x.add(1, 'days');
              x.set({hour: work_start_hour, minute: work_start_minute, second: work_start_second});
              continue;
          }

          work_start = moment( x.format('YYYY-MM-DD'), 'YYYY-MM-DD');
          work_start.set({hour: work_start_hour, minute: work_start_minute, second: work_start_second});
          work_end = moment( x.format('YYYY-MM-DD'), 'YYYY-MM-DD');
          work_end.set({hour: work_end_hour, minute: work_end_minute, second: work_end_second});
          
          if( x.isBefore(work_start) == true ) {
              x.set({hour: work_start_hour, minute: work_start_minute, second: work_start_second});
          }
          
          if( x.isAfter(work_end) == true ) {
              x.add(1, 'days');    
              x.set({hour: work_start_hour, minute: work_start_minute, second: work_start_second});
              continue;
          }

          diffInMinutes = (work_end.diff(x, 'minutes') + 1);
          if( x.isSame(expire, 'day') == false && x.isBefore(expire, 'day') == true ) {
              minute_counter += diffInMinutes;
              x.add(1, 'days');    
              x.set({hour: work_start_hour, minute: work_start_minute, second: work_start_second});
              continue;
          }

          diffWithExpire = expire.diff(x, 'minutes');
          if( x.isSame(expire, 'hour') == false && diffWithExpire >= 60 && x.isBefore(expire) == true ) {
              minute_counter += 60;
              x.add(60, 'minutes');
              continue;
          }

          if( diffInMinutes >= 0 ) {
              minute_counter += 1;
          }

          x.add(1, 'minutes');
      } while( x.isBefore(expire) == true );

      var remaining_hours = Math.trunc(minute_counter/60);
      var remaining_minutes = (minute_counter%60);
      var text_msg = "";
      if( remaining_hours > 0 ) {
          text_msg += remaining_hours == 1 ? (remaining_hours + " Hr ") : (remaining_hours + " Hrs ");
      }
      if( remaining_minutes > 0 ) {
          text_msg += remaining_minutes > 9 ? (remaining_minutes + " Mins ") : ("0" + remaining_minutes.toString() + " Mins");
      }

      text_msg += ' ' + expire.format('(DD MMM YYYY hh:mm A)');
      $t.text($.trim(text_msg));
  };
}(jQuery));