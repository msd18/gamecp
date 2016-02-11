jQuery.fn.fadeToggle = function(s, fn){
  return (this.is(":visible"))
  ? this.fadeOut(s, fn)
  : this.fadeIn(s, fn);
}

function toggle_extra(div_id) {
  $('.extra_'+div_id).fadeToggle();
}

function toggle_fade(div_id) {
  $('.row'+div_id).fadeToggle();
}


function Comma(nStr) {
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 2 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function showHide(shID) {
    if (document.getElementById(shID)) {
        if (document.getElementById(shID+'-show').style.display != 'none') {
            document.getElementById(shID+'-show').style.display = 'none';
            document.getElementById(shID).style.display = 'block';
        }
        else {
            document.getElementById(shID+'-show').style.display = 'inline';
            document.getElementById(shID).style.display = 'none';
        }
    }
}

function voteScript(siteID,siteName) {
	var voteInfo = document.getElementById('show_vinfo');

	voteInfo.innerHTML  = '<form action="./vote.php" method="post" target="_blank">'
		 + '['+siteName+'] Your Account Name: <input type="text" name="vote_account" /> '
		 + ' <input type="hidden" name="vote_id" value="'+siteID+'">'
		 + ' <input type="submit" name="Vote!" value="Vote!">'
		 + '</form>';
}

function calculate_amount(id,price,preamount,current_gp) {
	var setprice = document.getElementById('price_'+id);
	var amount = document.getElementById('amount_'+id);
	var gp_after = document.getElementById('gpafter_'+id);

	var single_price,total_price,gpafter_price;
	
	single_price = Math.ceil(price/preamount);
	final_price = Math.ceil(single_price*amount.value);

	setprice.innerHTML = Comma(final_price)+' GP';
	gp_after.innerHTML = Comma(current_gp-final_price)+' GP';

}

function addFormField() {
	var id = document.getElementById("id").value;


	$("#myTable tr:eq(1)").before("	<tr id='row" + id + "'><td class='alt2' nowrap>" + id + "</td><td class='alt1' nowrap><div id='results_div" + id + "'>-1</div></td><td class='alt1' nowrap><input type='text' size='5' name='item_code[]' onchange='check_itemname(\"item_code"+ id +"\", \"results_div"+ id +"\");' id='item_code"+ id +"'/></td><td class='alt1' nowrap><input type='text' size='1' name='item_amount[]' value='0'/></td><td class='alt1' nowrap><select name='item_ups[]'><option value='0'>+0</option><option value='1'>+1</option><option value='2'>+2</option><option value='3'>+3</option><option value='4'>+4</option><option value='5'>+5</option><option value='6'>+6</option><option value='7'>+7</option></select>/<select name='item_slots[]'><option value='0'>0</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option></select> <select name='item_talic[]'><option value='1'>No Talic</option><option value='2'>Rebirth</option><option value='3'>Mercy</option><option value='4'>Grace</option><option value='5'>Glory</option><option value='6'>Guard</option><option value='7'>Belief</option><option value='8'>Sacred Flame</option><option value='9'>Wisdom</option><option value='10'>Favor</option><option value='11'>Hatred</option><option value='12'>Chaos</option><option value='13'>Darkness</option><option value='14'>Destruction</option><option value='15'>Ignorant</option></select></td><td class='alt1' nowrap><input type='text' size='5' name='item_rental_time[]' value='0'/></td><td class='alt1' nowrap><a href='#' onClick='removeFormField(\"#row" + id + "\"); return false;'>Remove</a></td></tr>");	

	id = (id - 1) + 2;

	document.getElementById("id").value = id;
}

function removeFormField(id) {
	$(id).remove();
}

function convert(id,rate,max,current,points,currency) {
	var current;
	var result_input = document.getElementById('result_'+id);
	var exchange_input = document.getElementById('exchange_'+id);

	if(isNaN(exchange_input.value)) {
		exchange_input.value = 0;
	}

	if(exchange_input.value < 0) {
		exchange_input.value = 0;
	}

	if(exchange_input.value > points) {
		exchange_input.value = points;
	}

	money = Math.floor(rate*exchange_input.value);

	if(money > max) {
		money = Math.floor(max);
		max_value = Math.floor(money/rate);
		exchange_input.value = Math.floor(max_value);
		money = Math.floor(rate*exchange_input.value);
	}
	
	total = Comma(Math.floor(current + money));
	money = Comma(Math.floor(money));

	result_input.innerHTML = 'Exchange: ' + money + ' ' + currency + '<br/>Total: ' + total;
}
