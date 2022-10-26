const $profile_page = 'profile.html';
const $login_page = 'login.html';
const $register_page = 'register.html';

var $page = window.location.pathname.split("/").pop();
var session_token = localStorage.getItem("session_token");

if(($page == $login_page) || ($page == $register_page)){
  if(session_token){
    window.location.href = $profile_page;
  }
} else {
  if(!session_token){
    window.location.href = $login_page;
  } else {
    let url = `php/functions.php?action=check-authorization`;
    let options = { 
      method:'post', 
      headers: {
        'Content-Type': 'application/json',
        'Authorization': session_token
      },
    };
    fetch(url,options)
    .then((resp)=>{ 
      return resp.json(); 
    })
    .then((resp)=>{ 
      if(resp.status == false){
        localStorage.removeItem("session_token");
        window.location.reload();
      }
    })
    .catch((err)=>{ 
      console.log(err); 
    });
  }
}


$(document).on("click","#btnLogout",function(){
  let url = `php/functions.php?action=logout`;
    let options = { 
      method:'post', 
      headers: {
        'Content-Type': 'application/json',
        'Authorization': session_token
      },
    };
    fetch(url,options)
    .then((resp)=>{ 
      return resp.json(); 
    })
    .then((resp)=>{ 
      if(resp.status == true){
        localStorage.removeItem("session_token");
        window.location.reload();
      }
    })
    .catch((err)=>{ 
      console.log(err); 
    });
});