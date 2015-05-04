$(function () {
	function time() {
	  //  discuss at: http://phpjs.org/functions/time/
	  // original by: GeekFG (http://geekfg.blogspot.com)
	  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // improved by: metjay
	  // improved by: HKM
	  //   example 1: timeStamp = time();
	  //   example 1: timeStamp > 1000000000 && timeStamp < 2000000000
	  //   returns 1: true

	  return Math.floor(new Date()
	    .getTime() / 1000);
	}

	function strtotime(text, now) {
	  //  discuss at: http://phpjs.org/functions/strtotime/
	  //     version: 1109.2016
	  // original by: Caio Ariede (http://caioariede.com)
	  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // improved by: Caio Ariede (http://caioariede.com)
	  // improved by: A. Matías Quezada (http://amatiasq.com)
	  // improved by: preuter
	  // improved by: Brett Zamir (http://brett-zamir.me)
	  // improved by: Mirko Faber
	  //    input by: David
	  // bugfixed by: Wagner B. Soares
	  // bugfixed by: Artur Tchernychev
	  //        note: Examples all have a fixed timestamp to prevent tests to fail because of variable time(zones)
	  //   example 1: strtotime('+1 day', 1129633200);
	  //   returns 1: 1129719600
	  //   example 2: strtotime('+1 week 2 days 4 hours 2 seconds', 1129633200);
	  //   returns 2: 1130425202
	  //   example 3: strtotime('last month', 1129633200);
	  //   returns 3: 1127041200
	  //   example 4: strtotime('2009-05-04 08:30:00 GMT');
	  //   returns 4: 1241425800

	  var parsed, match, today, year, date, days, ranges, len, times, regex, i, fail = false;

	  if (!text) {
	    return fail;
	  }

	  // Unecessary spaces
	  text = text.replace(/^\s+|\s+$/g, '')
	    .replace(/\s{2,}/g, ' ')
	    .replace(/[\t\r\n]/g, '')
	    .toLowerCase();

	  // in contrast to php, js Date.parse function interprets:
	  // dates given as yyyy-mm-dd as in timezone: UTC,
	  // dates with "." or "-" as MDY instead of DMY
	  // dates with two-digit years differently
	  // etc...etc...
	  // ...therefore we manually parse lots of common date formats
	  match = text.match(
	    /^(\d{1,4})([\-\.\/\:])(\d{1,2})([\-\.\/\:])(\d{1,4})(?:\s(\d{1,2}):(\d{2})?:?(\d{2})?)?(?:\s([A-Z]+)?)?$/);

	  if (match && match[2] === match[4]) {
	    if (match[1] > 1901) {
	      switch (match[2]) {
	      case '-':
	        {
	          // YYYY-M-D
	          if (match[3] > 12 || match[5] > 31) {
	            return fail;
	          }

	          return new Date(match[1], parseInt(match[3], 10) - 1, match[5],
	            match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
	        }
	      case '.':
	        {
	          // YYYY.M.D is not parsed by strtotime()
	          return fail;
	        }
	      case '/':
	        {
	          // YYYY/M/D
	          if (match[3] > 12 || match[5] > 31) {
	            return fail;
	          }

	          return new Date(match[1], parseInt(match[3], 10) - 1, match[5],
	            match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
	        }
	      }
	    } else if (match[5] > 1901) {
	      switch (match[2]) {
	      case '-':
	        {
	          // D-M-YYYY
	          if (match[3] > 12 || match[1] > 31) {
	            return fail;
	          }

	          return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
	            match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
	        }
	      case '.':
	        {
	          // D.M.YYYY
	          if (match[3] > 12 || match[1] > 31) {
	            return fail;
	          }

	          return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
	            match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
	        }
	      case '/':
	        {
	          // M/D/YYYY
	          if (match[1] > 12 || match[3] > 31) {
	            return fail;
	          }

	          return new Date(match[5], parseInt(match[1], 10) - 1, match[3],
	            match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
	        }
	      }
	    } else {
	      switch (match[2]) {
	      case '-':
	        {
	          // YY-M-D
	          if (match[3] > 12 || match[5] > 31 || (match[1] < 70 && match[1] > 38)) {
	            return fail;
	          }

	          year = match[1] >= 0 && match[1] <= 38 ? +match[1] + 2000 : match[1];
	          return new Date(year, parseInt(match[3], 10) - 1, match[5],
	            match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
	        }
	      case '.':
	        {
	          // D.M.YY or H.MM.SS
	          if (match[5] >= 70) {
	            // D.M.YY
	            if (match[3] > 12 || match[1] > 31) {
	              return fail;
	            }

	            return new Date(match[5], parseInt(match[3], 10) - 1, match[1],
	              match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
	          }
	          if (match[5] < 60 && !match[6]) {
	            // H.MM.SS
	            if (match[1] > 23 || match[3] > 59) {
	              return fail;
	            }

	            today = new Date();
	            return new Date(today.getFullYear(), today.getMonth(), today.getDate(),
	              match[1] || 0, match[3] || 0, match[5] || 0, match[9] || 0) / 1000;
	          }

	          // invalid format, cannot be parsed
	          return fail;
	        }
	      case '/':
	        {
	          // M/D/YY
	          if (match[1] > 12 || match[3] > 31 || (match[5] < 70 && match[5] > 38)) {
	            return fail;
	          }

	          year = match[5] >= 0 && match[5] <= 38 ? +match[5] + 2000 : match[5];
	          return new Date(year, parseInt(match[1], 10) - 1, match[3],
	            match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
	        }
	      case ':':
	        {
	          // HH:MM:SS
	          if (match[1] > 23 || match[3] > 59 || match[5] > 59) {
	            return fail;
	          }

	          today = new Date();
	          return new Date(today.getFullYear(), today.getMonth(), today.getDate(),
	            match[1] || 0, match[3] || 0, match[5] || 0) / 1000;
	        }
	      }
	    }
	  }

	  // other formats and "now" should be parsed by Date.parse()
	  if (text === 'now') {
	    return now === null || isNaN(now) ? new Date()
	      .getTime() / 1000 | 0 : now | 0;
	  }
	  if (!isNaN(parsed = Date.parse(text))) {
	    return parsed / 1000 | 0;
	  }

	  date = now ? new Date(now * 1000) : new Date();
	  days = {
	    'sun': 0,
	    'mon': 1,
	    'tue': 2,
	    'wed': 3,
	    'thu': 4,
	    'fri': 5,
	    'sat': 6
	  };
	  ranges = {
	    'yea': 'FullYear',
	    'mon': 'Month',
	    'day': 'Date',
	    'hou': 'Hours',
	    'min': 'Minutes',
	    'sec': 'Seconds'
	  };

	  function lastNext(type, range, modifier) {
	    var diff, day = days[range];

	    if (typeof day !== 'undefined') {
	      diff = day - date.getDay();

	      if (diff === 0) {
	        diff = 7 * modifier;
	      } else if (diff > 0 && type === 'last') {
	        diff -= 7;
	      } else if (diff < 0 && type === 'next') {
	        diff += 7;
	      }

	      date.setDate(date.getDate() + diff);
	    }
	  }

	  function process(val) {
	    var splt = val.split(' '), // Todo: Reconcile this with regex using \s, taking into account browser issues with split and regexes
	      type = splt[0],
	      range = splt[1].substring(0, 3),
	      typeIsNumber = /\d+/.test(type),
	      ago = splt[2] === 'ago',
	      num = (type === 'last' ? -1 : 1) * (ago ? -1 : 1);

	    if (typeIsNumber) {
	      num *= parseInt(type, 10);
	    }

	    if (ranges.hasOwnProperty(range) && !splt[1].match(/^mon(day|\.)?$/i)) {
	      return date['set' + ranges[range]](date['get' + ranges[range]]() + num);
	    }

	    if (range === 'wee') {
	      return date.setDate(date.getDate() + (num * 7));
	    }

	    if (type === 'next' || type === 'last') {
	      lastNext(type, range, num);
	    } else if (!typeIsNumber) {
	      return false;
	    }

	    return true;
	  }

	  times = '(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec' +
	    '|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?' +
	    '|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?)';
	  regex = '([+-]?\\d+\\s' + times + '|' + '(last|next)\\s' + times + ')(\\sago)?';

	  match = text.match(new RegExp(regex, 'gi'));
	  if (!match) {
	    return fail;
	  }

	  for (i = 0, len = match.length; i < len; i++) {
	    if (!process(match[i])) {
	      return fail;
	    }
	  }

	  // ECMAScript 5 only
	  // if (!match.every(process))
	  //    return false;

	  return (date.getTime() / 1000);
	}

	function humanTiming (timeVal)
	{
		timeVal = time() - strtotime(timeVal); // to get the time since that moment

		var tokensKeys = [
			31536000,
			2592000,
			604800,
			86400,
			3600,
			60,
			1
		];

		var tokensValues = [
			'year',
			'month',
			'week',
			'day',
			'hour',
			'minute',
			'second'
		];

		var i;
		var len = tokensKeys.length;
		var unit;
		var numberOfUnits;
		var text;

		for (i=0; i<len; i++) {
			unit = tokensKeys[i];
			if (timeVal < unit) continue;
			numberOfUnits = Math.floor(timeVal / unit);
			text = tokensValues[i];
			return numberOfUnits+' '+text+((numberOfUnits>1)?'s':'');
		}

		return '0 seconds';
	}
	
	var $updateHumanTiming = $('.humanTiming');
	setInterval(function() {
		$updateHumanTiming.each(function() {
			var $this = $(this);
			var humanTimingVal = humanTiming($this.attr('data-time'));
			var html = humanTimingVal + ' ago'; 
			if($this.html() != html) {
				$this.html( html );
			}
		});
	}, 1000);

	$.ajax({
		url: "/dashboard/getMessages",
		type: "GET",
		cache: false,
		success: function (r) {
			var json = $.parseJSON(r);
			
			if(json)
			{
				var options = "";
				$.each(json, function(index, item){
					options += "<option value='" + item.id + "'>" + item.text + "</option>";
				});
				$(".companion-messages-select").html(options).selectpicker();
				
				$('.companion-messages-select').on('change', function(e, isTriggered) {
					if(!this.value)
						return;
						
					var selfie = $(this);
					var ancestor = selfie.parent();
					ancestor.find('#companion-message-audio-player').remove();
					
					$.ajax({
						url: "/dashboard/getAudioPlayer",
						type: "GET",
						data: { id : this.value },
						cache: false,
						success: function (r) {
							ancestor.append("<div id='companion-message-audio-player' class='span1 pull-right' style='margin-top:-5px;'>"+r+"</div>");
							var player = $('#companion-message-audio-player').find('.jp-jplayer');
							
							if(!isTriggered)
							{
								player.bind($.jPlayer.event.ready, function(event) {
									player.jPlayer("play");
								});
							}
							
							$('.btn-send-reply').unbind('click');
							$('.btn-send-reply').bind('click', function(e) {
								var companionId = $(this).data('companionId');
								
								if(player)
									player.jPlayer("stop");
											
								$.ajax({
									url: "/dashboard/sendAudioMessage",
									type: "GET",
									cache: false,
									data: { audioId : selfie.val(), companionId: companionId},
									success: function (r) {
										if(player)
											player.jPlayer("stop");
										$('#send-a-message-modal-'+companionId).modal('hide');
										$('#success-modal').modal('show');
									},
									error: function( jqXhr ) {
										if( jqXhr.status == 401 )
											window.location = '/sign_in';
									}
								});
							});
						},
					
						error: function( jqXhr ) {
							if( jqXhr.status == 401 )
								window.location = '/sign_in';
						}
					});
				});
				$('.companion-messages-select').trigger('change', [true]);
			}
		},
	
		error: function( jqXhr ) {
			if( jqXhr.status == 401 )
				window.location = '/sign_in';
		}
	});

	var pauseReload = 0;
	var shouldReload = false;
	var reloadAborted = false;

	function pausePollingReload() {
		pauseReload++;
	}

	function playPollingReload() {
		if(pauseReload == 0 && shouldReload) {
			toastr["error"]("The page will reload momentarily... click to cancel.", "Safety Team Updates!");
		}
		pauseReload--;
		if(pauseReload < 0)
			pauseReload = 0;
	}

	function goPoll() {
		if(!shouldReload) {
			poll();
		}
	}

	function toastCloseClick() {
		reloadAborted = true;
	}

	function toastHidden() {
		if(!reloadAborted) {
			location.reload(true);
			return;
		}

		reloadAborted = false;
		shouldReload = false;
		goPoll();
	}

	//TODO: enhance with the use of http://www.html5rocks.com/en/tutorials/eventsource/basics/
	function poll() {
       $poller = $.ajax({ 
       		url: "/dashboard/poll", 
       		success: function(r) {
       			if(r === 1 || r === "1") {
       				shouldReload = true;
       				playPollingReload();
            	}
       		}, 
       		error: function( jqXhr ) {
				if( jqXhr.status == 401 )
					window.location = '/sign_in';
			},
			type: "GET", 
			cache: false, 
			complete: goPoll 
		});
	}

	$(document).on('show', '.modal', pausePollingReload);

	$(document).on('hidden', '.modal', playPollingReload);

	toastr.options = {
	  "closeButton": true,
	  "closeHtml": '<button onclick="$(\'#toast-container .toast\').click();return false;" type="button">&times;</button>',
	  "debug": false,
	  "newestOnTop": false,
	  "progressBar": true,
	  "positionClass": "toast-bottom-center",
	  "preventDuplicates": true,
	  "onclick": toastCloseClick,
	  "onHidden": toastHidden,
	  "showDuration": "300",
	  "hideDuration": "1000",
	  "timeOut": "15000",
	  "extendedTimeOut": "5000",
	  "showEasing": "swing",
	  "hideEasing": "linear",
	  "showMethod": "fadeIn",
	  "hideMethod": "fadeOut"
	};

	poll();
});