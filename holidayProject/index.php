<!DOCTYPE html>
<html>
<head>
    <title>My Holiday Project</title>
</head>

<body>
    <h1>My Holiday Project</h1>
    <h2>Server 1</h2>
<h1>
<?php

     echo "hostname is " .gethostname();
     echo "ip is " .$_SERVER['SERVER_ADDR'];
?>
</h1>
</body>
</html>