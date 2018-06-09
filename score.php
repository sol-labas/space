<?php

// Assume that the posted content is a session management request.
// Note that you should really confirm by examing the Content-type field in the header.
session_start();
require_once("db.php");
require_once("auth.php");
    
if (!islogin())
{
    echo json_encode (["error" => "user not login"]);
    die();
}
$jsonContent = json_decode( file_get_contents( 'php://input'), true);
if (empty($jsonContent["score"])) 
{
    echo json_encode (["error" => "score not found"]);
    die();
}

$addSQL = "INSERT INTO scores (userID, score) VALUES (:userID, :score)";
$query = $db->prepare($addSQL);	
$user = getUser();				
$query->bindParam( ':userID', $user["id"]);	
$query->bindParam( ':score', $jsonContent["score"]);					
$res = $query->execute();		
if( $res)
{
    echo json_encode (["result" => "ok"]);    
}
else
{
    echo json_encode (["error" => "Cannot save data", "details" => $query->errorInfo()]);   
}