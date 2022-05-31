<nav class="navbar navbar-expand-xl p-0">
	<div class="navbar__left">
		<div class="header__logo">
			<a href="/">
				<img class="header__logo--desktop" src="<?php echo get_template_directory_uri(); ?>/images/logo.svg" alt="Hotspring" />
				<img class="header__logo--mobile" src="<?php echo get_template_directory_uri(); ?>/images/logo-small.svg" alt="Hotspring" />
			</a>
		</div>
		<?php echo do_shortcode("[main-menu]");  ?>
	</div>
	<div class="navbar__right">
		<?//php echo do_shortcode("[additional-menu]");  ?>

		<div class="additional-menu">
			<div class="menu__item">
				<a href="#" data-mobile-title="Dealer">Find Dealer</a>
			</div>
			<div class="menu__item">
				<a href="/get-brochure" data-mobile-title="Brochure">Get Brochure</a>
			</div>
			<div class="menu__item dropdown lang">
				<a href="#" class="link-dark dropdown-toggle" id="langDropdown" data-bs-toggle="dropdown">EN</a>
				<ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="langDropdown">
					<li><a class="dropdown-item" href="#" data-lang="EN">English (EN)</a></li>
					<li><a class="dropdown-item" href="#" data-lang="FR">Français (FR)</a></li>
					<li><a class="dropdown-item" href="#" data-lang="ES">Español (ES)</a></li>
				</ul>
			</div>
			<div class="menu__item has-dropdown search">
				<span class="search_icon"></span>
			</div>
		</div>
		<div class="hamburger">
				<div class="hamburger-box">
					<div class="hamburger-inner"></div>
				</div>
		</div>
	</div>
</nav>

<div class="desktop-search-panel">
	<div class="header__container desktop-search-panel__container">
		<form role="search" method="get" action="/">
			<input type="text" name="s" placeholder="Search our website…" />
			<button>Search</button>
		</form>
		<span class="desktop-search-panel__close"></span>
	</div>
</div>

<div class="desktop-dealer-panel">
	<div class="header__container desktop-dealer-panel__container">
		<form role="search" method="get" action="/">
			<input type="text" name="s" placeholder="Zip Code" />
			<button>Find Your Dealer</button>
		</form>
		<span class="desktop-dealer-panel__close"></span>
	</div>
</div>

<div class="mobile-menu">
	<div class="dark-blur"></div>
	<div class="mobile-panel">
		<div class="mobile-panel__wrapper">
			<div class="mobile-panel__menu"></div>
			<div class="mobile-search">
				<form role="search" method="get" action="/">
					<input type="text" name="s" placeholder="Search our website…" />
					<button></button>
				</form>
			</div>
			<div class="mobile-plugins">
				<div class="dropdown lang">
					<a href="#" class="link-dark dropdown-toggle" id="langDropdown" data-bs-toggle="dropdown">EN</a>
					<ul class="dropdown-menu dropdown-menu-end text-small shadow" aria-labelledby="langDropdown">
						<li><a class="dropdown-item" href="#" data-lang="EN">English (EN)</a></li>
						<li><a class="dropdown-item" href="#" data-lang="FR">Français (FR)</a></li>
						<li><a class="dropdown-item" href="#" data-lang="ES">Español (ES)</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>