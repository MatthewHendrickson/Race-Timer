/*
 * race-timer
 */
var newbib = 0; // current bib number
var counter = 0; // id of next racer
var timing_screen = true;
var time_screen = false;
var race_screen = false;
var results_screen = false;
var setup_screen = false;
var set_racers = false;
var set_interval = false;
var start_timer = null;
var enter_start_time = {'hr':'00', 'mn':'00', 'sc': '00'};
var race = null;
var last_button = null;

if (localStorage.races) {
//	alert('starting with data: '+localStorage.getItem('races'));
	restore_race();
	count = race.results.length;
//	alert('racers: '+count);
} else {
//	alert('starting with no data');
	init_race();
}

// Stuff that needs to wait until  the page renders
window.addEvent('load', function(event) {
//	alert('window load');
	race.results.each(function(racer, index) {
//		alert('racer '+index+' has bib '+racer.bib+' and status '+racer.status);
		racer.displayed = false;
		racer.index = counter;
		add_racer(racer.bib, index);
	});
});

function backup_race()
{
//	var stringy = JSON.stringify(race); WTF? This doesn't really work!
	var stringy = JSON.encode(race);
	localStorage.setItem('races', stringy);
//$('debug').innerHTML = localStorage.races;
}

function restore_race()
{
	var stringy = localStorage.getItem('races');
	race = JSON.decode(stringy);
//	race = JSON.parse(stringy);
//	results = JSON.parse(race.results);
//	race.results = results;
//	var tmp = race.results.length;
}

function init_race()
{
	race = {};
	race.id = 0;
	race.name = 'New Race';
	now = new Date;
	race.racedate = now.toDateString();
	race.results = new Array(); // result for every racer
	race.racers_start = 1; // racers per start time; 0 => mass start
	race.start_interval = 30; // seconds between starts
	race.start_bib = 0;
	race.fastest_time = 0;
	var now = new Date();
	race.race_start = now.getTime();
//	localStorage.races = new Array();
	backup_race();
	count = race.results.length;
//	alert(count+' racers');
}

function push_button(index)
{
//$('debug').innerHTML = localStorage.races;
	// Change the color of the number button so the user can see what they pushed
	if (last_button == index) {
		$('button_'+index).className = 'double_click';
	} else if (index != 'X' && index != '<') {
		$('button_'+index).className = 'single_click';
		if (last_button != null) $('button_'+last_button).className = 'no_click';
		last_button = index;
	} else if (last_button != null) {
		$('button_'+last_button).className = 'no_click';
		last_button = null;
	}

	if (timing_screen == true) { // Entering a bib number
		var bib = $('racerbibX');
	//	if (newbib == 0) bib.innerHTML = '';
	//	bib.innerHTML += index;
		if (index == '<') {
			newbib = parseInt(newbib / 10);
		} else if (index == 'X') {
			newbib = 0;
		} else {
			newbib = newbib*10 + index;
		}
		if (newbib == 0) {
			bib.innerHTML = '';
		} else {
			bib.innerHTML = newbib;
		}
	} else if (time_screen == true) {
		var hr = parseInt(enter_start_time.hr);
		var mn = parseInt(enter_start_time.mn);
		var sc = parseInt(enter_start_time.sc);
		hr = hr*10 + parseInt(mn/10);
		enter_start_time.hr = hr;
		mn = mn%10*10 + parseInt(sc/10);
		enter_start_time.mn = mn;
		if (mn < 10) mn = '0'+mn;
		sc = sc%10*10 + index;
		enter_start_time.sc = sc;
		if (sc < 10) sc = '0'+sc;
		$('start_time').innerHTML = hr+':'+mn+':'+sc;
	} else if (race_screen == true) {

	}
//	alert("pressed "+index);
}

//function reset_button()
//{
//	$$('#keypad input').
//}

function add_racer(addbib, index)
{
	// Clear the last button
	if (last_button != null) {
		$('button_'+last_button).className = 'no_click';
		last_button = null;
	}

	if (typeof addbib == 'undefined') {
		if (newbib == 0) return;
	} else {
		newbib = addbib;
	}
//	race.results.each
//	alert('Adding racer: '+newbib);
	if (results_screen) push_results();

	// Check for duplicate bib number
	race.results.each(function(racer, each_index) {
		if (each_index == index) return;
//		alert('looking at racer: \''+racer.bib+'\', compare to \''+newbib+'\'');
		if (parseInt(racer.bib) == parseInt(newbib) /* && racer.displayed == true */) {
//			alert('The same');
			newbib = -1;
			return;
		};
	});
	// Was this a duplicate?
	if (newbib < 0) return;

//	if (race.results[newbib]) {
//		alert('already have racer '+newbib);
//		return;
//	}
	if (typeof addbib == 'undefined') {
		race.results[counter] = {'bib': newbib, 'status': 'started', 'displayed': true, 'index': counter};
		backup_race();
		index = counter;
	}
	var racer = $('racerX');
	var newracer = racer.cloneNode(true);
	newracer.setAttribute('id', 'racer'+index);
	var bib = newracer.firstChild;
	while (bib.nodeType != 1) bib = bib.nextSibling;
	bib.setAttribute('id', 'racerbib'+index);
	bib.innerHTML = newbib;
	racer.parentNode.insertBefore(newracer, racer);
	var zerobib = $('racerbibX');
	zerobib.innerHTML = '  ';
	bib = bib.nextSibling;
	while (bib.nodeType != 1) bib = bib.nextSibling;
	var button = bib.firstChild;
	while (button.nodeType != 1) button = button.nextSibling;
	if (race.results[index].status == 'started') {
		button.value = 'Racing';
		button.setAttribute('class', 'racing');
	} else {
		button.setAttribute('class', 'done');
		set_time_str(index, true);
		var order_el = $$('#racer'+index+' td.race_rank');
		order_el[0].innerHTML = race.results[index].order;
		if (parseInt(race.results[index].order) > 1) $('racer'+index).style.display = 'none';
	}
	button.setAttribute('onclick', 'finish_racer('+index+')');
	newbib = 0;
	counter += 1;
}

function sort_racers(a, b)
{
//	var result;
//	if (a.race_time > b.race_time) result = 1;
//	else if (a.race_time < b.race_time) result = -1;
//	else if (a.race_time == b.race_time) result = 0;
//	else if (a.race_time > 0) result = -2;
//	else if (b.race_time > 0) result = 2;
//	else result = 0;
////	alert('comparing '+b.race_time+' to '+a.race_time+' to get '+result);
//	return result;

	if (a.race_time > b.race_time) return 1;
	if (a.race_time < b.race_time) return -1;
	if (a.race_time == b.race_time) {
//		if (a.index > b.index) return 1;
//		if (a.index < b.index) return -1;
		return 0;
	}
	if (a.race_time > 0) return -1;
	if (b.race_time > 0) return 1;
//	if (a.index > b.index) return 1;
//	if (a.index < b.index) return -1;
	return 0;
//	var diff = a.race_time - b.race_time;
//	alert('comparing '+b.race_time+' to '+a.race_time+' to get '+diff);
//	return diff;
//	return a.race_time - b.race_time;
}

function resort_racers(a, b)
{
	return a.index - b.index;
}

function reorder_racers()
{
	race.results.each(function(racer, each_index) {
		var elem = $('racer'+racer.index);
		elem.parentNode.insertBefore(elem, $('racerX'));
	});
}

function finish_racer(index)
{
	if (race.results[index].status == 'finished') return;

	// Hide all but the number one position
	race.results.each(function(racer, each_index) {
		if (racer.order > 1 && racer.status == 'finished') $('racer'+racer.index).style.display = 'none';
	});

	// Compute time since the race start
	var now = new Date();
	race.results[index].finish_time = now.getTime();
	var time = now.getTime() - race.race_start;
	// Adjust for start time
	var adjust = parseInt((race.results[index].bib - race.start_bib) / race.racers_start) * race.start_interval * 1000;
	time = adjust < time ? (time - adjust) : time;
//	if (time < 0) time = 0;
	race.results[index].race_time = time; //parseInt((time + 500) / 1000);
	race.results[index].status = 'finished';

	// Do we have the new fasted time?
	var reset_times = false;
	if (race.fastest_time == 0 || race.fastest_time > time) {
		race.fastest_time = time;
		reset_times = true;
	}

	// Sort the results
	race.results.sort(sort_racers);
	var order = 1;
//	var my_order = -1;
//	var debug = '';
	race.results.each(function(racer, each_index) {
		racer.order = order;
//debug += '<br/>'+racer.race_time+'('+racer.order+') - bib: '+racer.bib+', index: '+racer.index;
//		if (index == this_index) my_order = order;
		if (racer.race_time > 0) {
			order_el = $$('#racer'+racer.index+' td.race_rank');
			order_el[0].innerHTML = order;
			if (reset_times) set_time_str(each_index);
		}
		order += 1;
	});
	backup_race();
//$('debug').innerHTML = debug;
	reorder_racers();
	// Now put them back in the original order
	race.results.sort(resort_racers);

	// Change the button to "Done"
	fix_button(index, true);
}

function set_time_str(index)
{
	var time = race.results[index].race_time;
	if (time != race.fastest_time) time = time - race.fastest_time;
	var time_str = get_time(true, time);
	time = $$('#racer'+race.results[index].index+' td.race_time');
	time[0].innerHTML = time_str;
	return;
}

function fix_button(index)
{
	// Reset the button
	var button = $$('#racer'+index+' input');
//	button[0].onclick = null;
	button[0].value = 'Done';
	button[0].setAttribute('class', 'done');
	return;
}

function get_time(tenths, time)
{
	if (typeof time == 'undefined') {
		var now = new Date();
		time = now.getTime() - race.race_start;
	}
	if (tenths) time += 50; // round to the nearest 100 msec
	else time += 500; // round to the nearest 1000 msec
	var hours = parseInt(time / 3600000);
	time = time % 3600000;
	var mins = parseInt(time / 60000);
	if (mins < 10) mins = '0'+mins;
	time = time % 60000;
	var secs = parseInt(time / 1000);
	if (secs < 10) secs = '0'+secs;
	time = time % 1000;
	var tenths = tenths ? '.'+parseInt(time / 100) : '';
	var time_str = hours+':'+mins+':'+secs+tenths;
	return time_str;
}

function push_time()
{
	// Set the time
	var time_str = get_time(false);
	$('start_time').innerHTML = time_str;
	var now = new Date();
	var msec = 1001;
	start_timer = window.setTimeout(function() {
		start_timer = window.setInterval('update_start_time()', 1001);
		var time_str_x = get_time(false);
		$('start_time').innerHTML = time_str_x;
	}, msec);

	// Make the right screen visible
	var time = $('time_screen');
	$('timing_screen').style.display = 'none';
	timing_screen = false;
	time_screen = true;
	time.style.display = 'block';
}

function push_clear()
{
	if (confirm('Really clear?')) {
		init_race();
		while (counter > 0) {
			counter -= 1;
			var rem = $('racer'+counter);
			rem.parentNode.removeChild(rem);
		}
	}
}

function update_start_time()
{
	// Set the time
	var time_str = get_time(false);
	$('start_time').innerHTML = time_str;
}

function clear_time()
{
	window.clearTimeout(start_timer);
	window.clearInterval(start_timer);
	start_timer = null;
	$('start_time').innerHTML = '00:00:00';
	enter_start_time.hr = 0;
	enter_start_time.mn = 0;
	enter_start_time.sc = 0;
	backup_race();
}

function start_time()
{
//	alert('hr = '+enter_start_time.hr+', mn = '+enter_start_time.mn+', sc = '+enter_start_time.sc);
	var offset = enter_start_time.hr*3600000 + enter_start_time.mn*60000 + enter_start_time.sc*1000;
	var now = new Date();
	race.race_start = now.getTime() - offset;
	backup_race();
	if (start_timer == null) push_time();
}

function push_race()
{
	var race_s = $('race_screen');
	$('timing_screen').style.display = 'none';
	if (setup_screen) done_setup();
	if (results_screen) push_results();
	if (time_screen) dome_time();
	timing_screen = false;
	race_screen = true;
	race_s.style.display = 'block';
}

function push_results()
{
	race.results.each(function(racer, each_index) {
		if (results_screen && racer.order > 1) $('racer'+each_index).style.display = 'none';
		else $('racer'+each_index).style.display = 'table-row';
	});
	results_screen = results_screen ? false : true;
	return;

	var results_s = $('results_screen');
	$('timing_screen').style.display = 'none';
	timing_screen = false;
	results_screen = true;
	results_s.style.display = 'block';
}

function push_setup()
{
	var setup_s = $('setup_screen');
	$('timing_screen').style.display = 'none';
	timing_screen = false;
	setup_screen = true;
	setup_s.style.display = 'block';
}

function set_racers_start()
{
	race.racers_start = parseInt($('racers_start').value);
	if (race.racers_start == 0) {
		race.racers_start = 1;
		race.start_interval = 0; // seconds between starts
	}
	backup_race();
}

function set_racers_int()
{
	race.start_interval = parseInt($('start_int').value);
	backup_race();
}

function set_start_bib()
{
	race.start_bib = parseInt($('start_bib').value);
	backup_race();
}

function done_time()
{
	$('time_screen').style.display = 'none';
	time_screen = false;
	timing_screen = true;
	$('timing_screen').style.display = 'block';
	if (last_button != null) $('button_'+last_button).className = 'no_click';
}

function done_race()
{
	$('race_screen').style.display = 'none';
	race_screen = false;
	timing_screen = true;
	$('timing_screen').style.display = 'block';
	if (last_button != null) $('button_'+last_button).className = 'no_click';
}

function done_results()
{
	race.results.each(function(racer, each_index) {
		if (racer.order > 1) $('racer'+each_index).style.display = 'none';
	});
	if (last_button != null) $('button_'+last_button).className = 'no_click';
	return;

	$('results_screen').style.display = 'none';
	results_screen = false;
	timing_screen = true;
	$('timing_screen').style.display = 'block';
}

function done_setup()
{
	$('setup_screen').style.display = 'none';
	setup_screen = false;
	timing_screen = true;
	$('timing_screen').style.display = 'block';
}

function push_upload()
{
	var stringy = JSON.encode(race);
	var jsonRequest = new Request.JSON({
		'method':'post',
		'data': {'race':stringy},
		'url': 'http://localhost/race-timer/index.php/race/upload',
		'onSuccess': function(result){
			if (result.status == 'success') {
				race.id = result.id;
				alert('Uploaded!');
			} else {
				alert('Failed to upload!');
			}
		}
	}).send();
}