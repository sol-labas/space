<?php ?>
<html>
  <head>
    <meta charset="utf-8">
    <title>Space Invaders</title>
    <link rel="stylesheet" type="text/css" href="mystyle2.css">
    <script type="text/javascript" src="spaceinvaders.js"></script>
    <script type="text/javascript">
      try
      {
        var backgroundHttpRequest = null;
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

        function SetVisibleOfDivElements(  visibleDiv)
        {
           try
           {
              function  MakeVisible( elmt, visible)
              {
                    elmt.style.visibility  =  visible? "visible": "hidden";
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
                                    alert( "made Login visible")
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
        }

        function SubmitRegistration()
        {
           try
           {
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
      }
      catch( ex)
      {
        alert( "Exception caught: " + ex);
      }
    </script>
  </head>

  <body onload="checkCookies()">

    <div id="Login" style="position:absolute;left:0px;top:0px;visibility:visible;">
       Email address:<br>
       <input type="email" id="loginEmail" /><br><br>
       Password:<br>
       <input type="password" id="loginPassword" /><br><br>
       <input type="checkbox" id="rememberMe" />
       remember me<br><br>
       <input type="submit" value="Submit" onclick="SubmitLogin();"/> <br><br>
       <a href="javascript:ShowRegistration()">Register</a>
    </div>

    <div id="Register" style="position:absolute;left:0px;top:0px;visibility:hidden;" >
       Email address:<br>
       <input type="email" id="registerEmail" /><br><br>
       Password:<br>
       <input type="password" id="registerPassword1" /><br><br>
       Repeat password:<br>
       <input type="password" id="registerPassword2" /><br><br>
       <input type="submit" value="Submit" onclick="SubmitRegistration()"/> <br><br>
       <a href="javascript:ShowLogin();">Login</a>
    </div>

    <div id="Main"  style="position:absolute;left:0px;top:0px;visibility:hidden;">
        <svg  version="1.1"
          baseProfile="full"
		      width="600" height="400"
		      id="svgBoard">
          <rect id="alien00" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien01" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien02" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien03" x="60" y="60" width="100" height="40" fill="blue" />
       
          <rect id="alien10" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien11" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien12" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien13" x="60" y="60" width="100" height="40" fill="blue" />
         
          <rect id="alien20" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien21" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien22" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien23" x="60" y="60" width="100" height="40" fill="blue" />
         
          <rect id="alien30" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien31" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien32" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien33" x="60" y="60" width="100" height="40" fill="blue" />
        
          <rect id="alien40" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien41" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien42" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien43" x="60" y="60" width="100" height="40" fill="blue" />
         
          <rect id="alien50" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien51" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien52" x="60" y="60" width="100" height="40" fill="blue" />
          <rect id="alien53" x="60" y="60" width="100" height="40" fill="blue" />

          <rect id="base0" x="100" y="100" width="100" height="40" fill="red" />
          <rect id="base1" x="100" y="100" width="100" height="40" fill="red" />
          <rect id="base2" x="100" y="100" width="100" height="40" fill="red" />
          <rect id="ship" x="120" y="120" width="100" height="40" fill="yellow" />
        </svg>
        <br/>

       <a href="javascript:SubmitLogout();" >Logout</a>
    </div>

  </body>
</html>
