<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>			
		<meta http-equiv="Content-Language" content="EN"/>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>Creating another Carousel: Karousel</title>
		<meta name="description" content="jQuery Carousel, lets you practically everyting! by Kalarrs Topham"/>
		<meta name="keywords" content="JQuery, Carousel, Slide, Tabbing, Tabber, Kalarrs, Topham, Kalarrs Topham"/>
		<meta charset="utf-8">				
		<meta http-equiv="X-UA-Compatible" contents="IE=edge,chrome=1">
		<meta name="viewport" contents="width=device-width">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js" type="text/javascript"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js" type="text/javascript"></script>

		<script type="text/javascript">
			//<![CDATA[
				( function($) {
					// extend the jQuery object by adding the reverse array method from the array prototype
					$.fn.reverse = [].reverse;
					
					$.karousel = function(context,settings) {
						t = this; //Store this instance of Karosel.  This is so a page can have mutiple karousels!
						$karousel = $(context); //Store the karousel element
						
						// [Left , Right] (Used to dertermine position)  if Left > Right then the loop has items from the end to the right
						// Start at 0 because we haven't loaded any images yet
						t.position = [0,0];
						
// TODO : Gracefull Error Handeling
// TODO : error Object or Message Object? Update: started a error object, now I want to convert this into a message object.
						t.error = {
							'Fatal': [],
							'Warning' : [],
							'Unknown' : []
						};

						// t.s = Settings
						t.s = $.extend(true,{}, {
							mode: 'manual', // manual: will not tranistion ie waits for user to trigger transistion, auto: will use the transition.duration to call the transition. transtion automatically.
							items: { // items object contains all of the attributes about the items in the karousel
								perPage: 1, // How many items are visible at a time in the mask
// TODO: Possibly move items.total into the AJAX methods.
								total: null, // How many items in total their are.  Used in conjunction with AJAX so the karousel can determine the correct call to make
								selector: '.items > li', // The selctore that get the wrapper of the itmes.
								fetch: { // Allows the items to be fetched from the docuemnt or from an ajax call
									from: 'document', // document will retrieve the items from the karousel element upon load.
// TODO: Possibly move items.fetch.frequency and items.fetch.url into the AJAX methods?
									frequency: 'once', // Will store the items and not call the fetch method again.
									url: '' // Where if ajax is used
								},
								repeat: false, // If true the carosel will not appeat to have an end.
								size: 'auto', // Have this plugin calculate the width/height based on items.perPage (includes padding and margin on each li)
								buffer: true, // Add an addition margin value to the calulation of the li width/height (does interal check that margin is only on one side)
								orientation: 'horizontal',
								direction: 'auto',
								defaultCss: {},  // Css can be passed to be applied to the items by default.  Usefull in AJAX
								container: '.items' // Selector for the items
							},
							transition: {
								moveNumberOfItems: 1, // By default when trasition is called how many items to move by
								moveNumberOfPages: 1, // By default how many pages to move by default
								speed: 500, //
								duration: 5000,
								type: 'slide',
// TODO: Remove by: [page,item]?  Since I don't have a next but a nextPage and nextItem?								
								by: 'page', // page or item. By default the transition will move the number of items
								createEmptyItems: false, // When the repeat is FALSE then do we move the carousel to the end thus changing the transitions page or do we change the page move to an item move.
								orientation: 'auto',
								type: 'slide'
							},
							mask: {
								size: '400px',
								overflowMarginHorizontal: 'right',
								overflowMarginVertical: 'bottom',
								defaultCss: {'position':'relative','overflow':'hidden'} // This makes the mask hide the non visible itmes.  However if one wanted the items could be visilbe. etc.
							}
						}, settings);
						
						t.pub = {
							'rotate': function(obj) {
								t.in.rotate(obj);
							},
							'play': function() {
								t.in.play();
							},
							'pause': function() {
								t.in.pause();
							},
							'moveNextPage': function(x) {
								x = x || (t.s.transition.moveNumberOfPages * t.s.items.perPage);
								t.in.rotate({
									'direction': 'next',
									'speed' : t.s.transition.speed,
									'items' : x
								});
							},
							'movePrevPage': function(x) {
								x = x || (t.s.transition.moveNumberOfPages * t.s.items.perPage);
								t.in.rotate({
									'direction': 'prev',
									'speed' : t.s.transition.speed,
									'items' : x
								});
							},
							'moveNextItem': function(x) {
								x = x || t.s.transition.moveNumberOfItems;
								t.in.rotate({
									'direction': 'next',
									'speed' : t.s.transition.speed,
									'items' : x
								});
							},
							'movePrevItem': function(x) {
								x = x || t.s.transition.moveNumberOfPages;
								t.in.rotate({
									'direction': 'prev',
									'speed' : t.s.transition.speed,
									'items' : x
								});
							},
							'items': function() {

							},
							'transtion': function() {
							}
						}

						t.in = {
// TODO : Remove Valid URL - handle in the ajax function! Weeeee.
							'validURL': function(url) {
								return url.match(/^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/);
							},
							'addError': function(type,message) { // Basic Error Handling.
								if(typeof t.error[type] == 'undefined') {
									t.error('Unknown',message);
								} else {
									if(message.length > 0 ) {
										t.error[type].push(Date() + " \n " + message)
									} else {
										t.error('Unknown','An error was thrown, however the message empty');
									}
								}
							},
							'outputError': function(type) {
								type = type || 'all';
								switch(type) {
									case 'all':
										$.each(t.error,function(k,v) {
											if(v.length > 0) {
												console.group(k);
												$.each(v,function(v2) {
													console.log(v[v2]);
												});
												console.groupEnd();
											}
										});
									break;
									default:
										if(typeof t.error[type] == 'undefined') {
											var validTypes = [];
											$.each(t.error,function(k,v) {
											   validTypes.push(k);
											});
// TODO: Make sure to check if console exists, otherwise output as a dialog window.
// TODO: Add a "SupressMessages" option so in production enviroment the carousle does not output anything.
											t.in.addError('Warning','An invalid type supplied to the outputError method.\nType supplied: '+type+'\nValid types are ['+validTypes.join(',')+',all]');
											t.outputError('Warning');
										} else {
											if(t.error[type].length > 0) {
												console.group(type);
												$.each(t.error[type],function(v2) {
													console.log(v2);
												});
												console.groupEnd();
											}
										}
									break;
								}
							},
							'itemSize': function(m1,m2,mt) {
								if(t.s.items.size == 'auto') {
									if(t.s.items.buffer && (margin = ( m1 === 0 || m2 === 0)) ) {
										t.itemSize = Math.floor((t.s.mask.size - (t.s.items.perPage * (m1+m2)) -  (mt)) / t.s.items.perPage);
										t.maskMargin = (t.s.mask.size - (t.s.items.perPage * (mt)) -  (mt)) % t.s.items.perPage;
									} else {
										if(!margin) {
											t.in.addError('Warning','Margin was found on both sides of the karousel items.  Buffer is going to be ignored.');
										}
										// Set the width/height of each li.  This is based on the desired witdth of the carousel / the number of visible items
										t.itemSize = Math.floor((t.s.mask.size - (t.s.items.perPage * (mt))) / t.s.items.perPage);
										t.maskMargin = (t.s.mask.size - (t.s.items.perPage * (mt))) % t.s.items.perPage;
									}
								} else {
									if(parseInt(t.s.items.size) > 0) {
										t.itemSize = t.s.items.size;
									} else {
										t.in.addError('Fatal','An invalid items.size value was suppled to the karousel.\nType supplied: ' + t.s.items.size+'\nValid types are int. with optional endings : [\'px\',\'em\',\'%\',]');
										t.in.outputError();
										return false;
									}
								}
								t.itemOuterSize = t.itemSize + (mt);
								return true;
							}
							,
							'init': function() {
								// Check the item fetch from value.
								switch(t.s.items.fetch.from) {
									case 'document':
										// Store the container for the items
										if( !(t.$karousel = $(t.s.items.container)).length > 0) {
											t.in.addError('Fatal','Unable to find items container to use for the karousel.\nSelector supplied: ' + t.s.items.container+'\njQuery matched these items'+$(t.s.items.container));
											t.in.outputError();
											return;
										}
										// Check to see if items exist.
// TODO : change the selector to be a filter of Container?
										if( (t.$items = $(t.s.items.selector,$karousel)).length > 0) {
											t.in.setupKarousel();
										} else {
											t.in.addError('Fatal','Unable to find items to use for the karousel.\nSelector supplied: ' + t.s.items.selector+'\njQuery matched these items'+$(t.s.items.selector));
											t.in.outputError();
											return;
										}
										
									break;
									case 'ajax':
										try {
											if((t.s.items.total = parseInt(t.s.items.total)) > 0) throw 'When using ajax to fetch items a items.total must be supplied.  This is how the ajax function determines how many items total, then it fetches them appropiately.';
											if(t.s.items.fetch.frequency != ('once' || 'every')) throw 'An invaild items.fetch.frequency was supplied to the karousel.\Value supplied: ' + t.s.items.fetch.frequency+'\nValid types are [\'once\',\'every\']';
											//if(!t.in.validURL(t.s.items.fetch.url)) throw  'An invaild items.fetch.url was supplied to the karousel.\Value supplied: ' + t.s.items.fetch.url;
										} catch(err) {
											t.in.addError('Fatal',err);
											t.in.outputError();
											return;
										}
										switch(t.s.items.fetch.frequency) {
											case 'once':
// TODO : Abstract how ajax is being called.  The ajax settings should be passed in by the user if desired.
// TODO : Abstract how the ajax response is handled.											
												$.ajax({
													url: t.s.items.fetch.url,
													type: 'GET',
													data: 'from=5&numbers=true',
													dataType: 'html'
												}).done(function(html) {
// TODO : Upon Abstracting the handling of the response should be left up to the end user.
// The expected response should return only the items.
// TODO : Add selector for items VS items > li .parent
													if( !(t.$karousel = $(t.s.items.container)).length > 0) {
														t.in.addError('Fatal','Unable to find items container to use for the karousel.\nSelector supplied: ' + t.s.items.container+'\njQuery matched these items'+$(t.s.items.container));
														t.in.outputError();
														return;
													}
													t.$karousel.html(t.$items = $(html).hide());
													
													t.in.setupKarousel();
												}).fail(function(jqXHR, textStatus) {
													t.in.addError('Fatal','The ajax request failed: '+textStatus);
													t.in.outputError();
													return;
												});
											break;
											case 'every':
												//ajax.php?from=0&to=2
												return;
											break;
										}
									break;
									default:
										t.in.addError('Fatal','An invalid items.fetch.from value was suppled to the karousel.\nType supplied: ' + t.s.items.fetch.from+'\nValid types are [\'document\',\'ajax\']');
										t.in.outputError();
										return;
									break;
								}
							},
							'setupKarousel': function() {
// TODO : allowing EM / % / px to be passed to the karousel
								if($karousel.attr('style')) {
									karouselWidth = $karousel.attr('style').match(/width:(.*)(?:px|%|em|;)/)[1].trim() || '';
								} else {
									karouselWidth = '';
								}
// TODO : check if Supplied Mask Size is larger than Karousel Avaliable width.  Ie karousel is 300px and mask is set to 400px;
// if so throw a warning!

// TODO : Look into why position is failing to center / center?  IT Does not fail.  See above line.
// There is not enough room for controls and other stuff on the carousel.

								t.s.mask.size = parseInt($karousel.width(t.s.mask.size).width());
								$karousel.width(karouselWidth);

								
								switch(t.s.items.orientation) {
									case 'horizontal':
										
										var m1 = parseInt(t.$items.css('margin-left'));
										var m2 = parseInt(t.$items.css('margin-right'));
										var mt = m1 + m2;
										
										if(!t.in.itemSize(m1,m2,mt)) return;
										
// TODO : check the defaultCss is a valid css string(convert to object) or a valid jquery css object  Than add the itemSize and float left to that
// TODO : check if the defaultCss has margins in it.  use those over the css margins on the inital load.  USEful for ajax abstraction.

										t.s.items.defaultCss.width = t.itemSize;
										t.s.items.defaultCss.float = 'left';
										
// TODO : add a check for karousel WIDTH if not a int then fail or revert to default.

										t.s.mask.defaultCss.width = t.s.mask.size;
										
										if(t.maskMargin > 0) {
											t.s.mask.defaultCss.width = t.s.mask.defaultCss.width - t.maskMargin;
											switch(t.s.mask.overflowMarginHorizontal) {
												case 'left':
												case 'right':
													t.s.mask.defaultCss['margin-'+t.s.mask.overflowMarginHorizontal] = t.maskMargin;
												break;
												case 'both':
													t.s.mask.defaultCss['margin-left'] = Math.floor(t.maskMargin/2);
													t.s.mask.defaultCss['margin-right'] = Math.ceil(t.maskMargin/2);
												break;
												default:
													t.in.addError('Fatal','An invalid mask.overflowMarginHorizontal value was suppled to the karousel.\nType supplied: ' + t.s.mask.overflowMarginHorizontal+'\nValid types are [\'left\',\'right\',\'both\']');
													t.in.outputError();
													return;
												break;
											}
										}
									break;
									case 'vertical':
										var m1 = parseInt(t.$items.css('margin-top'));
										var m2 = parseInt(t.$items.css('margin-bottom'));
										var mt = (m1 >= m2) ? m1 : m2;
										if(!t.in.itemSize(m1,m2,mt)) return;
										
										t.s.items.defaultCss.height = t.itemSize;
										
										t.s.mask.defaultCss.height = t.s.mask.size;
										if(t.maskMargin > 0) {
											t.s.mask.defaultCss.height = t.s.mask.defaultCss.height - t.maskMargin;
											switch(t.s.mask.overflowMarginVertical) {
												case 'top':
												case 'bottom':
													t.s.mask.defaultCss['margin-'+t.s.mask.overflowMarginHorizontal] = t.maskMargin;
												break;
												case 'both':
													t.s.mask.defaultCss['margin-top'] = Math.floor(t.maskMargin/2);
													t.s.mask.defaultCss['margin-bottom'] = Math.ceil(t.maskMargin/2);
												break;
												default:
													t.in.addError('Fatal','An invalid mask.overflowMarginVertical value was suppled to the karousel.\nType supplied: ' + t.s.mask.overflowMarginHorizontal+'\nValid types are [\'left\',\'right\',\'both\']');
													t.in.outputError();
													return;
												break;
											}
										}
									break;
									default:
										t.in.addError('Fatal','An invalid items.orientation value was suppled to the karousel.\nType supplied: ' + t.s.items.orientation+'\nValid types are [\'horizontal\',\'vertical\']');
										t.in.outputError();
										return;
									break;
								}

								// Store items into an array in memory
								t.kitems = t.$items.css(t.s.items.defaultCss).show().clone(); // Clone into an array in memory!

								// Remove all items from the carousel
								t.$items.remove();
								
//TODO add logic for IF AJAX and EVERY!!!!!!!
								// Add a div that wraps the carousel.  The carousel width is applied to this and the carosel container is set to position relative.  This is how the items can slide.
								t.$karousel.wrap($('<div class="mask">').css(t.s.mask.defaultCss));
								
								// Add style to the karousel
								t.$karousel.css({
									left: '0',
									position: 'relative'
								});
								
								// Restore the desired number of visible items.
// TODO : Add logic that checks that the amount of items is > the visible items.
								// Otherwise need logic to check if carousel has ends or not
								
								
								t.$karousel.html(t.kitems.slice(0,t.s.items.perPage).clone());
								t.position = [1,t.s.items.perPage]; // Store the position in the array, since matching could be a bitch.
								console.log($karousel);
								$karousel.data('karousel', t.pub );
								
								if(typeof t.s.afterInit == 'function') t.s.afterInit();
								
								t.in.outputError();
							},
							'rotate' : function(s) {					
// TODO check if is rotating then add to the queue
								if(t.$karousel.is(':animated')){
									console.log('animated');
									return;
								}
								
								// TODO check if the carosuel was stopped in an animation
								if(typeof t.rotating == 'object') {
									console.log('was stopped while in animation.');
									return;
								}
								
								var s = $.extend({},{
									'direction': 'next',
									'speed' : t.s.transition.speed,
									'items' : t.s.items.perPage
								},s);
								
								// Store the resulting position offset then determine if it wraps in the karousel
								// position + visible items + move items
								// TODO maybe remove the second part of the t.positon array since this is the same as t.visibleItems
								
								switch(s.direction) {
									case 'next':
										s.newPosition = t.position[1] + s.items;
										var position = t.position[1];
										var offset = position + s.items;
// TODO : add a logic check to make sure the next and previous are not disabled
									break;
									case 'prev':
										s.newPosition = t.position[0] - s.items;
										/*
											Inverse the position (1 based NOT 0 based)
											1|234|5
											  |
											5|432|1
											
											2=4 Math Formula : newPosition = numberOfItems - (currentPosition - 1)
										*/

										t.kitems.reverse();
										var position = t.kitems.length - (t.position[0] - 1);
										var offset = position - s.items;
// TODO : add a logic check to make sure the next and previous are not disabled
									break;
									default:
										t.in.addError('Warning','An invalid direction value was suppled to the rotatation.\nType supplied: ' + s.direction+'\nValid types are [\'next\',\'prev\']');
										t.in.outputError();
										return;
									break;
								}
								
								if(t.s.items.repeat) {
									var itemsRemaining = s.items;
									var newItems = $();

									// For memory optimization the array is only duplicated when in a transition.
									// We use math to keep only one copy of the original elements in a javascript variable (a jQuery array).
									
									/*
										A note about patterns
										3 visbile 4 orignal items
										123412341234  This pattern has has length of 12.
										|-||-||-||-|  Pattern ends when moduls is 0
									*/
									
									/* 3 Catch Options */
									
									// Catch 1 - Not at position 1 - Goal : get to position 1 for Phase 2 and 3
									if(position != t.kitems.length) {
										var numToEnd = t.kitems.length - position;
										
										if(itemsRemaining > numToEnd){
											var moveItems = numToEnd;
											s.newPosition = t.kitems.length % (position + moveItems);
										} else {
											var moveItems = itemsRemaining;
											s.newPosition = position + moveItems;
										}
										newItems = newItems.add(t.kitems.slice(position, position +  moveItems).clone());
										itemsRemaining = itemsRemaining - moveItems;
									}
									
									//Catch 2 and 3
									while ( itemsRemaining > 0 ) {
										if(itemsRemaining > t.kitems.length) {
											// Catch 2 - All items from begining (1)
											newItems = newItems.add(t.kitems.clone());
											itemsRemaining = itemsRemaining - t.kitems.length;
										} else {
											// Catch 3 - Remaning items from begining (1)
											newItems = newItems.add(t.kitems.slice(0,itemsRemaining).clone());
											s.newPosition = itemsRemaining;
											itemsRemaining = 0;
										}
									}
									if(s.direction == 'prev') {
										t.kitems.reverse();
										s.newPosition = t.kitems.length - (s.newPosition - 1)
									}
									t.in.slide(s, newItems);
								} else {
									if(!((s.direction == 'next' && s.newPosition > t.kitems.length) || (s.direction == 'prev' && s.newPosition < 1)) ) {
										var newItems = t.kitems.slice(position,position + s.items).clone();
									} else {
										if(t.s.transition.createEmptyItems && (s.direction == 'next' && s.newPosition > t.kitems.length)) {
											var newItems = t.kitems.slice(position).clone();
											for(i = 0; i < (t.s.items.perPage - (t.kitems.length%t.s.items.perPage)); i++) { 
												var newItems = newItems.add( $(t.s.items.selector+':eq(0)').clone().html(''));
											}
// TODO : set the next to disabled HERE											
											
										} else {
											if(s.dirction == 'next') {
// TODO : set the next to disabled HERE												
											}
											if(s.direction == 'prev') {
// TODO : set the prev to disabled HERE
												t.kitems.reverse();
											}
											
	// TODO : SHOULD BE ABLE TO REMOVE ERROR MESSAGE BECAUSE CHECK HAPPENS EARLIER?
	// TODO : Use logic of items object to determine if EMPTY spots should be created.  Ie page at a time with 3 items per page and 5 items total.
	// TODO : if created empty and page at a time pagination is possible.

											//Get how many items it overby.
											console.log('too far!');
											return;
										}
									}

									if(s.direction == 'prev') t.kitems.reverse();
									t.in.slide(s, newItems);
								} 
							},
							'slide' : function(s,newItems) {
								if(s.direction == 'next') {
									t.rotating = {
										
										animate : {
											'left': '-' + (t.itemOuterSize * s.items)
										},
										settings : s,
										complete: function () {
											// Complete Rotation
											
											$('.items > *').not($('.items > *').slice(-(t.s.items.perPage))).remove();
											t.$karousel.css({
												width: t.s.mask.width,
												left: '0',
												position: 'relative'
											});
											
											
											//(s.newPosition > t.kitems.length) ? s.newPosition % t.kitems.length : s.newPosition;
											var first = (s.newPosition < t.s.items.perPage) ? s.newPosition - t.s.items.perPage + t.kitems.length + 1  : s.newPosition - t.s.items.perPage + 1;
											t.position = [ first , s.newPosition]; // Store the position in the array, since matching could be a bitch
											console.log(t.position);
											//remove this
											delete t.rotating;
										}
									};
									t.$karousel.css({width: '+='+s.items * t.itemOuterSize});
									t.$karousel.append(newItems);
								}
								if(s.direction == 'prev') {
									t.rotating = {
										
										animate : {
											'left': '+=' + (t.itemOuterSize * s.items)
										},
										settings : s,
										complete: function () {
											// Complete Rotation
											$('.items > *').not($('.items > *').slice(0,t.s.items.perPage)).remove();
											t.$karousel.css({
												width: t.s.mask.width,
												left: '0',
												position: 'relative'
											});
											
											//(s.newPosition < -t.s.items.perPage) ? t.kitems.length % s.newPosition : (s.newPosition <= 0) ? t.kitems.length + s.newPosition : s.newPosition;
											var last = ((s.newPosition + t.s.items.perPage - 1) > t.kitems.length) ? -(t.kitems.length - (s.newPosition + t.s.items.perPage -1))  : s.newPosition + t.s.items.perPage - 1;
											t.position = [ s.newPosition , last]; // Store the position in the array, since matching could be a bitch
											console.log(t.position);
											//remove this
											delete t.rotating;
										}
									};
									t.$karousel.css({
										width: '+='+s.items * t.itemOuterSize,
										left: -(s.items * t.itemOuterSize)
									});
									t.$karousel.prepend(newItems.reverse().clone());
								}
								
								// Animate the carousel
								t.rotating.start = new Date();
								t.$karousel.animate(t.rotating.animate, t.rotating.settings.speed, 'linear', t.rotating.complete);
							},
							'removeNonVisible' : function($elements) {
								$elements = $elements || false;
								if(!$elements) {
									$('.items > *').each(function() {
										$this = $(this);
										if ($this.offset().left < 0 || $this.offset().left > t.s.karouselWidth) {
											$this.remove();
										}
									});
								}
							},
							'pause' : function() {
								if(typeof t.rotating == 'object' && t.$karousel.is(':animated')) {
									t.rotating.stop = new Date();
									t.$karousel.stop();
								} else if (typeof t.rotating == 'object'){
									t.in.play();
								}
							},
							'play' : function() {
								
								if(typeof t.rotating == 'object') {
									// Set the t.rotating.speed to the new quantity, this is so if you pause then play then pause then play it is still the orininal speed (IE appears to resume at the same rate.)
									t.$karousel.animate(t.rotating.animate,  (t.rotating.settings.speed = t.rotating.settings.speed - (t.rotating.stop - t.rotating.start)), 'linear', t.rotating.complete);
									//Reset the start time.  Start time is used to calculate the amount of time between the animation and the press of the pause button.  Not store when the original rotate begain.
									t.rotating.start = new Date();
									//return t.rotating;
								} else {
									// TODO : ERROR Sorry nothing to do the carousel was not paused during an animation
									return;
								}
							},
							'itemCount': function() {
								//meow
							}
							
						}
						
						t.in.init();
					}
					$.fn.karousel = function(settings) {
						return this.each(function() {
							new $.karousel(this,settings);
						});
					}
				})(jQuery)
				
				var x = {};
				var z = {};
				$(document).ready(function() {
					x = $('.karousel').karousel({
						items:{
							perPage: 3,
							repeat:true
						},
						transition:{
							createEmptyItems:true
						},
						mask:{
							size:'600',
							overflowMarginHorizontal:'both'
						},
						afterInit: function() {
// TODO: pass the karousel in the CallBack Functions
// TODO: create the following callbacks : beforeTransition, afterTranistion, beforePause, afterPause, beforeResume, afterResume.

							$('.karousel .mask').position({my:'center',at:'center',of:$('.karousel')});
							$('.karousel .next').position({my:'right center',at:'right center',of:$('.karousel .next').parents('.karousel')});
							$('.karousel .prev').position({my:'left center',at:'left center',of:$('.karousel .prev').parents('.karousel')});
						}
					}).eq('0').data('karousel');
					//x = $('.karousel').karousel({items.perPage: 3,karouselWidth: 600, type:'hasEnds'}).eq('0').data('karousel');
					$('.karousel .control.move').each(function() {
						$this = $(this);
						
						if($this.is('.next')) {
							//$this.position({my:'right center',at:'right center',of:$this.parents('.karousel')});
							//rotate({'direction':'next'});
							$this.find('*').click(function(e){e.preventDefault();});
							$this.click(function(e) {
								$karousel = $this.parents('.karousel').data('karousel');
								$karousel.moveNextPage();
							});
						}
						
						if($this.is('.prev')) {
							//$this.position({my:'left center',at:'left center',of:$this.parents('.karousel')});
							//rotate({'direction':'prev'});
							$this.find('*').click(function(e){e.preventDefault();});
							$this.click(function(e) {
								$karousel = $this.parents('.karousel').data('karousel');
								$karousel.movePrevPage();
							});
						}
						
						function rotate(o) {
							$karousel = $this.parents('.karousel').data('karousel');
							$this.find('*').click(function(e){e.preventDefault();});
							$this.click(function(e) {
								e.preventDefault();
								$karousel.rotate(o);
							});
						}
					});
				});
			//]]>
		</script>
				
		<link rel="stylesheet" href="http://yui.yahooapis.com/3.5.1/build/cssreset/cssreset-min.css" type="text/css" media="all" />
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css" type="text/css" media="all" />
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/dark-hive/jquery-ui.css" type="text/css" media="all" />
<!--
		<link rel="stylesheet" href="/css/1020_12_col.css" />
		<link rel="stylesheet" href="/css/style.css" />
-->
		<style type="text/css">
			.karousel { overflow: hidden; clear: both; width: 95%; }
			.karousel img { width: 180px; }
			
			.karousel .items > li { /*float: left;*/ margin-left: 10px; }
			
			.karousel .mask, .karousel .control.move { float: left; }
/* TODO: Add position logic to karousel.  It will float according to which items are on the same ROW! */			
			
			.karousel .control.move { display: block; width: 50px; background: #ccc; cursor: pointer; }

			/* Colorize */
			
			.karousel .items > li { background: #fff; }
			.karousel .mask { overflow: hidden; background-color: blue; float: left; }
		</style>
		
	</head>
	<body>
		<header class="container_12 clearfix" id="header">
		</header>
		<div role="main" class="container_12">
			<p>Watch the Magic!</p>
			<div class="karousel">
				<span class="control move prev"><a href="#">Prev</a></span>
				<ul class="items">
				<?php /*
					<li><a href="img/t28.jpg"><img src="img/t28.jpg" alt="T28 Turbo" title="T28 Turbo" /></a></li>
					<li><a href="img/big_air.jpg"><img src="img/big_air.jpg" alt="Snowboarding into foam pit" title="Snowboarding into foam pit" /></a></li>
					<li><a href="img/lib.png"><img src="img/lib.png" alt="LIB Tech Skateboard" title="LIB Tech Skateboard" /></a></li>
					<li><a href="img/Pyro-GX.jpg"><img src="img/Pyro-GX.jpg" alt="Pyro GX" title="Pyro GX" /></a></li>
					<li><a href="img/forum_grudge_f.jpg"><img src="img/forum_grudge_f.jpg" alt="Forum Snowboard" title="Forum Snowboard" /></a></li>
				*/ ?>
				
					<li><a href="img/1.png"><img src="img/1.png" alt="1" title="1" /></a></li>
					<li><a href="img/2.png"><img src="img/2.png" alt="2" title="2" /></a></li>
					<li><a href="img/3.png"><img src="img/3.png" alt="3" title="3" /></a></li>
					<li><a href="img/4.png"><img src="img/4.png" alt="4" title="4" /></a></li>
					<li><a href="img/5.png"><img src="img/5.png" alt="5" title="5" /></a></li>
				
				 <?php /* */?>
				</ul>
				<span class="control move next"><a href="#">Next</a></span>
			</div>
			
		</div>
		<footer>
		</footer>
	</body>
</html>