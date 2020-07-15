<?php
declare(strict_types=1);
//Displaying errors since this is turned off by default

ini_set('display_errors', "1");
ini_set('display_startup_errors', "1");
error_reporting(E_ALL);

//Define a function to get file content as array
function fetchPokemon(string $url): array
{
    $res = file_get_contents($url);
    $res = json_decode($res, true);
    return $res;
}

//VARIABLES
//Current page
$currentPage = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

////GET DATA AND WORK WITH THEM
$type = '';

if (isset($_GET['types'])) {
    $type = $_GET['types'][0];
}

// Link here: becode.local/20200714-pokedex/category.php?types%5B%5D=fire&submit=Get+Selected+Values&page=2

$pokemonsResponse = (isset($_GET['types'])) ? fetchPokemon("https://pokeapi.co/api/v2/type/" . $type) :
    fetchPokemon('https://pokeapi.co/api/v2/pokemon?limit=1000');
//Array containing all pokemons
$pokemons = (isset($_GET['types'])) ? $pokemonsResponse['pokemon'] : $pokemonsResponse['results'];

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/2e0a884014.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=BenchNine:wght@300&family=Orbitron&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
          integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <title>Pokedex</title>
</head>
<body id="pokedex"></body>
<div class="container-fluid px-0">
    <header>
        <div class="headerH1">
            <h1>Pokemon</h1>
        </div>
    </header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Pokemon</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="category.php">Category <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>
    <main class="container text-center">
        <div class="d-flex pt-5 pb-4 justify-content-center">
            <nav aria-label="...">
                <ul class="pagination">
                    <li class="page-item <?php if ($currentPage === 1) echo 'disabled'; ?>">
                        <a class="page-link"
                           href="http://becode.local/20200714-pokedex/category.php?<?php
                           if (isset($_GET['types'])) echo "types%5B%5D=$type&submit=Get+Selected+Values&";
                           ?>page=1"
                        >
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                    <li class="page-item <?php if ($currentPage === 1) echo 'disabled'; ?>">
                        <a class="page-link"
                           href="http://becode.local/20200714-pokedex/category.php?<?php
                           if (isset($_GET['types'])) echo "types%5B%5D=$type&submit=Get+Selected+Values&";
                           ?>page=<?php echo $currentPage === 1 ? $currentPage : $currentPage - 1; ?>"
                        >
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link"
                           href="http://becode.local/20200714-pokedex/category.php?<?php
                           if (isset($_GET['types'])) echo "types%5B%5D=$type&submit=Get+Selected+Values&";
                           ?>page=<?= $currentPage; ?>"
                        >
                            <?= $currentPage; ?>
                            <span class="sr-only">(current)</span>
                        </a>
                    </li>
                    <li class="page-item <?php if ($currentPage === (int)ceil(count($pokemons) / 20)) echo 'disabled'; ?>">
                        <a class="page-link"
                           href="http://becode.local/20200714-pokedex/category.php?<?php
                           if (isset($_GET['types'])) echo "types%5B%5D=$type&submit=Get+Selected+Values&";
                           ?>page=<?php echo $currentPage === ceil(count($pokemons) / 20) ? $currentPage : $currentPage + 1; ?>"
                        >
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </li>
                    <li class="page-item <?php if ($currentPage === (int)ceil(count($pokemons) / 20)) echo 'disabled'; ?>">
                        <a class="page-link"
                           href="http://becode.local/20200714-pokedex/category.php?<?php
                           if (isset($_GET['types'])) echo "types%5B%5D=$type&submit=Get+Selected+Values&";
                           ?>page=<?= ceil(count($pokemons) / 20); ?>"
                        >
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="d-flex justify-content-center mb-5">
            <form action="#" method="get" class="mr-4">
                <select name="types[]" class="mr-2">
                    <option value="<?php echo (!empty($type)) ? $type : 'none'; ?>"><?php echo (!empty($type)) ? 'Current type: ' . $type : 'Choose a type'; ?></option>
                    <option value="normal">Normal</option>
                    <option value="fighting">Fighting</option>
                    <option value="flying">Flying</option>
                    <option value="poison">Poison</option>
                    <option value="ground">Ground</option>
                    <option value="rock">Rock</option>
                    <option value="bug">Bug</option>
                    <option value="ghost">Ghost</option>
                    <option value="steel">Steel</option>
                    <option value="fire">Fire</option>
                    <option value="water">Water</option>
                    <option value="grass">Grass</option>
                    <option value="electric">Electric</option>
                    <option value="psychic">Psychic</option>
                    <option value="ice">Ice</option>
                    <option value="dragon">Dragon</option>
                    <option value="dark">Dark</option>
                    <option value="fairy">Fairy</option>
                    <option value="shadow">Shadow</option>
                    <option value="unknown">Unknown</option>
                </select>
                <input type="submit" name="submit" value="Display one type"/>

            </form>
            <a href="category.php"><button>Display all types</button></a>
        </div>

        <div class="row">
            <?php
            foreach (array_slice($pokemons, ($currentPage - 1) * 20, 20) as $pokemon):
                ?>
                <div class="col-sm-6 col-md-3">
                    <div>
                        <img
                                alt="Image N/A"
                                src=
                                <?php
                                $url = (isset($_GET['types'])) ? $pokemon['pokemon']['url'] : $pokemon['url'];
                                echo fetchPokemon($url)['sprites']['front_default'];
                                ?>
                        />
                    </div>
                    <div>
                        <?php echo ucfirst((isset($_GET['types'])) ? $pokemon['pokemon']['name'] : $pokemon['name']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="d-flex pt-4 pb-5 justify-content-center">
            <nav aria-label="...">
                <ul class="pagination">
                    <li class="page-item <?php if ($currentPage === 1) echo 'disabled'; ?>">
                        <a class="page-link"
                           href="http://becode.local/20200714-pokedex/category.php?<?php
                           if (isset($_GET['types'])) echo "types%5B%5D=$type&submit=Get+Selected+Values&";
                           ?>page=1"
                        >
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                    <li class="page-item <?php if ($currentPage === 1) echo 'disabled'; ?>">
                        <a class="page-link"
                           href="http://becode.local/20200714-pokedex/category.php?<?php
                           if (isset($_GET['types'])) echo "types%5B%5D=$type&submit=Get+Selected+Values&";
                           ?>page=<?php echo $currentPage === 1 ? $currentPage : $currentPage - 1; ?>"
                        >
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link"
                           href="http://becode.local/20200714-pokedex/category.php?<?php
                           if (isset($_GET['types'])) echo "types%5B%5D=$type&submit=Get+Selected+Values&";
                           ?>page=<?= $currentPage; ?>"
                        >
                            <?= $currentPage; ?>
                            <span class="sr-only">(current)</span>
                        </a>
                    </li>
                    <li class="page-item <?php if ($currentPage === (int)ceil(count($pokemons) / 20)) echo 'disabled'; ?>">
                        <a class="page-link"
                           href="http://becode.local/20200714-pokedex/category.php?<?php
                           if (isset($_GET['types'])) echo "types%5B%5D=$type&submit=Get+Selected+Values&";
                           ?>page=<?php echo $currentPage === ceil(count($pokemons) / 20) ? $currentPage : $currentPage + 1; ?>"
                        >
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </li>
                    <li class="page-item <?php if ($currentPage === (int)ceil(count($pokemons) / 20)) echo 'disabled'; ?>">
                        <a class="page-link"
                           href="http://becode.local/20200714-pokedex/category.php?<?php
                           if (isset($_GET['types'])) echo "types%5B%5D=$type&submit=Get+Selected+Values&";
                           ?>page=<?= ceil(count($pokemons) / 20); ?>"
                        >
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </main>
    <footer>
        &copy; Copyright 2020.
    </footer>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
        crossorigin="anonymous"></script>
</body>
</html>