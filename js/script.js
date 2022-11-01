const $profile_page = 'profile.html';
const $login_page = 'login.html';
const $register_page = 'register.html';

var $page = window.location.pathname.split("/").pop();
var session_token = localStorage.getItem("session_token");

if (($page == $login_page) || ($page == $register_page)) {
  if (session_token) {
    window.location.href = $profile_page;
  }
} else {
  if (!session_token) {
    window.location.href = $login_page;
  } else {
    let url = `php/functions.php?action=check-authorization`;
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
        }
      }
    });
  }
}


$(document).on("click", "#btnLogout", function () {
  let url = `php/functions.php?action=logout`;
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
        if (resp.status == true) {
          localStorage.removeItem("session_token");
          window.location.reload();
        }
      }
    }
  });
});