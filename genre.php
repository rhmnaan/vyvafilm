<?php include 'layout/header.php'; ?>

<main class="genre-section" style="padding: 30px; color: white;">
  <h2 style="border-bottom: 2px solid red; padding-bottom: 10px;">Genre</h2>
  <div class="genre-grid" style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 15px;">
    <?php
      $genres = [
        "Comedy", "Romance", "Horror", "Action",
        "Drama", "Adventure", "Fantasy", "History",
        "Anime", "Kids", "Thriller", "Drama Korea"
      ];

      foreach ($genres as $genre) {
        echo "<button style='
          background-color: #2b2b2b;
          color: white;
          padding: 10px 20px;
          border-radius: 8px;
          border: none;
          font-size: 16px;
          cursor: pointer;
        '>$genre</button>";
      }
    ?>
  </div>
</main>

<?php include 'layout/footer.php'; ?>
