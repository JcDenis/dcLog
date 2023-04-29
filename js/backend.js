/*global $, dotclear */
'use strict';

Object.assign(dotclear.msg, dotclear.getData('dcLog_msg'));

$(function(){
	$('.checkboxes-helpers').each(function(){
		dotclear.checkboxesHelpers(this)
	});
	$('input[name="selected_logs"]').click(function(){
		return window.confirm(dotclear.msg.confirm_delete_selected_log)
	});
	$('input[name="all_logs"]').click(function(){
		return window.confirm(dotclear.msg.confirm_delete_all_log)
	})
  	dotclear.condSubmit('#dcLog_form  td input[type=checkbox]', '#dcLog_form #selected_logs');
})