var event_temp={};
$(document).ready(function() {
		/* initialize the external events
		-----------------------------------------------------------------*/
		$('#external-events .fc-event').each(function() {
			// store data so the calendar knows to render an event upon drop
			$(this).data('event', {
				title: $.trim($(this).text()),
				backgroundColor:$.trim($(this).css("background-color")), 
				textColor:$.trim($(this).css("color")),
				name: 'chumma',
				stick: true,
				overlap:true,
				durationEditable:true,
			});
			// make the event draggable using jQuery UI
			$(this).draggable({
					zIndex: 999,
					revert: true,      // will cause the event to go back to its
					revertDuration: 0  //  original position after the drag
			});
		});
		/* initialize the calendar
		-----------------------------------------------------------------*/
		$('#calendar').fullCalendar({
			header: {
				left: '',
				center: 'Doctor Scheduling',
				right: ''
			},
			allDaySlot:false,
			editable: true,
			droppable: true, // this allows things to be dropped onto the calendar
			defaultView:"agendaWeek",
			dayNamesShort:['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
			defaultTimedEventDuration:"00:30:00",
			defaultAllDayEventDuration:"1",
			forceEventDuration:true,
			eventDurationEditable:true,
			drop: function(event, jsEvent, view ) {
				$('#calendar').fullCalendar('removeEvents', function (calEvent) {
					if(event == calEvent)
					return true;
				});
			},
			events: function(start, end, timezone, callback) {
					$.ajax({
						url: 'schedule-feed.php',
						dataType: 'JSON',
						data:{clinic_id:$("#clinics").val(),action:"get",did:$("#did").val()},
						success: function(data) {
							if(data.status==1)
								callback(data.events);
							else  alert("no events");
						}
					});
			},
			viewRender:function(){
				$("th.fc-day-header.fc-widget-header.fc-sun").text("Sunday")
				$("th.fc-day-header.fc-widget-header.fc-mon").text("Monday")
				$("th.fc-day-header.fc-widget-header.fc-tue").text("Tuesday")
				$("th.fc-day-header.fc-widget-header.fc-wed").text("Wednesday")
				$("th.fc-day-header.fc-widget-header.fc-thu").text("Thursday")
				$("th.fc-day-header.fc-widget-header.fc-fri").text("Friday ")
				$("th.fc-day-header.fc-widget-header.fc-sat").text("Saturday")
			},
			eventClick:function( event, jsEvent, view ) {
				event_temp=event;
				$("#TypesOfSchedule").val(event.title);
				m=event._start;
				$('#timepicker5').timepicker('setTime', m.format("h:mm:ss A"));
				m=event._end;
				$('#timepicker6').timepicker('setTime', m.format("h:mm:ss A"));
				$('#EventDetail').modal('show');
			},
			eventDrop: function(event, delta, revertFunc) {
				var start = new Date(event.start);
				var end = new Date(event.end);
				var overlap;
				overlap = $('#calendar').fullCalendar('clientEvents', function(ev) {
					if( ev == event || ev.rendering=="background")
						return false;
					var estart = new Date(ev.start);
					var eend = new Date(ev.end);
					return (Math.round(estart)/1000 < Math.round(end)/1000 && Math.round(eend) > Math.round(start));
				});
				if (overlap.length){
					revertFunc(event)
				}
			}
		});
		$("#clinics").change(function(){
			var box;
			$.ajax({
				url:"schedule-feed.php",
				data:{"clinic_id":$(this).val(),action:"get",did:$("#did").val()},
				dataType:"JSON",
				success:function(source){
					if(box)box.remove();
					$('#calendar').fullCalendar( 'removeEvents');
					if(source.status==1){
						$('#calendar').fullCalendar( 'addEventSource', source.events);
						$('#calendar').fullCalendar( 'rerenderEvents');
					}
					else  alert("no events");
				},
				beforeSend:function(){
					box = new ajaxLoader($("body")[0], {classOveride: 'blue-loader', bgColor: '#000', opacity: '0.3'});
				}
			})
		})
		$("#save_scheduling").click(function(){
			var box;
			var a=new Array();
			v=$('#calendar').fullCalendar('clientEvents',function(eve){
				return eve;
			});
			for(i=0;i<v.length;i++){
				if(v[i].rendering == 'background') continue;
				b={
					"end_day":v[i]._end.day(),
					"end_minute":v[i]._end.minute(),
					"end_hour":v[i]._end.hour(),
					"start_day":v[i]._start.day(),
					"start_minute": v[i]._start.minute(),
					"start_hour": v[i]._start.hour(),
					"title": v[i].title
				};
				a.push(b);
			}
			$.ajax({
				url:"schedule-feed.php",
				type:"POST",
				data:{events:a,clinic_id:$("#clinics").val(),action:"save",did:$("#did").val()},
				dataType:"JSON",
				success:function(source){
					if(box)box.remove();
					$('#calendar').fullCalendar( 'removeEvents');
					if(source.status==1){
						$('#calendar').fullCalendar( 'addEventSource', source );
						$('#calendar').fullCalendar( 'rerenderEvents');
					}
					if(source.clashed_events>0){
							alert(source.clashed_events+" Event(s) Clashed")
					}
					alert(source.msg);
				},
				beforeSend:function(){
					box = new ajaxLoader($("body")[0], {classOveride: 'blue-loader', bgColor: '#000', opacity: '0.3'});
				}
			})
		})
		$("#UpdateEvent").click(function(){
			event_temp.start=event_temp.start.time(moment(moment().format("YYYY MM DD")+" "+$("#timepicker5").val()).format("H:m:ss"));
			event_temp.end=event_temp.end.time(moment(moment().format("YYYY MM DD")+" "+$("#timepicker6").val()).format("H:m:ss"));
			event_temp.title=$("#TypesOfSchedule").val();
			event_temp.backgroundColor=$("#"+$("#TypesOfSchedule").val().split(" ").join("_")).css("background-color");
			event_temp.textColor=$("#"+$("#TypesOfSchedule").val().split(" ").join("_")).css("color");
			$("#calendar").fullCalendar( 'updateEvent', event_temp );
			$('#EventDetail').modal('hide');
		})
		$("#delete_event").click(function(){
			k=confirm("Are you sure to delete event");
			if(k){
				$('#calendar').fullCalendar('removeEvents',event_temp._id);
				$('#EventDetail').modal('hide');
				//$("#save_scheduling").trigger("click");
			}
		})
});