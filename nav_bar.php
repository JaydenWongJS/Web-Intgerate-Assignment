      <div class="nav_bar_container">
          <nav class="nav_bar">
              <div class="logo">
                  <img src="/image/logo.png" alt="Logo" width="70" height="70">
              </div>
              <ul class="nav_url">
                  <li><a class="link" href="/index.php"><i class="fas fa-home"></i> Home</a></li>
                  <li><a class="link" href="/aboutus.php"><i class="fas fa-info-circle"></i> About</a></li>
                  <li><a class="link" href="/shop.php"><i class="fas fa-dollar-sign"></i> Shop</a></li>
                  <li><a class="link" href="/contactus.php"><i class="fas fa-envelope"></i> Contact</a></li>
                  <?php if ($_user?->role == "member"): ?>
                      <li>
                          <a href="/add_cart/cart.php" class="link"><i class="fas fa-shopping-cart">
                              </i>My Cart
                              (
                              <?= count_cart(); ?>
                              )
                          </a>
                      </li>
                  <?php endif ?>
              </ul>

              <?php if (!$_user?->role == "member" && !$_user?->role == "admin"): ?>
                  <div class="user_settings">
                      <a href="login_register/login.php" class="login"><i class="fas fa-sign-in-alt"></i> Login</a>
                      <a href="login_register/register.php" class="login"><i class='fas fa-user-cog'></i> Register</a>
                  </div>
              <?php endif ?>
              <?php if ($_user): ?>

                  <div class="login_nav_user">
                      <img src="/uploadsImage/userProfile/<?= $_user->image ?>" class="user-profile-img">
                      <div class="user-details">
                          <span class="user-name"><?= $_user->firstname; ?> <?= $_user->lastname; ?></span>
                          <span class="user-role"><?= $_user->role ?></span>
                      </div>
                      <!-- Dropdown Trigger -->
                      <div class="dropdown-toggle">
                          <i class="fas fa-chevron-down"></i> <!-- Down arrow icon -->
                      </div>

                      <!-- Dropdown Menu -->
                      <div class="dropdown-content">
                          <?php if ($_user->role === 'admin'): ?>
                              <a class="link" href="/adminPage/adminDashboard.php"><i class='fas fa-tachometer-alt'></i>Admin Dashboard</a>
                          <?php else: ?>
                              <a class="link" href="/profile/profile.php"><i class='fas fa-user-cog'></i> Profile</a>
                              <a class="link" href="/myOrder.php">  <i class="fas fa-receipt icon"></i>Orders  <span class="notification-badge"><?=countCurrentOrder($_user->member_id)?></span></a>
                          <?php endif; ?>
                          
                          <a href="/log_out.php" class="logout"><i class='fas fa-sign-out-alt'></i> Logout</a>
                      </div>
                  </div>

              <?php endif ?>

          </nav>
      </div>