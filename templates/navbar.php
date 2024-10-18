<!-- Navbar Section -->
<nav class="navbar is-light">
	<div class="navbar-brand">
		<a href="/" class="navbar-item" id="main">
			<strong>CARDSTOCK</strong>
		</a>
		<a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
		</a>
	</div>

	<div id="navbarBasicExample" class="navbar-menu">
		<div class="navbar-start">
			<a href="/app/inventory.php" class="navbar-item">Inventory</a>
			<a href="/app/decks.php" class="navbar-item">Decks</a>
			<a href="/app/insert_form.php" class="navbar-item">Card Input Form</a>
			<a href="/app/print_card.php" class="navbar-item">Card Print</a>
		</div>
			<!-- Search Box-->
			<form action="/app/search_page.php" method="GET" class="navbar-item">
				<div class="field has-addons">
					<div class="control">
						<input class="input" type="text" name="search_term" placeholder="Search..." required>
					</div>
					<div class="control">
						<button class="button is-info" type="submit">Search</button>
					</div>
				</div>
			</form>


			<div class="navbar-item">
				<a href="#" class="navbar-item">
					<img src="/images/account.png" width="55px" height="40px">
				</a>
				<a href="/app/login.php">Log In</a>
				<a href="/Scripts/logout.php">Log Out</a>
			</div>
		</div>
	</div>
</nav>

</body>
</html>
