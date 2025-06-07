<?php include 'layout/header.php'; ?>

<main class="genre-section" style="padding: 30px; color: white;">

  <!-- Year section -->
  <h2 style="border-bottom: 2px solid red; padding-bottom: 10px;">Year</h2>
  <div class="year-grid" style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 15px;">
    <?php
      $years = range(2015, 2025);  // misal tahun dari 2015 sampai 2025

      foreach ($years as $year) {
        echo "<button style='
          background-color: #2b2b2b;
          color: white;
          padding: 10px 20px;
          border-radius: 8px;
          border: none;
          font-size: 16px;
          cursor: pointer;
        '>$year</button>";
      }
    ?>
  </div>

</main>

<?php include 'layout/footer.php'; ?>
