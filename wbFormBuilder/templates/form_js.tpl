<!-- wblib/wbFormBuilder/form_js.tpl -->
{{ :comment This may be moved to the head if LibraryAdmin is in use }}
<!-- position: head -->
<script type="text/javascript">
	// make sure that jQuery is available
	if ( typeof jQuery === undefined ) {
	    var fileref = document.createElement("script");
	    fileref.setAttribute( "type", "text/javascript" );
	    fileref.setAttribute( "src", "https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" );
	    if (typeof fileref != "undefined" ) {
	        document.getElementsByTagName("head")[0].appendChild(fileref);
	    }
	}
</script>
{{ :if use_filetype_check }}
  <script type="text/javascript" src="{{ WBLIB_BASE_URL }}/wblib/js/filetypes.js"></script>
{{ :ifend }}
<script type="text/javascript" src="{{ WBLIB_BASE_URL }}/wblib/js/tooltip/rounded-corners.js"></script>
<script type="text/javascript" src="{{ WBLIB_BASE_URL }}/wblib/js/tooltip/form-field-tooltip.js"></script>
<script type="text/javascript" src="{{ WBLIB_BASE_URL }}/wblib/js/jquery.jqEasyCharCounter.js"></script>
<script type="text/javascript" src="{{ WBLIB_BASE_URL }}/wblib/js/jquery.passwordstrength.js"></script>
{{ :if use_calendar }}
  <link media="screen" rel="stylesheet" type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/base/jquery-ui.css" />
  <script type="text/javascript">
	// make sure that jQuery UI is available
	if ( typeof jQuery.ui == 'undefined' ) {
	    var fileref = document.createElement("script");
	    fileref.setAttribute( "type", "text/javascript" );
	    fileref.setAttribute( "src", "http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js" );
	    if (typeof fileref != "undefined" ) {
	        document.getElementsByTagName("head")[0].appendChild(fileref);
	    }
	}
	var calendar_image = '{{ WBLIB_BASE_URL }}/wblib/wbFormBuilder/templates/calendar.gif';
  </script>
  <script type="text/javascript" src="{{ WBLIB_BASE_URL }}/wblib/js/jquery.datepicker.js"></script>
{{ :ifend }}
{{ :if use_editor }}
  <script type="text/javascript" src="{{ WBLIB_BASE_URL }}/wblib/js/cleditor/jquery.cleditor.min.js"></script>
  <script type="text/javascript">
    $.cleditor.defaultOptions.width = 486;
    $.cleditor.defaultOptions.height = 300;
  </script>
{{ :ifend }}