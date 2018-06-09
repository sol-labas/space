<html>
<head>
  <meta charset="utf-8">
  <title>Space Invaders</title>
  <style>
  table {
     border: 1px solid grey; 
  }
  th {
      border: 1px solid grey;
      padding: 5px;
  }
  td {
      border: 1px solid grey;
      padding: 5px;
  }
  </style>
</head>
<body>
<?php
session_start();
require_once("db.php");

$scoreSQL = "
    SELECT email, MAX(score) max_score, AVG(score) avg_score, SUM(score) sum_score FROM users u
    JOIN scores s ON (u.id = s.userID)
    GROUP BY email
    ORDER BY sum_score
";
$query = $db->prepare($scoreSQL);					
$res = $query->execute();		
if( $res)
{
    echo "<table cellpadding=\"0\" cellspacing=\"0\">";
    echo "<tr><th>Email</th><th>Total score</th><th>Max score</th><th>Average score</th></tr>";
    while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        echo "<tr><td>{$row->email}</td><td>{$row->sum_score}</td><td>{$row->max_score}</td><td>{$row->avg_score}</td></tr>";
    }
    echo "</table>";
}
else
{
    echo "<p style=\"color: red\">Error: Cannot access Leader Board</>";
}
?>
</body>