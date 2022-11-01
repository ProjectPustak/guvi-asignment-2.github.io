$(document).on('submit','form', async function(e){
  e.preventDefault();
  let url = 'php/functions.php?action=register';
  $.ajax({
    type:'post',
    url:url,
    data:$(this).serialize(),
    success:function(resp){
      resp = JSON.parse(resp);
      if(resp.status==true){
        Swal.fire({
          icon: 'success',
          title: resp.title,
          html: resp.html,
          confirmButtonColor: '#00a65a',
        })
        .then(() => {
          window.location.href = 'login.html';
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