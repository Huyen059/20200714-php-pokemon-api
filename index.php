<?php
declare(strict_types=1);
//Displaying errors since this is turned off by default

ini_set('display_errors', "1");
ini_set('display_startup_errors', "1");
error_reporting(E_ALL);

$searchPoke = '';
$name = '';
$id = '';
$currentPokeImgFrontUrl = '';
$currentPokeImgBackUrl = '';
$currentPokeMoves = [];
$currentPokeTypes = [];
$urlToPrevPokemon = '';
$urlToNextPokemon = '';
$prevPokeImgUrl = '';
$nextPokeImgUrl = '';
function fetchPokemon (string $url) : array {
    $res = file_get_contents($url);
    $res = json_decode($res, true);
    return $res;
}

if (!empty($_GET['searchPoke'])) {
    $searchPoke = strtolower($_GET['searchPoke']);
    //// Fetch data of that pokemon
    $pokeInfoRes = fetchPokemon('https://pokeapi.co/api/v2/pokemon/' . $searchPoke);
    // Get ID, name, images, moves, types of current pokemon
    $id = $pokeInfoRes['id'];
    $name = $pokeInfoRes['name'];
    $currentPokeImgFrontUrl = $pokeInfoRes['sprites']['front_default'];
    $currentPokeImgBackUrl = $pokeInfoRes['sprites']['back_default'];

    $maxNumberOfMovesToDisplay = 4;
    $currentPokeMovesIndexes = array_rand($pokeInfoRes['moves'], min($maxNumberOfMovesToDisplay, count($pokeInfoRes['moves'])));
    if (!is_array($currentPokeMovesIndexes)) {
        $currentPokeMoves[] = $pokeInfoRes['moves'][0]['move']['name'];
    } else {
        foreach ($currentPokeMovesIndexes as $index) {
            $currentPokeMoves[] = $pokeInfoRes['moves'][$index]['move']['name'];
        }
    }

    foreach ($pokeInfoRes['types'] as $type) {
        $currentPokeTypes[] = $type['type']['name'];
    }

    //// Fetch evolution chain url
    $evoChainInfoRes = fetchPokemon('https://pokeapi.co/api/v2/pokemon-species/' . $id);
    $evoChainUrl = $evoChainInfoRes['evolution_chain']['url'];
    //// Fetch evolutions
    $evolutionRes = file_get_contents($evoChainUrl);
    $evolutionRes = json_decode($evolutionRes, true);

    //// Put all evolutions in an array
    $path = $evolutionRes['chain'];
    $evolutions = [$path['species']['name']];
    while (count($path['evolves_to']) > 0) {
        foreach ($path['evolves_to'] as $evo) {
            $evolutions[] = $evo['species']['name'];
        }
        $path = $path['evolves_to'][0];
    }

    //// Get position of current pokemon in array
    $pos = array_search($name, $evolutions);
    // Check if there is a previous/next evolution
    if ($pos - 1 >= 0) {
        // Fetch and display image of the previous evolution
        $prevPokeInfoRes = fetchPokemon('https://pokeapi.co/api/v2/pokemon/' . $evolutions[$pos - 1]);
        $prevPokeImgUrl = $prevPokeInfoRes['sprites']['front_default'];
        $urlToPrevPokemon = "http://becode.local/20200714-pokedex/?searchPoke=" . $prevPokeInfoRes['name'];
    }

    if ($pos + 1 < count($evolutions)) {
        // Fetch and display image of the next evolution
        $nextPokeInfoRes = fetchPokemon('https://pokeapi.co/api/v2/pokemon/' . $evolutions[$pos + 1]);
        $nextPokeImgUrl = $nextPokeInfoRes['sprites']['front_default'];
        $urlToNextPokemon = "http://becode.local/20200714-pokedex/?searchPoke=" . $nextPokeInfoRes['name'];
    }
}
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
<body id="pokedex">
<div class="container-fluid px-0">
    <header>
        <div class="headerH1">
            <h1>Pokemon</h1>
        </div>
    </header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Pokemon</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="category.php">Category</a>
                </li>
            </ul>
        </div>
    </nav>
    <main class="container">
        <div class="pokedexCtn">
            <img src="img/pokedexpaint.png" alt="pokedex">
            <div id="pokemontypecolor" class="pokemonImgCtn">
                <div class="row">
                    <div class="pokemonFrontImg"><?php if(!empty($currentPokeImgFrontUrl)) echo "<img src=$currentPokeImgFrontUrl alt='front image' style='height: 96px'/>";  ?></div>
                    <div class="pokemonBackImg"><?php if(!empty($currentPokeImgBackUrl)) echo "<img src=$currentPokeImgBackUrl alt='back image' style='height: 96px'/>";  ?></div>
                </div>
                <div class="row">
                    <p class="typeHeading"></p>&nbsp;
                    <div class="types"><?php if(!empty($currentPokeTypes)) echo 'Type: ' . implode(', ', $currentPokeTypes) ?></div>
                </div>

            </div>
            <div class="idBtnCtn">
                <a href=<?php if (!empty($pokeInfoRes) && $id > 1) echo "http://becode.local/20200714-pokedex/?searchPoke=" . ($id-1); ?>><button id="prevId" title="Previous ID" class="btn btn-success idBtnStyle"><i class="fas fa-backward"></i></button></a>
                <a href=<?php if (!empty($pokeInfoRes)) echo "http://becode.local/20200714-pokedex/?searchPoke=" . ($id+1); ?>><button id="nextId" title="Next ID" class="btn btn-warning idBtnStyle"><i class="fas fa-forward"></i></button></a>
            </div>
            <div class="idScreenCtn"><?php if(!empty($id)) echo $id; ?></div>
            <div class="nameAndMovesCtn">
                <h5 class="name"><?= ucfirst($name); ?></h5>
                <ul>
                    <div class="row">
                        <li class="col pokeMove"><?php if(!empty($currentPokeMoves[0])) echo ucfirst($currentPokeMoves[0]); ?></li>
                        <li class="col pokeMove"><?php if(!empty($currentPokeMoves[1])) echo ucfirst($currentPokeMoves[1]); ?></li>
                    </div>
                    <div class="row">
                        <li class="col pokeMove"><?php if(!empty($currentPokeMoves[2])) echo ucfirst($currentPokeMoves[2]); ?></li>
                        <li class="col pokeMove"><?php if(!empty($currentPokeMoves[3])) echo ucfirst($currentPokeMoves[3]); ?></li>
                    </div>
                </ul>
            </div>
            <div class="warningCtn">
                <p>Fetch error or invalid name/id!</p>
            </div>
            <form class="searchCtn" method="get">
                <input id="pokeinput" type="text" name="searchPoke" placeholder="Name/ID">
                <button id="run" class="btn btn-secondary" type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div class="evolutionImgCtn">
                <div title="Previous evolution" class="prevEvo"><?php if(!empty($prevPokeImgUrl)) echo "<a href=$urlToPrevPokemon><img src=$prevPokeImgUrl alt='prevEvo' /></a>";  ?></div>
                <div title="Next evolution" class="nextEvo"><?php if(!empty($nextPokeImgUrl)) echo "<a href=$urlToNextPokemon><img src=$nextPokeImgUrl alt='nextEvo' /></a>";  ?></div>
            </div>
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