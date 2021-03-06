function checkProcess($app) {
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
              $('.success-message').css('display', 'hide');
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


    XMLHttpRequestObject.send("param= " + JSON.stringify({app: $app}));


  }

  return $dfd.promise();
}


function checkAppProcess($app) {
  $dfd = $.Deferred();
  if (XMLHttpRequestObject) {

    XMLHttpRequestObject.open("POST", "/main/get-app-process");


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
              $('.success-message').css('display', 'hide');
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
    XMLHttpRequestObject.send("param= " + JSON.stringify({app: $app}));


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
        $('#submitBtn').html('<i class="fa fa-upload"></i> IMPORT');
        alert('Import success.');
        $disableCheckProcess = false;
      //  checkProcess('app_2');
      }
      if (XMLHttpRequestObject.status == 500) {
        alert('Something went wrong, please try again');
        $disableCheckProcess = false;
      }
    }
    XMLHttpRequestObject.send("param= " + JSON.stringify($data));


  }

  return false
}


function importGrabberSellerList($data) {

  if (XMLHttpRequestObject) {

    XMLHttpRequestObject.open("POST", "/main/grabber/import");


    XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    XMLHttpRequestObject.onreadystatechange = function () {
      if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
        var response = $.parseJSON(XMLHttpRequestObject.responseText);
        $('#submitBtn').html('<i class="fa fa-upload"></i> IMPORT');
        alert('Import success.');
        checkProcess('app_3');
      }
      if (XMLHttpRequestObject.status == 500) {
        alert('Something went wrong, please try again');
      }
    }
    XMLHttpRequestObject.send("param= " + JSON.stringify($data));


  }

  return false
}

function  reRun($app) {

  if (XMLHttpRequestObject) {

    XMLHttpRequestObject.open("POST", "/main/rerun");


    XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    XMLHttpRequestObject.onreadystatechange = function () {
      if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
        var response = $.parseJSON(XMLHttpRequestObject.responseText);
        $('#rerunBtn').html('<i class="fa fa-refresh"></i> Re-run');
        checkProcess($app);
        alert('Re-run success.');
      }
      if (XMLHttpRequestObject.status == 500) {
        alert('Something went wrong, please try again');
      }
    }
    XMLHttpRequestObject.send("param= " + JSON.stringify({app: $app}));


  }

  return false
}


function removeTitles() {

  if (XMLHttpRequestObject) {

    XMLHttpRequestObject.open("POST", "/main/remove-titles");


    XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    XMLHttpRequestObject.onreadystatechange = function () {
      if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
        var response = $.parseJSON(XMLHttpRequestObject.responseText);
        $('#removeBtn').html('<i class="fa fa-refresh"></i> Remove Titles');
        checkProcess('app_2');
        alert('Titles remove successfully.');
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


function setActivity($data) {

  if (XMLHttpRequestObject) {

    XMLHttpRequestObject.open("POST", "/app/activity");


    XMLHttpRequestObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    XMLHttpRequestObject.onreadystatechange = function () {
      if (XMLHttpRequestObject.readyState == 4 && XMLHttpRequestObject.status == 200) {
        var response = $.parseJSON(XMLHttpRequestObject.responseText);

      }
      if (XMLHttpRequestObject.status == 408 || XMLHttpRequestObject.status == 503 || XMLHttpRequestObject.status == 500) {
        alert('Something went wrong, please try again');
      }
    }
    XMLHttpRequestObject.send("param= " + JSON.stringify($data));


  }

  return false
}


function exec($app) {
  setInterval(function () {
    if($disableCheckProcess == false){
      checkProcess($app);
    }
  }, 60000);
}

