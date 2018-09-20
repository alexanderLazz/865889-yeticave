<?php

require('functions.php');

$is_auth = rand(0, 1);

$user_name = 'Alexander';
$user_avatar = 'img/user.jpg';

$categories = ["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];

$adverts = [
    [
        'item' => '2014 Rossignol District Snowboard',
        'category' => $categories[0],
        'price' => 10999,
        'url' => 'img/lot-1.jpg'
    ],
    [
        'item' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => $categories[0],
        'price' => 159999,
        'url' => 'img/lot-2.jpg'
    ],
    [
        'item' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => $categories[1],
        'price' => 8000,
        'url' => 'img/lot-3.jpg'
    ],
    [
        'item' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => $categories[2],
        'price' => 10999,
        'url' => 'img/lot-4.jpg'
    ],
    [
        'item' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => $categories[3],
        'price' => 7500,
        'url' => 'img/lot-5.jpg'
    ],
    [
        'item' => 'Маска Oakley Canopy',
        'category' => $categories[5],
        'price' => 5400,
        'url' => 'img/lot-6.jpg'
    ]
];

$page_content = include_template('index.php', ['categories' => $categories, 'adverts' => $adverts]);

$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => 'Yeticave главная', 'user_name' => $user_name, 'user_avatar' => $user_avatar, 'categories' => $categories, 'is_auth' => $is_auth]);

print($layout_content);

?>
