<?php
/**
 * @var $name string
 * @var $surname string
 * @var $amount int
 * @var $bookName string
 * @var $bookPrice float
 */
?>

<html lang="en">
    <head>
        <style>
            .container{
                margin: auto;
            }

            .book-view-container {
                width: 100%;
            }

            .book-view-container > label, div{
                text-align: center;
                display: block;
                width: 100%;
            }

            .book-view-container > input {
                min-height: 30px;
            }

            .book-view-container > label {
                margin-top: 15px;
            }

            .book-view-container > .submit-button {
                margin-top: 20px;
                margin-left: auto;
                margin-right: auto;
                width: 200px;
                height: 50px;
                display: block;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class='book-view-container'>
                <label>Name:</label>
                <div> <?= $name ?> </div>

                <label>Surname:</label>
                <div> <?= $surname ?> </div>

                <label>Amount:</label>
                <div> <?= $surname ?> </div>

                <label>Book:</label>
                <div> <?= $bookName ?> </div>

                <label>Price:</label>
                <div> <?= $bookPrice ?> </div>
            </div>
        </div>
    </body>
</html>







