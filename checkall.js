function checkAllFields(ref)
{
var chkAll = document.getElementById('checkAll');
var checks = document.getElementsByName('ban_serial[]');
var removeButton = document.getElementById('removeChecked');
var boxLength = checks.length;
var allChecked = false;
var totalChecked = 0;
	if ( ref == 1 )
	{
		if ( chkAll.checked == true )
		{
			for ( i=0; i < boxLength; i++ ) {
				checks[i].checked = true;
				document.getElementById('tr_' + i).className = 'alt2';
			}
		}
		else
		{
			for ( i=0; i < boxLength; i++ ) {
				checks[i].checked = false;
				document.getElementById('tr_' + i).className = 'alt1';
			}
		}
	}
	else
	{
		for ( i=0; i < boxLength; i++ )
		{
			if ( checks[i].checked == true )
			{
			allChecked = true;
			continue;
			}
			else
			{
			allChecked = false;
			break;
			}
		}
		if ( allChecked == true )
		chkAll.checked = true;
		else
		chkAll.checked = false;
	}
	for ( j=0; j < boxLength; j++ )
	{
		if ( checks[j].checked == true )
		totalChecked++;
	
	}


	removeButton.value = "Submit ["+totalChecked+"] Selected";
}


/* Highlight Row */
function highlight(checkboxId, id)
{
   var row_element = document.getElementById(id);

   row_element.className = checkboxId.checked ? 'alt2' : 'alt1';
}

