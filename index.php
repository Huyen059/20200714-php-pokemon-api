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
$goToPrevPokeName = '';
$goToNextPokeName = '';
$prevPokeImgUrl = '';
$nextPokeImgUrl = '';

if (!empty($_GET['searchPoke'])) {
    $searchPoke = strtolower($_GET['searchPoke']);
    //// Fetch data of that pokemon
    $pokeInfoRes = file_get_contents('https://pokeapi.co/api/v2/pokemon/' . $searchPoke);
    $pokeInfoRes = json_decode($pokeInfoRes, true);
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
    $evoChainInfoRes = file_get_contents('https://pokeapi.co/api/v2/pokemon-species/' . $id);
    $evoChainInfoRes = json_decode($evoChainInfoRes, true);
    $evoChainUrl = $evoChainInfoRes['evolution_chain']['url'];
    //// Fetch evolutions
    $evolutionRes = file_get_contents($evoChainUrl);
    $evolutionRes = json_decode($evolutionRes, true);

    //// Put all evolutions in an array
    $path = $evolutionRes['chain'];
    $evolutions = [$path['species']['name']];
    function getSpecies($path, $evolutions)
    {
        while (count($path['evolves_to']) > 0) {
            foreach ($path['evolves_to'] as $evo) {
                $evolutions[] = $evo['species']['name'];
            }
            $path = $path['evolves_to'][0];
        }
        return $evolutions;
    }
    $evolutions = getSpecies($path, $evolutions);

    //// Get position of current pokemon in array
    $pos = array_search($name, $evolutions);
    // Variables to store the name of the previous/next evolution (if they exists)
    $prevEvolutionName = '';
    $nextEvolutionName = '';
    // Check if there is a previous evolution
    if ($pos - 1 >= 0) {
        $prevEvolutionName = $evolutions[$pos - 1];
        // Fetch and display image of the previous evolution

        $prevPokeInfoRes = file_get_contents('https://pokeapi.co/api/v2/pokemon/' . $prevEvolutionName);
        $prevPokeInfoRes = json_decode($prevPokeInfoRes, true);
        $prevPokeImgUrl = $prevPokeInfoRes['sprites']['front_default'];
        $goToPrevPokeName = "http://becode.local/20200714-pokedex/?searchPoke=" . $prevPokeInfoRes['name'];
    }

    if ($pos + 1 < count($evolutions)) {
        $nextEvolutionName = $evolutions[$pos + 1];
        // Fetch and display image of the next evolution
        $nextPokeInfoRes = file_get_contents('https://pokeapi.co/api/v2/pokemon/' . $nextEvolutionName);
        $nextPokeInfoRes = json_decode($nextPokeInfoRes, true);
        $nextPokeImgUrl = $nextPokeInfoRes['sprites']['front_default'];
        $goToNextPokeName = "http://becode.local/20200714-pokedex/?searchPoke=" . $nextPokeInfoRes['name'];
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
                <div title="Previous evolution" class="prevEvo"><?php if(!empty($prevPokeImgUrl)) echo "<a href=\"$goToPrevPokeName\"><img src=$prevPokeImgUrl alt='prevEvo' /></a>";  ?></div>
                <div title="Next evolution" class="nextEvo"><?php if(!empty($nextPokeImgUrl)) echo "<a href=\"$goToNextPokeName\"><img src=$nextPokeImgUrl alt='nextEvo' /></a>";  ?></div>
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