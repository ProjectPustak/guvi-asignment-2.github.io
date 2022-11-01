$(document).ready(function () {
  let url = `php/functions.php?action=get-user`;
  $.ajax({
    type: 'post',
    url: url,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': session_token
    },
    success: function (resp) {
      resp = JSON.parse(resp);
      if (resp.status == 401) {
        localStorage.removeItem("session_token");
        window.location.reload();
      } else {
        let record = resp.data;
        if (record.length != 0) {
          $('#name').val(record.name);
          $('#mobile').val(record.mobile);
          $('#age').val(record.age);
        }
      }
    }
  });
});


//handles every form request
$(document).on('submit', 'form', async function (e) {
  e.preventDefault();
  let url = 'php/functions.php?action=update';
  let name = $('#name').val();
  let mobile = $('#mobile').val();
  let age = $('#age').val();
  let data = {
    name: name,
    mobile: mobile,
    age: age
  }
  $.ajax({
    type: 'post',
    url: url,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': session_token
    },
    data: JSON.stringify(data),
    success: function (resp) {
      resp = JSON.parse(resp);
      if (resp.status == 401) {
        localStorage.removeItem("session_token");
        window.location.reload();
      } else {
        if (resp.status == true) {
          window.location.reload();
        } else {
          Swal.fire({
            icon: 'error',
            title: resp.title,
            html: resp.html,
            confirmButtonColor: '#D52D31',
          });
        }
      }
    }
  });
});