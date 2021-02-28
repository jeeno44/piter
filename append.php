<?php

ini_set('error_reporting', E_ALL);

// Настройки для соединения с БД
$host = 'localhost';
$db   = 'piter';
$user = 'root';
$pass = '123';
$charset = 'utf8';

// Соединение с базой данных
$connection = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);

// Получаем список всех постов
$urlPosts = "https://jsonplaceholder.typicode.com/posts";
$requestPosts = file_get_contents($urlPosts);
$posts = json_decode($requestPosts);

// Проверяем есть ли пользователи в таблице users
$sql = "SELECT COUNT(*) FROM users";
$res = $connection->query($sql);
$countUsers = intval($res->fetchColumn());

// Если пользователей нет то добавляем пользователей
if ($countUsers <= 0){

    $users = [];
    foreach ($posts as $post) {
        $users[] = $post->userId;
    }
    $users = array_unique($users);

    foreach ($users as $user) {
        $sqlInsertUsers = "INSERT INTO users (id, name) VALUES (?, ?)";
        $insUsers = $connection->prepare($sqlInsertUsers);
        $insUsers->execute([NULL,"user$user"]);
    }
    // Выводим сообщение что пользователи добавлены
    echo "ПОЛЬЗОВАТЕЛИ ДОБАВЛЕНЫ \n";
}

// Проверяем есть ли посты в таблице posts
$sql = "SELECT COUNT(*) FROM posts";
$res = $connection->query($sql);
$countPosts = intval($res->fetchColumn());

// Если постов нет то добавляем посты
if ($countPosts < count($posts)){

    foreach ($posts as $post) {
        $sqlInsertPosts = "INSERT INTO posts (id, user_id, title, body) VALUES (?, ?, ?, ?)";
        $insPosts = $connection->prepare($sqlInsertPosts);
        $insPosts->execute([NULL,"$post->userId","$post->title","$post->body"]);
    }
    echo "Добавлено ".count($posts)." записей\n";
}

// Получаем комментарии
$urlComments = "https://jsonplaceholder.typicode.com/comments";
$requestComments = file_get_contents($urlComments);
$comments = json_decode($requestComments);

// Проверяем есть ли комментарии в таблице comments
$sql = "SELECT COUNT(*) FROM comments";
$res = $connection->query($sql);
$countComments = intval($res->fetchColumn());

// Если комментариев меньше чем записей в запросе то вставляем их в БД
if ($countComments < count($comments)){

    foreach ($comments as $comment) {
        $sqlInsertComments = "INSERT INTO comments (id, post_id, name, email, body) VALUES (?, ?, ?, ?, ?)";
        $insComments = $connection->prepare($sqlInsertComments);
        $insComments->execute([NULL,"$comment->postId","$comment->name","$comment->email","$comment->body"]);
    }
    echo "Добавлено ".count($comments)." комментариев\n";
}

