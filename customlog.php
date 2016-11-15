<?php

$dbname = 'DSHOPDRUPLET01';
$host = 'vpc-devshop-dev.cm8ulxn1bw7j.us-east-1.rds.amazonaws.com';
$username = 'dshopusr01';
$password = 'dshop$l1s#3r';

$logs= "";

try{
   $dbh = new PDO('mysql:dbname=DSHOPDRUPLET01;host=vpc-devshop-dev.cm8ulxn1bw7j.us-east-1.rds.amazonaws.com', $username, $password, array(PDO::ATTR_PERSISTENT => true));

   $siteQuery = "SELECT value as lastjob from site_variables WHERE name='lastjobid' ";
   $siteRes = $dbh->query($siteQuery);
   foreach($siteRes as $rw){
     $lastjobid = $rw['lastjob'];
   }

   //$query = "SELECT *, j.id as job_id from job_logs j, nbcu_sites s WHERE j.site_id = s.id  AND j.id >= " .$lastjobid. "  ORDER BY j.created_at, j.id asc";
   $query = "SELECT *, j.id as job_id from job_logs j, nbcu_sites s WHERE j.site_id = s.id ORDER BY j.created_at asc";
   $result = $dbh->query($query);
   foreach($result as $row){
          $logs.= $row['created_at']." ";
          $logs.="job_id=".$row['job_id']." ";
          $logs.="site_name=".$row['site_name']." ";
          $logs.="data=".$row['data']." ";
          $logs.="message=".$row['message']." ";
          $logs.="status=".$row[10]." ";
          $logs.="finished=".$row['finished_at']." ";
          $logs.="\r\n";
          //echo $logs;
          $last_jobid = $row['job_id'];
   }

   $updateQ = "UPDATE site_variables set value=". $last_jobid . " WHERE name='lastjobid' ";
   $rowChange = $dbh->exec($updateQ);

}catch(PDOEXception $e){
   print "Error!: " . $e->getMessage() . "<br/>";
   die();
}

/*** Log writing ****/

try{
   $fp = fopen("/var/log/splunk/devshop/devshop_job.log", "w");
   fwrite($fp, $logs);
   fclose($fp);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>
