<html>
  <head>
    <meta charset="utf-8">
    <title>Space Invaders</title>
    <link rel="stylesheet" type="text/css" href="mystyle2.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script type="text/javascript" src="spaceinvaders.js"></script>
    <script type="text/javascript">
      try
      {
        var backgroundHttpRequest = null;
        var captchaDone = false;

        function setCaptcha() {
          captchaDone = true;
        }
        function checkCookies()
        {
          <?php
          if (!empty($_COOKIE["Autologin"]))
          {
            ?>
            ShowMain();
            <?php
          }
          ?>
        }
        function backgroundRequestProgress( evt)
        {

        }

         function backgroundRequestComplete( evt)
         {
              try
              {
                let jsonObj = JSON.parse( backgroundHttpRequest.responseText);
                let errorCode = parseInt( jsonObj.ErrorCode);


                if( errorCode == 0)
                {
                  ShowMain();
                }
                else
                {
                    alert( jsonObj.Message);
                }

              }
              catch (ex)
              {
                 alert( "Error in backgroundRequestComplete: " + ex);
              }
         }

         function registrationRequestComplete( evt)
         {
              try
              {
                let jsonObj = JSON.parse( backgroundHttpRequest.responseText);
                let errorCode = parseInt( jsonObj.ErrorCode);


                if( errorCode == 0)
                {
                    ShowLogin();
                }
                else
                {
                    alert( jsonObj.Message);
                }

              }
              catch (ex)
              {
                 alert( "Error in backgroundRequestComplete: " + ex);
              }
         }

         function backgroundRequestFailed( evt)
         {
            alert( "Error: cannot connect to server");
         }

         function backgroundRequestCanceled( evt)
         {
            alert( "Error: Request cancelled");
         }


        //*****************************************************************
        //  Screen management utilitu functions

        function SetVisibleOfDivElements(visibleDiv)
        {
           try
           {
              function  MakeVisible( elmt, visible)
              {
                  elmt.style.display = visible ? "block": "none";
              }

              let divLogin    = document.getElementById( "Login");
              let divRegister = document.getElementById( "Register");
              let divMain     = document.getElementById( "Main");

              MakeVisible( divLogin, false);
              MakeVisible( divRegister, false);
              MakeVisible( divMain, false);

              if( visibleDiv == "Login")
              {
                  MakeVisible( divLogin, true);
              }
              else if( visibleDiv == "Register")
              {
                  MakeVisible( divRegister, true);
              }
              else if( visibleDiv == "Main")
              {
                  MakeVisible( divMain, true);
              }
              else
              {
                  throw "Invalid visibleDiv=" + visibleDiv;
              }
           }
           catch( ex)
           {
             alert( "Error: SetVisibleOfDivElements error=" + ex);
           }
        }

        //*****************************************************************
        //  Event handlers
        function ShowRegistration()
        {
           SetVisibleOfDivElements( "Register");
        }

        function ShowLogin()
        {
           SetVisibleOfDivElements( "Login");
        }

        function ShowMain()
        {
           SetVisibleOfDivElements( "Main");
           gameStart();
        }

        function SubmitLogin()
        {
          try
          {
            if (!captchaDone) {
              alert("Invalid captcha");
              return;
            }
            let editFieldEmail      = document.getElementById( "loginEmail");
            let editFieldPassword   = document.getElementById( "loginPassword");
            let editFieldRememberMe = document.getElementById( "rememberMe");
            let emailAddress = editFieldEmail.value;
            let password = editFieldPassword.value;
            let remeberMe = editFieldRememberMe.checked;
            let jsonObj = { 
              'Request': 'Login', 
              'Email': emailAddress, 
              'Password': password,
              'RememberMe': rememberMe ? 1 : 0
            }

             let message = JSON.stringify( jsonObj);

             backgroundHttpRequest =  new XMLHttpRequest();
             backgroundHttpRequest.addEventListener("progress", backgroundRequestProgress);
             backgroundHttpRequest.addEventListener("load", backgroundRequestComplete);
             backgroundHttpRequest.addEventListener("error", backgroundRequestFailed);
             backgroundHttpRequest.addEventListener("abort", backgroundRequestCanceled);
             backgroundHttpRequest.open( "post", "session.php", true);
             backgroundHttpRequest.setRequestHeader( "Content-type", "application/json;charset=UTF-8");
             backgroundHttpRequest.send( message);
          }
          catch( ex)
          {
             alert( "Error: SubmitLogin error=" + ex);
          }
          return false;
        }

        function SubmitRegistration()
        {
           try
           {
              if (!captchaDone) {
                alert("Invalid captcha");
                return;
              }
              let editFieldEmail      = document.getElementById("registerEmail");
              let editFieldPassword1  = document.getElementById("registerPassword1");
              let editFieldPassword2  = document.getElementById("registerPassword2");
              let emailAddress = editFieldEmail.value;
              let password1 = editFieldPassword1.value;
              let password2 = editFieldPassword2.value;
              if (password1 != password2)
              {
                alert ("Please enter the same password two times!");
                return;
              }
              if (password1.length < 6)
              {
                alert ("Parrword must be 6 or more characters!");
                return;
              }
              let jsonObj = { 
                'Request': 'Register', 
                'Email': emailAddress, 
                'Password': password1
              }

             let message = JSON.stringify( jsonObj);

             backgroundHttpRequest =  new XMLHttpRequest();
             backgroundHttpRequest.addEventListener("progress", backgroundRequestProgress);
             backgroundHttpRequest.addEventListener("load", registrationRequestComplete);
             backgroundHttpRequest.addEventListener("error", backgroundRequestFailed);
             backgroundHttpRequest.addEventListener("abort", backgroundRequestCanceled);
             backgroundHttpRequest.open( "post", "session.php", true);
             backgroundHttpRequest.setRequestHeader( "Content-type", "application/json;charset=UTF-8");
             backgroundHttpRequest.send( message);

           }
           catch( ex)
           {
            alert( "Error: SubmitRegistration error=" + ex);
           }
        }

        function SubmitLogout()
        {
          try
          {
            finishGame();
            let jsonObj = { 
                'Request': 'Logout'
              }

             let message = JSON.stringify( jsonObj);

             backgroundHttpRequest =  new XMLHttpRequest();
             backgroundHttpRequest.addEventListener("progress", backgroundRequestProgress);
             backgroundHttpRequest.addEventListener("load", ShowLogin);
             backgroundHttpRequest.addEventListener("error", backgroundRequestFailed);
             backgroundHttpRequest.addEventListener("abort", backgroundRequestCanceled);
             backgroundHttpRequest.open( "post", "session.php", true);
             backgroundHttpRequest.setRequestHeader( "Content-type", "application/json;charset=UTF-8");
             backgroundHttpRequest.send( message);
          }
          catch( ex)
          {
             alert( "Error: SubmitLogout error=" + ex);
          }
        }

        function submitScore(score) {
          let message = JSON.stringify({"score": score});

          let scoreRequset =  new XMLHttpRequest();
          scoreRequset.addEventListener("load", updateLeaders);
          scoreRequset.open( "post", "score.php", true);
          scoreRequset.setRequestHeader( "Content-type", "application/json;charset=UTF-8");
          scoreRequset.send( message);
        }

        function updateLeaders() {
          let frame = document.getElementById("leadersFrame");
          frame.src = frame.src;
        }
      }
      catch( ex)
      {
        alert( "Exception caught: " + ex);
      }


    </script>
  </head>

  <body onload="checkCookies()">
    <div class="panel">
    <div id="Login" class="pane" style="display:block;">
    <form id="LoginForm" onsubmit="SubmitLogin();return false;">
       Email address:<br>
       <input type="email" id="loginEmail" /><br><br>
       Password:<br>
       <input type="password" id="loginPassword" /><br><br>
       <input type="checkbox" id="rememberMe" />
       remember me<br><br>
       <div style="overflow:hidden" class="g-recaptcha" data-callback="setCaptcha" data-sitekey="6Lf1Gl4UAAAAABZjB6P1PnOOWOe3cGxOFanZNZ23"></div>
       <input type="submit" value="Submit"/> <br><br>
       <a href="javascript:ShowRegistration()">Register</a>
    </form>
    </div>

    <div id="Register" class="pane" >
       Email address:<br>
       <input type="email" id="registerEmail" /><br><br>
       Password:<br>
       <input type="password" id="registerPassword1" /><br><br>
       Repeat password:<br>
       <input type="password" id="registerPassword2" /><br><br>
       <div style="overflow:hidden" class="g-recaptcha" data-callback="setCaptcha" data-sitekey="6Lf1Gl4UAAAAABZjB6P1PnOOWOe3cGxOFanZNZ23"></div>
       <input type="submit" value="Submit" onclick="SubmitRegistration()"/> <br><br>
       <a href="javascript:ShowLogin();">Login</a>
    </div>

    <div id="Main" class="pane">
        <svg  version="1.1"
          baseProfile="full"
		      width="600" height="400"
		      id="svgBoard">
          <image id="svgBackground" xlink:href="images/space.jpg" x="0" y="0" height="400" width="600"/>
          <text id="svgScore" x = "50" y = "25" 
            style="font-family: Tahoma,Verdana,Arial; font-size: 14px; fill: #ffffff;">
          Score: 0
          </text>
        </svg>
        <br/>
       <a href="javascript:SubmitLogout();" >Logout</a>
    </div>
    
    <div class="pane2">
      <iframe id="leadersFrame" src="leaders.php"/>
    </div>
  </body>
</html>
