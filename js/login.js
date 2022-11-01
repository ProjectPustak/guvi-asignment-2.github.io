//handles every form request
$(document).on('submit', 'form', async function (e) {
  e.preventDefault();
  let url = 'php/functions.php?action=login';
  $.ajax({
    type: 'post',
    url: url,
    data: $(this).serialize(),
    success: function (resp) {
      resp = JSON.parse(resp);
      if (resp.status == true) {
        localStorage.setItem("session_token", resp.token);
        Swal.fire({
            icon: 'success',
            title: resp.title,
            html: resp.html,
            confirmButtonColor: '#00a65a',
          })
          .then(() => {
            window.location.reload();
          });
      } else {
        Swal.fire({
          icon: 'error',
          title: resp.title,
          html: resp.html,
          confirmButtonColor: '#D52D31',
        });
      }
    }
  });
});