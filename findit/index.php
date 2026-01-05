<?php 
include 'header.php'; 
include 'db_connect.php'; 
?>

<body>
  <section class="hero" id="search">
    <div class="hero-container">
      <h1>Lost Something? <span>We'll Help You Find It</span></h1>
      <p>Report lost items, search found items, and reconnect with your belongings through our community-driven platform.</p>
      
      <div class="search-section">
        <form class="search-form" id="searchForm">
          <div class="search-row">
            <input type="text" class="search-input" placeholder="What are you looking for?" id="searchQuery">
            
            <select class="search-select" id="categorySelect">
              <option value="">All Categories</option>
              <?php
                $catSql = "SELECT * FROM tbl_categories";
                $catResult = $conn->query($catSql);
                if ($catResult->num_rows > 0) {
                    while($cat = $catResult->fetch_assoc()) {
                        echo '<option value="'.$cat['ID'].'">'.$cat['CategoryName'].'</option>';
                    }
                }
              ?>
            </select>
          </div>
          <button type="submit" class="search-btn">🔍 Search Items</button>
        </form>

        <div class="categories-grid">
          <button class="category-tag active" data-category="">All</button>
          <?php
            if ($catResult->num_rows > 0) {
                // Reset pointer to reuse result set
                $catResult->data_seek(0); 
                $count = 0;
                while($cat = $catResult->fetch_assoc()) {
                    if($count >= 5) break; 
                    echo '<button class="category-tag" data-category="'.$cat['ID'].'">'.htmlspecialchars($cat['CategoryName']).'</button>';
                    $count++;
                }
            }
          ?>
        </div>
      </div>
    </div>
  </section>

  <section class="how-it-works" id="how-it-works">
    <div class="section-container">
      <h2 class="section-title">How It Works</h2>
      <p class="section-subtitle">Simple steps to recover your lost belongings</p>
      <div class="steps-grid">
        <div class="step-card">
          <div class="step-icon">📝</div>
          <span class="step-number">1</span>
          <h3>Report Item</h3>
          <p>Create a detailed report of your lost or found item with photos and location information.</p>
        </div>
        <div class="step-card">
          <div class="step-icon">🔍</div>
          <span class="step-number">2</span>
          <h3>Smart Matching</h3>
          <p>Our system automatically matches lost and found items based on description and location.</p>
        </div>
        <div class="step-card">
          <div class="step-icon">💬</div>
          <span class="step-number">3</span>
          <h3>Connect & Verify</h3>
          <p>Get notified of potential matches and verify ownership through our secure process.</p>
        </div>
        <div class="step-card">
          <div class="step-icon">🎉</div>
          <span class="step-number">4</span>
          <h3>Recover Item</h3>
          <p>Arrange a safe pickup and reunite with your belongings. It's that simple!</p>
        </div>
      </div>
    </div>
  </section>

  <section class="food-waste" id="food-waste">
    <div class="food-waste-container">
      <div class="food-waste-content">
        <h2>Available Food Offers nearby!</h2>
        <p>Browse existing food offers, near your city from top quality restaurants from street foods to 5 star quality foods (discount available upto 50%)</p>
        <div class="food-features">
          <div class="food-feature">
            <div class="food-feature-icon">🍽️</div>
            <span>Share Top quality food in a discounted price</span>
          </div>
          <div class="food-feature">
            <div class="food-feature-icon">📊</div>
            <span>Reduce food waste from reputed restaurants</span>
          </div>
          <div class="food-feature">
            <div class="food-feature-icon">🤝</div>
            <span>Connect with local food banks & charities</span>
          </div>
          <div class="food-feature">
            <div class="food-feature-icon">🏆</div>
            <span>Earn rewards points by attending campaigns</span>
          </div>
        </div>
        <button class="btn btn-food">Learn More →</button>
      </div>
      <div class="food-waste-visual">
        <div class="food-stat-card">
          <div class="food-stat-icon">🍽️</div>
          <div>
            <div class="food-stat-number">2,500+</div>
            <div class="food-stat-label">Meals Saved</div>
          </div>
        </div>
        <div class="food-stat-card">
          <div class="food-stat-icon">🌍</div>
          <div>
            <div class="food-stat-number">1.2 tons</div>
            <div class="food-stat-label">CO₂ Prevented</div>
          </div>
        </div>
        <div class="food-stat-card">
          <div class="food-stat-icon">👥</div>
          <div>
            <div class="food-stat-number">50+</div>
            <div class="food-stat-label">Active Restaurants</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="reviews" id="reviews">
    <div class="section-container">
      <h2 class="section-title">What People Say</h2>
      <p class="section-subtitle">Real stories from our community members</p>
      <div class="reviews-grid">
        <div class="review-card">
          <div class="review-stars">★★★★★</div>
          <p class="review-text">"I lost my laptop bag in the library and thought it was gone forever. Within 24 hours, someone reported finding it and I got it back with everything intact!"</p>
          <div class="review-author">
            <div class="review-avatar">SK</div>
            <div>
              <div class="review-name">Sarah Kim</div>
              <div class="review-role">Engineering Student</div>
            </div>
          </div>
        </div>
        <div class="review-card">
          <div class="review-stars">★★★★★</div>
          <p class="review-text">"The food waste feature is amazing! Our department now donates surplus food from events instead of throwing it away. Great for the community and environment."</p>
          <div class="review-author">
            <div class="review-avatar">MJ</div>
            <div>
              <div class="review-name">Mike Johnson</div>
              <div class="review-role">Event Coordinator</div>
            </div>
          </div>
        </div>
        <div class="review-card">
          <div class="review-stars">★★★★★</div>
          <p class="review-text">"Found someone's wallet in the parking lot. Reported it on FindIt and the owner claimed it the same day. The verification process made sure it went to the right person."</p>
          <div class="review-author">
            <div class="review-avatar">AP</div>
            <div>
              <div class="review-name">Alex Patel</div>
              <div class="review-role">Business Major</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="cta">
    <div class="section-container">
      <h2>Ready to Find What You've Lost?</h2>
      <p>Join thousands of users who have successfully recovered their belongings through our platform. It's free and takes just a minute to get started.</p>
      <div class="cta-buttons">
        <button class="btn btn-white" id="ctaRegisterBtn">Create Free Account</button>
        <button class="btn btn-ghost">Report Found Item</button>
      </div>
    </div>
  </section>
</body>

<script>
    $(document).ready(function() {
      // 1. Click Category Button
      $('.category-tag').on('click', function(e) {
        e.preventDefault(); // Prevent jump
        $('.category-tag').removeClass('active');
        $(this).addClass('active');
        $('#categorySelect').val($(this).data('category'));
      });
      
      // 2. Submit Search Form -> Redirect to Browse Page
      $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        
        // Use .val() safely
        const query = $('#searchQuery').val();
        const category = $('#categorySelect').val();

        // Redirect logic: we removed location logic completely to fix the error
        let redirectUrl = `browse.php?q=${encodeURIComponent(query)}&cat=${encodeURIComponent(category)}`;
        
        window.location.href = redirectUrl;
      });
    });
</script>

<?php include 'footer.php'; ?>