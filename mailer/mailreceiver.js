function ajaxFormRequest(formid,page) {
		var elem = document.getElementById(formid).elements;
		var data = { 'page':''};
        for(var i = 0; i < elem.length; i++)
        {
			if (elem[i].type != 'radio')
				data[ elem[i].name ] = elem[i].value;
			else if (elem[i].checked)
				data[ elem[i].name ] = elem[i].value;
        } 
        data['page'] = page;

  		var url = "http://link-to-receiver/mailreceiver.php"
  		$.ajax({
  		  type: "POST",
  		  url: url,
  		  data: data,
  		  success: function(data) {
  		    if (console) console.log(JSON.stringify(data))
  		    if (data && data.msg && data.msg == 'ok') {
  		       if (data.href)
  		       	 window.location.href = data.href;
  		       else
  		       if (data.text)
 	  		     alert(data.text);
  		       else
 	  		     alert("Daten wurden gesendet.");
 	  		     
	  		 } else
	  		   alert("Ein interner Fehler ist aufgetreten");
  		  },
  		  error: function(data) {
  		    if (console) console.log(JSON.stringify(data))
			if (data.responseText && data.responseText.href)
  		       	 window.location.href = data.href;
			else
			if (data.responseText && data.responseText.text)
				alert(data.responseText.text);
			else
  		  	    alert("Fehler beim senden");
  		  },
  		  dataType: 'json'
  		});
}
