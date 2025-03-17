<?php
session_start();
include('db_config.php'); 

$userId = $_SESSION['user_id']; 
$query = "SELECT * FROM favorite_movies WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$favorites = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


$movies = [];
if (isset($_GET['search'])) {
    $movieTitle = urlencode($_GET['search']);
    $apiKey = 'b4122b68'; 
    $url = "http://www.omdbapi.com/?s=$movieTitle&apikey=$apiKey";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if ($data['Response'] == 'True') {
        $movies = $data['Search'];
    }
}

if (isset($_POST['add_to_favorites'])) {
    $movieTitle = $_POST['movie_title'];
    $movieId = $_POST['movie_id'];
    $posterUrl = $_POST['poster_url'];

    $stmt = $conn->prepare("INSERT INTO favorite_movies (user_id, movie_title, movie_id, poster_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $movieTitle, $movieId, $posterUrl);
    $stmt->execute();
    header("Location: movie_dashboard.php"); 
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Dashboard</title>
    <link rel="stylesheet" href="styless.css"> 
<body>
    <div class="container">
        <h1>Movie Dashboard</h1>

        <form method="GET" action="movie_dashboard.php">
            <input type="text" name="search" placeholder="Search for a movie" required>
            <button type="submit">Search</button>
        </form>

        <?php if (!empty($movies)): ?>
            <h2>Search Results</h2>
            <div class="movie-list">
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-item">
                        <img src="<?= $movie['Poster'] ?>" alt="<?= $movie['Title'] ?>" width="100">
                        <h3><?= $movie['Title'] ?></h3>
                        <p><?= $movie['Year'] ?></p>
                        <form method="POST" action="movie_dashboard.php">
                            <input type="hidden" name="movie_title" value="<?= $movie['Title'] ?>">
                            <input type="hidden" name="movie_id" value="<?= $movie['imdbID'] ?>">
                            <input type="hidden" name="poster_url" value="<?= $movie['Poster'] ?>">
                            <button type="submit" name="add_to_favorites">Add to Favorites</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        
        <h2>Your Favorite Movies</h2>
        <div class="favorite-list">
            <?php if (count($favorites) > 0): ?>
                <?php foreach ($favorites as $favorite): ?>
                    <div class="favorite-item">
                        <img src="<?= $favorite['poster_url'] ?>" alt="<?= $favorite['movie_title'] ?>" width="100">
                        <h3><?= $favorite['movie_title'] ?></h3>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have no favorite movies yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
