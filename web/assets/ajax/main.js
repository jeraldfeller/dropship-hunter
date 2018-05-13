function importProductList($data){

  if(XMLHttpRequestObject)
  {

    XMLHttpRequestObject.open("POST", "/main/import");


    XMLHttpRequestObject.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

    XMLHttpRequestObject.onreadystatechange = function()
    {
      if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
      {
        var response =  $.parseJSON(XMLHttpRequestObject.responseText);
        $('#submitBtn').html('Import');
        alert('Import success.');
      }
      if (XMLHttpRequestObject.status == 500){
        alert('Something went wrong, please try again');
      }
    }
    XMLHttpRequestObject.send("param= "+ JSON.stringify($data));


  }

  return false
}


function updateProxy($proxy){

  if(XMLHttpRequestObject)
  {

    XMLHttpRequestObject.open("POST", "/proxy/update");


    XMLHttpRequestObject.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

    XMLHttpRequestObject.onreadystatechange = function()
    {
      if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200)
      {
        var response =  $.parseJSON(XMLHttpRequestObject.responseText);
        $('#updateProxyBtn').html('Update Proxy List');
        alert('Proxy List Updated');
      }
      if (XMLHttpRequestObject.status == 408 || XMLHttpRequestObject.status == 503 || XMLHttpRequestObject.status == 500){
        alert('Something went wrong, please try again');
      }
    }
    XMLHttpRequestObject.send("param= "+ JSON.stringify({proxy: $proxy}));


  }

  return false
}
