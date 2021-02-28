<?php

// Настройки для соединения с БД
$host = 'localhost';
$db   = 'piter';
$user = 'root';
$pass = '123';
$charset = 'utf8';

// Соединение с базой данных
$connection = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);

// Строка поиска
$mess = $_POST['message'];

if (strlen($mess) < 3){
    setcookie("errorLength","Длинна значения слишком мала");
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
else{
    setcookie("errorLength", "", time()-3600);
}

// Ищем строку поиска в таблице комментариев
$fndComs = $connection->prepare("SELECT * FROM comments WHERE body LIKE '%".$mess."%'");
$fndComs->execute();

$findComments = json_encode($fndComs->fetchAll(PDO::FETCH_ASSOC));
$findComments = json_decode($findComments);
$findPosts = [];


foreach ($findComments as $findComment) {
    $fndPosts = $connection->prepare("SELECT * FROM posts WHERE id = '".$findComment->post_id."'");
    $fndPosts->execute();
    $findPosts[] = json_decode(json_encode($fndPosts->fetchAll(PDO::FETCH_ASSOC)));
}

$posts = [];

foreach ($findPosts as $findPost) {
    foreach ($findPost as $item) {
        $posts[] = $item;
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Поиск</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/vue" type="text/javascript"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/vue-router/dist/vue-router.js"></script>
    
</head>
<body>

    <div class="container-fluid" id="app">
        
        <div class="row">
            <div class="col-12">
                
                <?php
                
                    if (count($findComments) > 0){
                
                ?>
                        
                        <table class="table table-bordered">
                                            
                              <thead>
                                            
                                 <th colspan="5" style="text-align: center">POSTS</th>

                              </thead>

                              <thead>

                                 <th>ID</th>
                                 <th>TITLE</th>
                                 <th>BODY</th>
                                 <th>COMMENTS</th>

                              </thead>
                                            
                              <tbody>
                                            
                                 <?php

                                 foreach ($posts as $post) {
                                     echo "<tr>";
                                     echo "<td>".$post->id."</td>";
                                     echo "<td>".$post->title."</td>";
                                     echo "<td>".$post->body."</td>";
                                     echo "<td>";
                                        echo "<table>";
                                             foreach ($findComments as $findComment) {
                                                 if ($findComment->post_id == $post->id){
                                                     echo "<tr>";
                                                     echo "<td>";
                                                     echo $findComment->body;
                                                     echo "</td>";
                                                     echo "</tr>";
                                                 }
                                             }
                                        echo "</table>";
                                     echo "</td>";
                                     echo "</tr>";
                                 }
                                 
                                 ?>
                                                               
                              </tbody>
                                            
                                            
                        </table>
                
                <?php
                
                    }
                
                ?>
                
                
            </div>
        </div>
        
        
        
        
    </div>

</body>
</html>
    
    