var email_data = {
	first_storey:[], //массив состоящий из объектов {title:'Название', area: 0}
	second_storey:[], //массив состоящий из объектов {title:'Название', area: 0} ИЛИ false - если дом одноэтажный
	areas:{
		first_storey:0, //плошадь первого этажа
		second_storey:0, //плошадь второго этажа ИЛИ false - если дом одноэтажный
		total_area:0, //общая плошадь
		total_building_area:0 //общая строительная плошадь
	},
	options:{
		foundation:'', //Фундамент
		wall_panel:'', //Стеновой брус
		roof_covering:'' //Кровельное покрытие
	},
	price:0 //цена
};

(function($){
$(function(){
	var before_device_type = false,
		device_type,
		mobile,
		desktop,
		init_func = [];

	var resize_function = function() {
		device_type = $('#mobile_or_desktop').is(':visible') ? 'desktop' : 'mobile';
		mobile = device_type === 'mobile';
		desktop = device_type === 'desktop';

		if(before_device_type===device_type){
			return;
		}

		for(var i =0;i<init_func.length;i++){
			init_func[i]();
		}

		before_device_type = device_type;
	};

	$(window).on('resize', resize_function);

	setTimeout(function(){
		$(window).trigger('resize');
	}, 100);

	$(window).on('load', function(){
		$(window).trigger('resize');
	});


	var allow_room_hover = true,
		allow_calculate = true,
		two_storeys = true,
		room_preview = $('#room_preview'),
		room_preview_parent = room_preview.parent(),
		room_title = room_preview.find('.room_title'),
		room_img = room_preview.find('.room_img'),
		calculator_wrapper = $('#calculator'),
		calculator = calculator_wrapper.find('.calculator'),
		el = {
			first_storey_height : $('#first_storey_height'),
			second_storey_height : $('#second_storey_height'),
			first_storey_area : calculator_wrapper.find('[data-first_storey_area="1"]'),
			second_storey_area : calculator_wrapper.find('[data-second_storey_area="1"]'),
			area : calculator_wrapper.find('[data-area="1"]'),
			building_area : calculator_wrapper.find('[data-building_area="1"]'),
			price_options : $('#price_options'),
			foundation : $('#foundation'),
			wall_panel : $('#wall_panel'),
			roof_covering : $('#roof_covering'),
			warning : $('#warning'),
			price : $('#price'),
		},
		id_from = 0,
		saved_data = JSON.parse( localStorage.getItem(save_key) ),
		save_data = saved_data ? saved_data : {
			storeys: [
				{
					bedrooms_count:0,
					rooms:[],
				},
				{
					enabled:two_storeys,
					bedrooms_count:0,
					rooms:[],
				}
			]
		},
		last_area = 0;

	function round(value){
		return Math.round(value*100)/100;
	}

	function calculate(){
		if(!allow_calculate){
			return;
		}

		var storeys = [{name:'first', check_area:0, building_area:0}],
			all_area_val = 0,
			all_building_area_val = 0,
			total_cost = 0,
			foundation = el.foundation.find('input:checked'),
			wall_panel = el.wall_panel.find('input:checked'),
			roof_covering = el.roof_covering.find('input:checked'),
			first_storey_height = el.first_storey_height.find('input').val(),
			second_storey_height = el.second_storey_height.find('input').val(),
			second_light = false;

		if(two_storeys){
			storeys.push({name:'second', area:0, building_area:0});
			save_data.storeys[1].enabled = true;
			email_data.second_storey = [];
		}else{
			email_data.second_storey = false;
			email_data.areas.second_storey = false;

			save_data.storeys[1].enabled = false;
			save_data.storeys[1].bedrooms_count = calculator.find('.second_storey .room.bedroom:not(.hidden)').length;
		}

		for(var i =0;i<storeys.length;i++){
			var all_storey_area_val = 0,
				all_storey_building_area_val = 0,
				storey_area_val = 0;

			calculator.find('.'+storeys[i].name+'_storey input[type="hidden"]:not(:disabled)').each(function(){
				var val = $(this).val();
				if($.trim(val).length===0){
					val = 0;
				}
				val = parseFloat(val);

				all_storey_building_area_val += val;
				if(!$(this).is('[data-only_building_area="1"]')){
					all_storey_area_val += val;
				}
				if($(this).is('[data-check="1"]')){
					storey_area_val += val;
				}
			});

			email_data[storeys[i].name+'_storey'] = [];

			save_data.storeys[i].rooms = [];
			save_data.storeys[i].bedrooms_count = 0;

			calculator.find('.'+storeys[i].name+'_storey .room:visible').each(function(room){
				var room_title = $(this).find('.big_title');

				if($(this).is('.bedroom')){
					save_data.storeys[i].bedrooms_count++;
				}

				save_data.storeys[i].rooms[room] = {options:[]};

				if(room_title.length){
					var value = room_title.nextAll('.numbers').find('input[type="hidden"]').val();
					email_data[storeys[i].name+'_storey'].push({title:room_title.text(), area:value});
					save_data.storeys[i].rooms[room].slider = value;
				}

				$(this).find('input:checkbox').each(function(option){
					save_data.storeys[i].rooms[room].options[option] = {checked:$(this).is(':checked'),value:$(this).val()};

					if($(this).is(':checked:not(:disabled)')){
						email_data[storeys[i].name+'_storey'].push({title:$(this).next('label').text(), area:$(this).val()});
					}
				});
			});

			calculator.find('.'+storeys[i].name+'_storey input:checked:not(:disabled)').each(function(){
				var val = parseFloat($(this).val());

				all_storey_building_area_val += val;
				if(!$(this).is('[data-only_building_area="1"]')){
					all_storey_area_val += val;
				}
				if($(this).is('[data-check="1"]')){
					storey_area_val += val;
				}

				if(i===1 && $(this).is('[data-second_light="1"]')){
					second_light = val;
				}
			});

			storeys[i].check_area = storey_area_val;
			storeys[i].area = all_storey_area_val;
			storeys[i].building_area = all_storey_building_area_val*building_area_coeff;

			all_area_val += storeys[i].area;
			all_building_area_val += storeys[i].building_area;

			el[storeys[i].name+'_storey_area'].find('.value span').text(round(storeys[i].area));

			email_data.areas[storeys[i].name+'_storey'] = round(storeys[i].area);
		}

		if(two_storeys && second_light!==false){
			storeys[1].check_area -= second_light;
		}

		if(two_storeys && storeys[1].check_area>storeys[0].check_area){
			var diff = storeys[1].check_area-storeys[0].check_area;

			if(diff/storeys[0].check_area>storeys_diff_percent/100){
				el.warning.css('display', 'block').find('.value span').text(round(diff));
				el.warning.find('[data-second_light="0"]').css('display', second_light!==false ? 'none' : 'inline');
				el.warning.find('[data-second_light="1"]').css('display', second_light!==false ? 'inline' : 'none')
			}else{
				el.warning.css('display', '');
			}
		}else{
			el.warning.css('display', '');
		}

		save_data.foundation = foundation.closest('.option').prevAll('.option').length;
		save_data.wall_panel = wall_panel.closest('.option').prevAll('.option').length;
		save_data.roof_covering = roof_covering.closest('.option').prevAll('.option').length;

		if(first_storey_height<min_first_storey_height){
			first_storey_height = min_first_storey_height;
		}else if(first_storey_height>max_first_storey_height){
			first_storey_height = max_first_storey_height;
		}

		save_data.first_storey_height = first_storey_height;
		save_data.second_storey_height = second_storey_height;

		el.first_storey_height.css('height', mobile ? 'auto' : (first_storey_height*(two_storeys ? two_storey_img_height_px_coeff : one_storey_img_height_px_coeff)));

		total_cost += storeys[0].building_area * foundation.attr('data-price');
		total_cost += storeys[0].building_area * first_storey_height * (two_storeys ? wall_panel.attr('data-first_storey_price') : wall_panel.attr('data-second_storey_price') );

		var biggest_area = storeys[0].building_area;
		if(two_storeys){
			el.second_storey_height.css('height', mobile ? 'auto' : (second_storey_height*two_storey_img_height_px_coeff));

			total_cost += storeys[1].building_area * second_storey_height * wall_panel.attr('data-second_storey_price');

			if(storeys[1].building_area>storeys[0].building_area){
				biggest_area = storeys[1].building_area;
			}
		}

		total_cost += (two_storeys ? storeys[1].building_area : storeys[0].building_area) * covering_cost_coeff;
		total_cost += biggest_area * roof_covering.attr('data-price');

		if(all_area_val!=last_area){
			last_area = all_area_val;

			if(window.reloadProjects){
				window.reloadProjects(round(last_area));
			}
		}

		el.area.find('.value span').text(round(all_area_val));
		el.building_area.find('.value span').text(round(all_building_area_val));

		total_cost = Math.round(total_cost)+'';

		email_data.options.foundation = foundation.next('label').text();
		email_data.options.wall_panel = wall_panel.next('label').text();
		email_data.options.roof_covering = roof_covering.next('label').text();
		email_data.areas.total_area = round(all_area_val);
		email_data.areas.total_building_area = round(all_building_area_val);
		email_data.price = Math.round(total_cost);

		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(total_cost)) {
			total_cost = total_cost.replace(rgx, '$1' + ' ' + '$2');
		}

		el.price.text(total_cost + ' руб');

		localStorage.setItem(save_key, JSON.stringify(save_data) );
	}

	function updateSlider(data, curr_slider, values, input){
		var active = values.eq(data.value),
			value = active.attr('data-value'),
			room = curr_slider.closest('.room');

		if(mobile){
			focus_room(room);
		}

		curr_slider.closest('.data').find('.info span').text(value);
		input.val(value);

		if(input.is('[data-copy_to]')){
			$('#'+input.attr('data-copy_to')).val(value);
		}

		room.removeClass('empty');

		room_img.attr('src', active.attr('data-img'));

		values.removeClass('active');
		active.addClass('active');
		calculate();
	}

	function start_slider(el){
		var curr_slider = el,
			numbers = curr_slider.next('.numbers'),
			input = numbers.find('input[type="hidden"]'),
			values = numbers.find('span'),
			active = values.filter('.active'),
			value = active.attr('data-value');

		input.val(value);
		curr_slider.closest('.data').find('.info span').text(value);

		if(input.is('[data-copy_to]')){
			$('#'+input.attr('data-copy_to')).val(value);
		}

		var slider = curr_slider.slider({
			orientation: "horizontal",
			range: "min",
			min: 0,
			max: values.length - 1,
			value: active.prevAll('span').length,
			step: 1,
			slide: function(e, data){updateSlider(data, curr_slider, values, input)},
			change: function(e, data){updateSlider(data, curr_slider, values, input)},
			start: function(e, data){
				var room = curr_slider.closest('.room');

				if(mobile){
					focus_room(room);
				}

				allow_room_hover = false;
			},
			stop: function(e, data){
				allow_room_hover = true;
			}
		});

		curr_slider.on('destroy', function(){
			slider.slider('destroy');
		});

		numbers.on('set_value', 'span', function(e, data){
			slider.slider('value', data);
		});
	}

	$('.slider').each(function(){
		start_slider($(this));
	});

	function start_spinner(el){
		var curr_input = el,
			storey_height_el = curr_input.is('[data-storey_height="1"]'),
			last_value = curr_input.val(),
			checkbox = curr_input.closest('.spinner_wrapper').prevAll('input:checkbox');

		if(checkbox.length){
			curr_input.val(checkbox.val()).attr('data-default_value', checkbox.attr('data-default_value'));
		}

		var spinner = curr_input.spinner({
			step: !storey_height_el ? 1 : 0.1,
			min: storey_height_el ? (el.closest('#first_storey_height').length ? min_first_storey_height : min_second_storey_height) : 1,
			max : storey_height_el ? (el.closest('#first_storey_height').length ? max_first_storey_height : max_second_storey_height) : null,
			disabled: checkbox.length>0 && !checkbox.is(':checked'),
			numberFormat: "n",
			spin: function(event, ui){
				last_value = ui.value;

				if(storey_height_el){
					setTimeout(calculate, 0);
				}else{
					checkbox.val(last_value).trigger('change');
				}
			},
			change: function(event, ui){
				var val = $(this).val(),
					new_val = val.replace(/[^0-9.]*/g, ''),
					spinner_val = spinner.spinner("value");

				if(spinner_val!==null){
					last_value = spinner_val;
				}

				if(new_val<1){
					new_val = 1;
				}

				if(new_val!=val){
					$(this).val(new_val);
				}

				spinner_val = spinner.spinner("value");

				if(spinner_val===null){
					$(this).val(last_value);
					last_value = spinner.spinner("value");
				}else{
					last_value = spinner_val;
				}

				if(storey_height_el){
					calculate();
				}else{
					checkbox.val(last_value).trigger('change');
				}
			}
		});

		curr_input.on('focus', function () {
			if(mobile){
				$(this).trigger('blur');
			}
		});

		curr_input.on('set_value', function(e, data){
			spinner.spinner('value', data);
		});

		curr_input.on('destroy', function(e){
			spinner.spinner('destroy');
		});

		checkbox.on('change', function(){
			if($(this).is(':checked')){
				spinner.spinner("enable");
			}else{
				spinner.spinner("disable");
			}
		});
	}

	$('.spinner').each(function(){
		start_spinner($(this));
	});

	var active_room = $();

	function change_storeys(add, add_classes){
		var input_status;
		if(add){
			two_storeys = true;
			input_status = false;
			el.second_storey_area.removeClass('disabled');

			if(add_classes){
				calculator_wrapper.removeClass('one_storey');
			}
		}else{
			two_storeys = false;
			input_status = true;
			el.second_storey_area.addClass('disabled').find('.value span').text(0);

			if(add_classes){
				calculator_wrapper.addClass('one_storey');

				if(active_room.length>0 && active_room.closest('.second_storey').length>0){
					active_room.removeClass('active');
					room_preview.css({display:'none'});
				}
			}
		}

		calculator.find('.storey.second_storey input').add(el.second_storey_height).prop('disabled', input_status);
	}

	function set_bedrooms(storey, need_bedrooms){
		var bedrooms = storey.find('.room.bedroom'),
			bedrooms_count = bedrooms.not('.hidden').length;

		if(bedrooms_count===0 && need_bedrooms>0){
			bedrooms.eq(0).prev('.room').find('.add_room_btn').trigger('click');
			bedrooms_count++;
		}
		if(bedrooms_count<need_bedrooms){
			need_bedrooms = need_bedrooms-bedrooms_count;
			for(var i = 0;i<need_bedrooms;i++){
				bedrooms.last().find('.add_room_btn').trigger('click');
				bedrooms = storey.find('.room.bedroom');
			}
		}else if(bedrooms_count>need_bedrooms){
			var delete_bedrooms = bedrooms_count-need_bedrooms;
			for(var z = 0;z<delete_bedrooms;z++){
				bedrooms.last().find('.del_bedroom').trigger('click');
				bedrooms = storey.find('.room.bedroom');
			}
		}
	}

	 function focus_room(room){
		if((desktop && !allow_room_hover) || room.is('.active')){
			return;
		}
		active_room.removeClass('active');
		active_room = room.addClass('active');

		if(mobile){
			var curr_pos = room.position().top,
				curr_scroll_top = $(window).scrollTop();
			room_preview.css({display:'block', 'margin-top': '0px'}).insertAfter(active_room.find('.data'));

			setTimeout(function(){
				var diff = room.position().top-curr_pos;
				$(window).scrollTop(curr_scroll_top+diff);
			}, 0);
		}else{
			room_preview.css({display:'block', 'margin-top': room.position().top + 20});
		}
		room_title.text(active_room.attr('data-preview_title'));
		room_img.attr('src', active_room.find('.numbers .active').attr('data-img'));
	}

	calculator_wrapper.on('click', '[data-action]', function (){
		var action = $(this).attr('data-action');

		switch(action){
			case 'add_second_storey':
				change_storeys(true, true);
			break;

			case 'remove_second_storey':
				change_storeys(false, true);
			break;

			case 'show_results':
				allow_room_hover = false;
				var scrollTo = $('#results').offset().top,
					fixed_header = $('header.fixed');
				if(fixed_header.length){
					scrollTo -= fixed_header.outerHeight();
				}

				$("body,html").animate({"scrollTop":scrollTo}, 800, function(){
					allow_room_hover = true;
				});

				return;
			break;

			case 'reset':
				allow_calculate = false;

				var storeys = calculator.find('.storey:not(.add_second_storey)');
				storeys.each(function(){
					var need_bedrooms = $(this).attr('bedrooms_count');
					set_bedrooms($(this), need_bedrooms);
				});

				el.price_options.find('[data-default_check="1"]').prop('checked', true);
				el.first_storey_height.add(el.second_storey_height).add(calculator).find('input.spinner').each(function(){
					$(this).trigger('set_value', [$(this).attr('data-default_value')]);
				});

				calculator.find('input:checkbox').each(function(){
					$(this).prop('checked', !!parseInt($(this).attr('data-default_check'))).trigger('change');
				});

				calculator.find('.slider_wrapper .numbers [data-default="1"]').each(function(){
					if($(this).is('.active')){
						return;
					}

					$(this).trigger('set_value', [$(this).prevAll('span').length]);
				});

				var second_storey = storeys.filter('.second_storey');
				if(!two_storeys && second_storey.is('[data-default_show="1"]')){
					change_storeys(true, true);
				}else if(two_storeys && second_storey.is('[data-default_show="0"]')){
					change_storeys(false, true);
				}

				allow_calculate = true;
			break;
		}

		calculate();
	});

	if(calculator_wrapper.is('.one_storey')){
		change_storeys(false, false);
	}

	function add_room_toggle(bedrooms_length, storey){
		if(bedrooms_length>=6){
			storey.addClass('disable_add_bedrooms');
		}else{
			storey.removeClass('disable_add_bedrooms');
		}
	}

	calculator.on('click', '.add_room_btn', function(){
		var room = $(this).closest('.room'),
			is_bedroom = room.is('.bedroom'),
			bedrooms = room.siblings('.bedroom'),
			storey = room.closest('.storey');

		var bedrooms_length = bedrooms.length;
		if(is_bedroom){
			bedrooms_length++;
		}

		if(bedrooms_length>=6){
			add_room_toggle(bedrooms_length, storey);
			return;
		}

		if(is_bedroom){
			room = room.clone().insertAfter(room);
			room.removeClass('active');
			var slider = room.find('.slider').html('').removeClass().addClass('slider');
			start_slider(slider);

			room.find('input:checkbox').each(function(){
				var new_id = 'checkbox_'+id_from;

				while($('#'+new_id).length>0){
					id_from++;
					new_id = 'checkbox_'+id_from;
				}

				$(this).attr('id', new_id);
				$(this).next('label').attr('for', new_id);

				id_from++;
			});

			room.find('.spinner_wrapper').each(function(){
				var input = $(this).find('input');

				if(!input.is('.spinner')){
					return;
				}

				var spinner = $('<input/>', {type:'text', value:input.val(), 'class':'spinner'}).prependTo($(this));

				$(this).find('.ui-spinner').remove();

				start_spinner(spinner);
			});


			bedrooms_length++;

			add_room_toggle(bedrooms_length, storey);
		}else{
			var bedroom = room.next('.bedroom.hidden').removeClass('hidden');
			room.addClass('hidden');

			bedroom.find('.numbers input[type="hidden"]').prop('disabled', false);
			bedroom.find('.option input[type="checkbox"]').prop('disabled', false);
		}

		calculate();
	}).on('click', '.del_bedroom', function(){
		var room = $(this).closest('.room'),
			bedrooms = room.siblings('.bedroom'),
			storey = room.closest('.storey');

		var bedrooms_length = bedrooms.length+1;

		if(active_room[0]===room[0]){
			active_room.removeClass('active');
			room_preview.css({display:'none'});
		}

		bedrooms_length--;
		if(bedrooms_length>=1){
			room.find('.slider').trigger('destroy');
			room.find('.spinner_wrapper input.spinner').trigger('destroy');
			room.remove();
		}else{
			room.addClass('hidden');
			room.prev('.room.hidden').removeClass('hidden');

			room.find('.numbers input[type="hidden"]').prop('disabled', true);
			room.find('.option input[type="checkbox"]').prop('disabled', true);
		}

		add_room_toggle(bedrooms_length, storey);
		calculate();
	});

	calculator_wrapper.on('mouseover', '.room[data-room_preview="1"]', function(){
		if(desktop){
			focus_room($(this));
		}
	}).find('.storey:not(.add_second_storey)').each(function(){
		var bedrooms = $(this).find('.bedroom'),
			visible_bedrooms = bedrooms.filter('[data-default="1"]'),
			bedrooms_length = visible_bedrooms.length;

		$(this).attr('bedrooms_count', bedrooms_length);

		allow_calculate = false;
		bedrooms.filter('[data-default="0"]').find('.del_bedroom').trigger('click');
		allow_calculate = true;

		if(bedrooms_length>0){
			bedrooms.eq(0).prev('.room').addClass('hidden');
		}

		add_room_toggle(bedrooms_length, $(this));
	});

	try{
		if(saved_data){
			allow_calculate = false;

			if(saved_data.storeys[1].enabled===false && two_storeys){
				change_storeys(false, true);
			}else if(two_storeys && saved_data.storeys[0].enabled!==false){
				change_storeys(true, true);
			}

			var storeys = calculator.find('.storey:not(.add_second_storey)');
			storeys.each(function(storey){
				var need_bedrooms = saved_data.storeys[storey].bedrooms_count;
				set_bedrooms($(this), need_bedrooms);

				$(this).find('.room:not(.hidden)').each(function(room){
					var room_title = $(this).find('.big_title');
					if(room_title.length){
						var slider_val = saved_data.storeys[storey].rooms[room].slider,
							need_number = room_title.nextAll('.numbers').find('span[data-value="'+slider_val+'"]');

						if(need_number.length){
							need_number.trigger('set_value', [need_number.prevAll('span').length]);
						}
					}

					$(this).find('input:checkbox').each(function(option){
						$(this).prop('checked', saved_data.storeys[storey].rooms[room].options[option].checked);

						var input = $(this).nextAll('.spinner_wrapper').find('input');
						if(input.is('.spinner')){
							input.trigger('set_value', [saved_data.storeys[storey].rooms[room].options[option].value]);
						}
					})
				});
			});

			el.foundation.find('input:radio').eq(saved_data.foundation).prop('checked', true);
			el.wall_panel.find('input:radio').eq(saved_data.wall_panel).prop('checked', true);
			el.roof_covering.find('input:radio').eq(saved_data.roof_covering).prop('checked', true);

			el.first_storey_height.find('input').val(saved_data.first_storey_height);
			el.second_storey_height.find('input').val(saved_data.second_storey_height);

			allow_calculate = true;
		}
	}catch(e){
		allow_calculate = true;
	}

	calculate();

	el.price_options.on('change', 'input:radio', calculate);
	calculator.on('change', 'input:checkbox', calculate);

	var show_message = function(title){
		$('#message_popup').addClass('show').find('.title').text(title);
	};

	$('.popup_wrapper').on('click', function (){
		$(this).closest('.popup_wrapper').removeClass('show');
	}).on('click', '.popup', function (e){
		e.stopPropagation()
	}).on('click', '.close', function (){
		$(this).closest('.popup_wrapper').trigger('click');
	});

	$('#send_house').on('click', function (){
		$('#send_house_popup').addClass('show');
	});

	/*$('#send_house_popup').on('submit', 'form', function (e){
		e.preventDefault();
		var form = $(this);

		if(form.is('.loading')){
			return;
		}

		var name = $.trim($(this).find('[name="name"]').val()),
			email = $.trim($(this).find('[name="email"]').val());

		if(name.length==0){
			show_message('Введите имя');
			return;
		}

		if(email.length==0){
			show_message('Введите email');
			return;
		}

		form.addClass('loading');

		$.ajax({
			url:'mail.php',
			type:'POST',
			processData:true,
			cache:false,
			data: $.param({data:JSON.stringify(email_data), name:name, email:email}),
			error:function (XMLHttpRequest, textStatus, errorThrown){
				show_message('Произошла неизвестная ошибка, повторите попытку позже.');
				form.removeClass('loading');
			},
			success:function (data, textStatus){
				show_message('Данные успешно отправлены. Спасибо!');
				form.removeClass('loading');
				form.trigger('reset');
			}
		});
	});*/

	$('#send_request').on('click', function (){
		$('#send_request_popup').addClass('show');
	});

	$('body').on('submit', 'form', function (e){
		var form = $(this);

		var error = false;
		form.find('[data-required]').each(function(){
			var val = $.trim($(this).val()),
				error_msg = $(this).data('required');

			if(val.length==0){
				show_message(error_msg);

				error = true;
				return false;
			}
		});

		if(error){
			e.preventDefault();
			e.stopImmediatePropagation();
		}
	});

	/*$('#send_request_popup').on('submit', 'form', function (e){
		e.preventDefault();
		var form = $(this);

		if(form.is('.loading')){
			return;
		}

		var name = $.trim($(this).find('[name="name"]').val()),
			phone = $.trim($(this).find('[name="phone"]').val()),
			email = $.trim($(this).find('[name="email"]').val());

		if(name.length==0){
			show_message('Введите имя');
			return;
		}

		if(phone.length==0){
			show_message('Введите телефон');
			return;
		}

		if(email.length==0){
			show_message('Введите email');
			return;
		}

		form.addClass('loading');

		$.ajax({
			url:'send_request.php',
			type:'POST',
			processData:true,
			cache:false,
			data: $.param({name:name, phone:phone, email:email}),
			error:function (XMLHttpRequest, textStatus, errorThrown){
				show_message('Произошла неизвестная ошибка, повторите попытку позже.');
				form.removeClass('loading');
			},
			success:function (data, textStatus){
				show_message('Спасибо за заявку!');
				form.removeClass('loading');
				form.trigger('reset');
			}
		});
	});*/

	var instruction_slider = false;
	init_func.push(function(){
		if(desktop){
			if(room_preview.parent()[0]!==room_preview_parent[0]){
				room_preview.prependTo(room_preview_parent);
			}
		}
	});

	init_func.push(function(){
		if(mobile){
			if(instruction_slider===false){
				instruction_slider = new Swiper('.swiper-container', {
					loop:true,
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev'
					},
					pagination: {
						el: '.swiper-pagination',
						type: 'bullets',
						clickable:true,
					}
				});
			}
		}else{
			if(instruction_slider!==false){
				instruction_slider.destroy();
				instruction_slider = false;
			}
		}
	});

	var fixed_sidebar = $('#fixed_sidebar'),
		total_price = $('#results .total_price');
	$(window).on("scroll", function(){
		if(total_price.offset().top<$(window).scrollTop() + $(window).height()){
			if(fixed_sidebar.is(':visible:not(:animated)')){
				fixed_sidebar.fadeOut();
			}
		}else{
			if(!fixed_sidebar.is(':visible:not(:animated)')){
				fixed_sidebar.fadeIn();
			}
		}
	});
})
})(jQuery);