<template>
	<div>
		<header class="app-header">
			<a class="app-header__logo" href="#">Admin Dashboard</a>
			<a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
			<ul class="app-nav">
				<li class="app-search">
					<input class="app-search__input" type="search" placeholder="Search" />
					<button class="app-search__button">
						<i class="fa fa-search"></i>
					</button>
				</li>
				<li class="dropdown">
					<a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Show notifications"><i class="fa fa-bell-o fa-lg"></i></a>
					<ul class="app-notification dropdown-menu dropdown-menu-right">
						<li class="app-notification__title">
							You have 4 new notifications.
						</li>
						<div class="app-notification__content">
							<li>
								<a class="app-notification__item" href="javascript:;">
									<span class="app-notification__icon">
										<span class="fa-stack fa-lg">
											<i class="fa fa-circle fa-stack-2x text-danger"></i>
											<i class="fa fa-hdd-o fa-stack-1x fa-inverse"></i>
										</span>
									</span>
									<div>
										<p class="app-notification__message">
											Mail server not working
										</p>
										<p class="app-notification__meta">5 min ago</p>
									</div>
								</a>
							</li>
							<li>
								<a class="app-notification__item" href="javascript:;">
									<span class="app-notification__icon">
										<span class="fa-stack fa-lg">
											<i class="fa fa-circle fa-stack-2x text-success"></i>
											<i class="fa fa-money fa-stack-1x fa-inverse"></i>
										</span>
									</span>
									<div>
										<p class="app-notification__message">
											Transaction complete
										</p>
										<p class="app-notification__meta">2 days ago</p>
									</div>
								</a>
							</li>
						</div>
						<li class="app-notification__footer">
							<a href="#">See all notifications.</a>
						</li>
					</ul>
				</li>
				<!-- User Menu-->
				<li class="dropdown">
					<a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu"><i class="fa fa-user fa-lg"></i></a>
					<ul class="dropdown-menu settings-menu dropdown-menu-right">
						<li>
							<a class="dropdown-item" href="#"><i class="fa fa-cog fa-lg"></i> Settings</a>
						</li>
						<li>
							<a class="dropdown-item" href="page-user.html"><i class="fa fa-user fa-lg"></i> Profile</a>
						</li>
						<li>
							<a class="dropdown-item" href="#"><i class="fa fa-sign-out fa-lg"></i> Logout</a>
						</li>
					</ul>
				</li>
			</ul>
		</header>

		<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
		<aside class="app-sidebar">
			<ul class="app-menu">
				<li>
					<!-- <a class="app-menu__item {{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
						<i class="app-menu__icon fa fa-dashboard"></i>
						<span class="app-menu__label">Dashboard</span>
					</a> -->
					<router-link class="app-menu__item active" :to="{ name: 'dashboard' }">
						<i class="app-menu__icon fa fa-dashboard"></i>
						<span class="app-menu__label">Dashboard</span>
					</router-link>
				</li>
			</ul>
		</aside>

		<main class="app-content" id="app">
			<!-- <transition name="fade" mode="out-in">
				<router-view></router-view>
			</transition> -->
			<router-view></router-view>
		</main>
	</div>
</template>

<script>
	export default {
		
	}
</script>