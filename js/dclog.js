/*global $, dotclear */
'use strict';

Object.assign(dotclear.msg, dotclear.getData('dclog_list'));

$(function(){
	$('.checkboxes-helpers').each(function(){
		dotclear.checkboxesHelpers(this)
	});
	$('input[name="del_logs"]').click(function(){
		return window.confirm(dotclear.msg.confirm_delete_selected_log)
	});
	$('input[name="del_all_logs"]').click(function(){
		return window.confirm(dotclear.msg.confirm_delete_all_log)
	})
})