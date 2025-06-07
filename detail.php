<?php
    include 'koneksi.php';
    include 'layout/header.php';

    if (!isset($_GET['id'])) {
        echo "<p>Film tidak ditemukan.</p>";
        include 'layout/footer.php';
        exit;
    }

    $movie_id = intval($_GET['id']);

    // Ambil detail film
    $query  = "SELECT * FROM movies WHERE id = $movie_id";
    $result = $conn->query($query);
    if ($result->num_rows === 0) {
        echo "<p>Film tidak ditemukan.</p>";
        include 'layout/footer.php';
        exit;
    }
    $movie = $result->fetch_assoc();

    // Ambil genre film
    $genre_result = $conn->query("SELECT name FROM genres
                                  JOIN movie_genres ON genres.id = movie_genres.genre_id
                                  WHERE movie_genres.movie_id = $movie_id");
    $genres = [];
    while ($g = $genre_result->fetch_assoc()) {
        $genres[] = $g['name'];
    }

    // Ambil rating rata-rata dan jumlah vote dari tabel ratings
    $rating_result = $conn->query("SELECT AVG(rating) AS avg_rating, COUNT(*) AS vote_count FROM ratings WHERE movie_id = $movie_id");
    $rating_data = $rating_result->fetch_assoc();
    $average_rating = $rating_data['avg_rating'] ?? 0;
    $vote_count = $rating_data['vote_count'] ?? 0;

    // Ambil film terkait
    $genre_ids = $conn->query("SELECT genre_id FROM movie_genres WHERE movie_id = $movie_id");
    $genre_id_list = [];
    while ($g = $genre_ids->fetch_assoc()) {
        $genre_id_list[] = $g['genre_id'];
    }
    $genre_id_str = implode(',', $genre_id_list);
    $relatedQuery = "
        SELECT DISTINCT m.*
        FROM movies m
        JOIN movie_genres mg ON m.id = mg.movie_id
        WHERE mg.genre_id IN ($genre_id_str) AND m.id != $movie_id
        LIMIT 6
    ";
    $relatedMovies = $conn->query($relatedQuery);

    // Persiapan sinopsis pendek
    $full_synopsis = htmlspecialchars($movie['description']);
    $short_synopsis_limit = 200;
    $short_synopsis = $full_synopsis;
    $needs_truncation = false;
    if (strlen($full_synopsis) > $short_synopsis_limit) {
        $short_synopsis = substr($full_synopsis, 0, $short_synopsis_limit);
        $short_synopsis = substr($short_synopsis, 0, strrpos($short_synopsis, ' '));
        $needs_truncation = true;
    }
?>

<link rel="stylesheet" href="/css/detail.css">

<main class="detail-page">

    <div class="detail-container">
        <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" class="detail-poster">
        <div class="detail-info">
            <h1><?php echo htmlspecialchars($movie['title']); ?> (<?php echo $movie['release_year']; ?>)</h1>
            <p style="color: #ccc; margin-top: 10px;"><?php echo htmlspecialchars($movie['tagline'] ?? ''); ?></p>
            <p style="font-weight: 300;">
                <span style="margin-right: 10px;"><?php echo date('d M Y', strtotime($movie['release_date'])); ?></span>
                <span style="font-weight: bold; margin-right: 10px;"><?php echo htmlspecialchars($movie['language']); ?></span>
                <span><?php echo $movie['duration']; ?> Min</span>
            </p>

            <div style="margin-top: 10px; padding: 15px 0; position: relative;">
                <div style="height: 2px; background: linear-gradient(to right, #999999, #ff0000); position: absolute; top: 0; left: 0; right: 0;"></div>

                 <div style="margin-top: 15px;">
                <div class="rating-info">
                    <div class="rating-score">
                        <?= number_format($average_rating, 1); ?>
                    </div>

                    <div class="star-rating">
                        <?php
                        $filledStars = floor($average_rating);
                        $totalStars = 10;
                        for ($i = 1; $i <= $totalStars; $i++) {
                            if ($i <= $filledStars) {
                                echo '<i class="fa-solid fa-star filled"></i>';
                            } else {
                                echo '<i class="fa-solid fa-star"></i>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <div class="votes-info">
                    <i class="fa-solid fa-users"></i> <?= $vote_count; ?> votes
                </div>
            </div>

                <div style="height: 2px; background: linear-gradient(to right, #999999, #ff0000); position: absolute; bottom: 0; left: 0; right: 0;"></div>
            </div>


            <div style="position: relative; margin: 10px 0; padding-bottom: 10px;">
                <?php foreach ($genres as $g): ?>
                    <span class="genre-badge"><?php echo htmlspecialchars($g); ?></span>
                <?php endforeach; ?>

                <div style="height: 3px; background: linear-gradient(to right, #999999, #ff0000); position: absolute; bottom: 0; left: 0; right: 0;"></div>
            </div>


            <button class="btn-mylist">
                <i class="fa-solid fa-bookmark" style="margin-right: 8px;"></i> Tambahkan Ke MyList
            </button>
        </div>
    </div>

    <section>
        <div class="synopsis-header" id="synopsisHeader">
            <span>Synopsis</span>
            <i class="fa-solid fa-chevron-down"></i>
        </div>
        <div class="synopsis-content" id="synopsisContent">
            <p class="synopsis-paragraph" id="shortSynopsisText">
                <?php echo nl2br($short_synopsis); ?>
                <?php if ($needs_truncation): ?>
                    <span class="read-more-btn" id="readMoreBtn">Read More...</span>
                <?php endif; ?>
            </p>
            <?php if ($needs_truncation): ?>
                <p class="synopsis-paragraph full-synopsis-text" style="display: none;">
                    <?php echo nl2br($full_synopsis); ?>
                </p>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!empty($movie['video_url'])): ?>
        <div class="video-container">
            <video controls poster="<?php echo htmlspecialchars($movie['banner_url']); ?>">
                <source src="<?= htmlspecialchars($movie['video_url']); ?>" type="video/mp4">
                Browser tidak mendukung video.
            </video>
        </div>
    <?php endif; ?>


    <section>
        <h2 style="border-bottom: 1px solid red; padding-bottom: 5px; display: inline-block;">Film terkait</h2>
        <div class="related-movies">
            <?php while ($related = $relatedMovies->fetch_assoc()): ?>
                <a href="detail.php?id=<?php echo $related['id']; ?>">
                    <img src="<?php echo htmlspecialchars($related['poster_url']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                    <div style="margin-top: 5px; font-size: 14px;">
                        <?php echo htmlspecialchars($related['title']); ?> (<?php echo $related['release_year']; ?>)
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </section>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const synopsisHeader = document.getElementById('synopsisHeader');
        const synopsisContent = document.getElementById('synopsisContent');
        const readMoreBtn = document.getElementById('readMoreBtn');
        const fullSynopsisText = document.querySelector('.full-synopsis-text');
        const shortSynopsisText = document.getElementById('shortSynopsisText');
        const chevronIcon = synopsisHeader.querySelector('i');

        // Function to toggle synopsis
        function toggleSynopsis() {
            synopsisContent.classList.toggle('expanded');
            if (synopsisContent.classList.contains('expanded')) {
                // If expanded
                if (fullSynopsisText) {
                    fullSynopsisText.style.display = 'block';
                }
                if (shortSynopsisText) {
                    shortSynopsisText.style.display = 'none'; // Sembunyikan teks singkat saat teks penuh ditampilkan
                }
                if (readMoreBtn) {
                    readMoreBtn.style.display = 'none';
                }
                chevronIcon.classList.remove('fa-chevron-down');
                chevronIcon.classList.add('fa-chevron-up');
            } else {
                // If collapsed
                if (fullSynopsisText) {
                    fullSynopsisText.style.display = 'none';
                }
                if (shortSynopsisText) {
                    shortSynopsisText.style.display = 'block'; // Tampilkan kembali teks singkat
                }
                // Hanya tampilkan tombol "Baca selengkapnya..." jika pemotongan awalnya diperlukan
                if (readMoreBtn && <?php echo json_encode($needs_truncation); ?>) {
                    readMoreBtn.style.display = 'inline';
                }
                chevronIcon.classList.remove('fa-chevron-up');
                chevronIcon.classList.add('fa-chevron-down');
            }
        }

        // Event listener for the header click
        synopsisHeader.addEventListener('click', toggleSynopsis);

        // Event listener for "Read more..." button click
        if (readMoreBtn) {
            readMoreBtn.addEventListener('click', function(event) {
                event.stopPropagation(); // Mencegah event click menyebar ke synopsisHeader
                toggleSynopsis();
            });
        }
    });
</script>

<?php
    include 'layout/footer.php';
    $conn->close();
?>