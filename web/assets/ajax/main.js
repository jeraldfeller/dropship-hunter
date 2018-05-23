function checkProcess() {
  $dfd = $.Deferred();
  if (XMLHttpRequestObject) {

    XMLHttpRequestObject.open("POST", "/main/get");


    XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    XMLHttpRequestObject.onreadystatechange = function () {
      if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
        var response = $.parseJSON(XMLHttpRequestObject.responseText);
        if (response.isActive == true) {
          if (response.status = true) {
            if(response.totalCount > 0){
              $('.progress-container').css('display', 'inline');
              $progWidth = (response.completeCount) * 100 / response.totalCount;
              $('.progress-bar').attr('aria-valuenow', (response.completeCount));
              $('.progress-bar').css('width', $progWidth + '%');
              $('.current_index').text(response.completeCount);
              $('.total_count').text(response.totalCount);
            }
            if (response.totalCount == response.completeCount) {
              $('.success-message').css('display', 'block');
            }
          }
        } else {
          $('.progress-container').css('display', 'none');
          $('.progress-bar').attr('aria-valuenow', 0);
          $('.progress-bar').css('width', 0);
          $('.current_index').text(0);
          $('.total_count').text(response.totalCount);
        }
        console.log(response);
        $dfd.resolve(response);
      }
      if (XMLHttpRequestObject.status == 408 || XMLHttpRequestObject.status == 503 || XMLHttpRequestObject.status == 500) {
        // alert('Something went wrong, please try again');
        $dfd.resolve(false);
      }
    }
    XMLHttpRequestObject.send("param= 1");


  }

  return $dfd.promise();
}

function importProductList($data) {

  if (XMLHttpRequestObject) {

    XMLHttpRequestObject.open("POST", "/main/import");


    XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    XMLHttpRequestObject.onreadystatechange = function () {
      if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
        var response = $.parseJSON(XMLHttpRequestObject.responseText);
        $('#submitBtn').html('Import');
        alert('Import success.');
        checkProcess();
      }
      if (XMLHttpRequestObject.status == 500) {
        alert('Something went wrong, please try again');
      }
    }
    XMLHttpRequestObject.send("param= " + JSON.stringify($data));


  }

  return false
}

function reRun() {

  if (XMLHttpRequestObject) {

    XMLHttpRequestObject.open("POST", "/main/rerun");


    XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    XMLHttpRequestObject.onreadystatechange = function () {
      if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
        var response = $.parseJSON(XMLHttpRequestObject.responseText);
        $('#submitBtn').html('<i class="fa fa-refresh"></i> Re-run');
        alert('Re-run success.');
      }
      if (XMLHttpRequestObject.status == 500) {
        alert('Something went wrong, please try again');
      }
    }
    XMLHttpRequestObject.send("param= 1");


  }

  return false
}


function updateProxy($proxy) {

  if (XMLHttpRequestObject) {

    XMLHttpRequestObject.open("POST", "/proxy/update");


    XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    XMLHttpRequestObject.onreadystatechange = function () {
      if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
        var response = $.parseJSON(XMLHttpRequestObject.responseText);
        $('#updateProxyBtn').html('Update Proxy List');
        alert('Proxy List Updated');
        exec();
      }
      if (XMLHttpRequestObject.status == 408 || XMLHttpRequestObject.status == 503 || XMLHttpRequestObject.status == 500) {
        alert('Something went wrong, please try again');
      }
    }
    XMLHttpRequestObject.send("param= " + JSON.stringify({proxy: $proxy}));


  }

  return false
}

function exec() {
  setInterval(function () {
    checkProcess();
  }, 60000);
}

