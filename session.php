<?php

      // Assume that the posted content is a session management request.
      // Note that you should really confirm by examing the Content-type field in the header.
	  session_start();
	  require_once("db.php");
      require_once("auth.php");

      function complexEnough($pass)
	  {
		  if( strlen( $pass) < 6)
              return false;
		  
		  return true;
	  }

      $jsonContent = json_decode( file_get_contents( 'php://input'), true);

      $requestType = $jsonContent[ 'Request'];

      if( $requestType == 'Login')
      {
          $emailAddr = $jsonContent[ 'Email'];
          $password = $jsonContent[ 'Password'];
          $rememberMe = $jsonContent[ 'RememberMe'];

          $query = $db->prepare( "select * from users where email=:emailAddress");	
          $query->bindParam( ':emailAddress', $emailAddr);					
          $query->execute();					
          $row = $query->fetch(PDO::FETCH_ASSOC);
         
          if( $row == false)
          {
              $jsonResponse = array( 'ErrorCode' => '1', 'Message' => "Invalid credentials.");
              echo json_encode( $jsonResponse);
          }
         else
         {
             $savedHash = $row['password'];
                         
             if( password_verify( $password, $savedHash))
             {	
                login($row, $rememberMe);
                $jsonResponse = array( 'ErrorCode' => '0', 'Message' => "");
                echo json_encode( $jsonResponse);
             }	
              else
              {
                 $jsonResponse = array( 'ErrorCode' => '1', 'Message' => "Invalid credentials.");
                 echo json_encode( $jsonResponse);
              }
         }
 

         $query = null;

 
      }
      else if( $requestType == 'Register')
      {
        $emailAddr = $jsonContent[ 'Email'];
        $password = $jsonContent[ 'Password'];        
	  
        # Create connection
        try
        {
            if( $emailAddr == "")
            {
                $jsonResponse = array( 'ErrorCode' => '1', 'Message' => "Registration failed: email address field was blank");
                echo json_encode( $jsonResponse);
                return;
            }
            else if(strpos($emailAddr, "@") === false) 
            {
                $jsonResponse = array( 'ErrorCode' => '1', 'Message' => "Registration failed: incorrect email address");
                echo json_encode( $jsonResponse);
                return;
            }

            else if($password == "")
            {
                $jsonResponse = array( 'ErrorCode' => '1', 'Message' => "Registration failed: passwords were blank");
                echo json_encode( $jsonResponse);
                return;
            }
            else if( ! complexEnough($password))
            {
                $jsonResponse = array( 'ErrorCode' => '1', 'Message' => "Registration failed: password is too simple");
                echo json_encode( $jsonResponse);
                return;	   
            }
            else
            {
                $query = $db->prepare( "insert into users ( email, password) values( :emailAddress, :password)");					
                $query->bindParam( ':emailAddress', $emailAddr);	
                $hashedPassword = password_hash( $password, PASSWORD_DEFAULT);
                $query->bindParam( ':password', $hashedPassword);					
                $res = $query->execute();					
                if( $res)
                {
                    $jsonResponse = array( 'ErrorCode' => '0', 'Message' => "Registration completed");
                    echo json_encode( $jsonResponse);
                    return;	     
                }
                else
                {
                    $jsonResponse = array( 'ErrorCode' => '1', 'Message' => "User already allocated");
                    echo json_encode( $jsonResponse);
                    return;	     
                }    
               
                $query= null;
                    
                $db = null;	
            }
        }
        catch( PDOException $e)
        {
            $jsonResponse = array( 'ErrorCode' => '1', 'Message' => "Error:" . $e->getMessage());
            echo json_encode( $jsonResponse);
            return;	     
        }
        catch( Exception $e)
        {
            $jsonResponse = array( 'ErrorCode' => '1', 'Message' => "Error:" . $e->getMessage());
            echo json_encode( $jsonResponse);
            return;	     
        }
      }
      else if( $requestType == 'Logout')
      {
        logout();
        $jsonResponse = array( 'ErrorCode' => '0', 'Message' => "");
        echo json_encode( $jsonResponse);
      }
      else
      {
          $jsonResponse = array( 'ErrorCode' => '1', 'Message' => "Error in request: Invalid session request type");
      	  echo json_encode( $jsonResponse);
      }