<?php
include 'koneksi.php';
include 'layout/header.php';

// Ambil semua genre
$genresQuery = "SELECT * FROM genres";
$genresResult = $conn->query($genresQuery);

// Ambil semua film banner
$bannerQuery = $conn->query("SELECT * FROM movies WHERE is_banner = 1 ORDER BY created_at DESC");
?>

<main>
  <!-- HERO SLIDER -->
  <section class="hero-slider">
    <div class="slides" style="display: flex; transition: transform 0.5s ease;">
      <?php while ($banner = $bannerQuery->fetch_assoc()): ?>
        <a href="detail.php?id=<?= $banner['id']; ?>" style="min-width: 100%; position: relative; display: block; text-decoration: none; color: inherit;">
          <div class="hero-slide" style="min-width: 100%; position: relative;">
            <img src="<?= htmlspecialchars($banner['banner_url']); ?>" alt="<?= htmlspecialchars($banner['title']); ?>" class="hero-img">
            <div class="hero-content">
              <h1><?= htmlspecialchars($banner['title']); ?> (<?= $banner['release_year']; ?>)</h1>
              <p>
                <?php
                $movie_id = $banner['id'];
                $genre_result = $conn->query("SELECT name FROM genres 
                                              JOIN movie_genres ON genres.id = movie_genres.genre_id 
                                              WHERE movie_genres.movie_id = $movie_id");
                $genres = [];
                while ($g = $genre_result->fetch_assoc()) {
                  $genres[] = $g['name'];
                }
                echo htmlspecialchars(implode(', ', $genres));
                ?>
              </p>
            </div>
          </div>
        </a>
      <?php endwhile; ?>
    </div>
    <button class="prev" onclick="plusSlides(-1)">&#10094;</button>
    <button class="next" onclick="plusSlides(1)">&#10095;</button>
  </section>

  <!-- SECTION PER GENRE -->
  <?php while ($genre = $genresResult->fetch_assoc()): ?>
    <section class="category">
      <h2><?= htmlspecialchars($genre['name']) ?><i class="arrow right"></i></h2>
      <div class="movie-list">
        <?php
        $genreId = $genre['id'];
        $moviesQuery = "
          SELECT m.*
          FROM movies m
          JOIN movie_genres mg ON m.id = mg.movie_id
          WHERE mg.genre_id = $genreId
          ORDER BY m.created_at DESC
          LIMIT 6
        ";
        $moviesResult = $conn->query($moviesQuery);
        while ($movie = $moviesResult->fetch_assoc()):
        ?>
          <div class="movie">
            <a href="detail.php?id=<?= $movie['id']; ?>">
              <img src="<?= htmlspecialchars($movie['poster_url']); ?>" alt="<?= htmlspecialchars($movie['title']); ?>">
              <p class="movie-title"><?= htmlspecialchars($movie['title']); ?><br>(<?= $movie['release_year']; ?>)</p>
            </a>
          </div>
        <?php endwhile; ?>
      </div>
    </section>
  <?php endwhile; ?>
</main>

<script>
let slideIndex = 0;
const slides = document.querySelector('.slides');
const totalSlides = document.querySelectorAll('.hero-slide').length;

function showSlide(index) {
  if (index >= totalSlides) slideIndex = 0;
  else if (index < 0) slideIndex = totalSlides - 1;
  else slideIndex = index;
  slides.style.transform = `translateX(-${slideIndex * 100}%)`;
}

function plusSlides(n) {
  showSlide(slideIndex + n);
}

// Auto slide
setInterval(() => {
  plusSlides(1);
}, 5000);

// Tampilkan slide pertama saat halaman dimuat
showSlide(slideIndex);
</script>

<?php
include 'layout/footer.php';
$conn->close();
?>
