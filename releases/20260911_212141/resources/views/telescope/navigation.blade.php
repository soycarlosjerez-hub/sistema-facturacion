<nav>
    <div class="sidebar-nav">
        <a href="{{ route('telescope.dashboard') }}" class="sidebar-link {{ request()->routeIs('telescope.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            Dashboard
        </a>
        <a href="{{ route('telescope.requests') }}" class="sidebar-link {{ request()->routeIs('telescope.requests') ? 'active' : '' }}">
            <i class="fas fa-globe"></i>
            Requests
        </a>
        <a href="{{ route('telescope.exceptions') }}" class="sidebar-link {{ request()->routeIs('telescope.exceptions') ? 'active' : '' }}">
            <i class="fas fa-bug"></i>
            Exceptions
        </a>
        <a href="{{ route('telescope.jobs') }}" class="sidebar-link {{ request()->routeIs('telescope.jobs') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i>
            Jobs
        </a>
        <a href="{{ route('telescope.queue') }}" class="sidebar-link {{ request()->routeIs('telescope.queue') ? 'active' : '' }}">
            <i class="fas fa-list"></i>
            Queue
        </a>
        <a href="{{ route('telescope.logs') }}" class="sidebar-link {{ request()->routeIs('telescope.logs') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i>
            Logs
        </a>
        <a href="{{ route('telescope.models') }}" class="sidebar-link {{ request()->routeIs('telescope.models') ? 'active' : '' }}">
            <i class="fas fa-database"></i>
            Models
        </a>
        <a href="{{ route('telescope.cache') }}" class="sidebar-link {{ request()->routeIs('telescope.cache') ? 'active' : '' }}">
            <i class="fas fa-caches"></i>
            Cache
        </a>
        <a href="{{ route('telescope.queries') }}" class="sidebar-link {{ request()->routeIs('telescope.queries') ? 'active' : '' }}">
            <i class="fas fa-server"></i>
            Queries
        </a>
        <a href="{{ route('telescope.mail') }}" class="sidebar-link {{ request()->routeIs('telescope.mail') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i>
            Mail
        </a>
        <a href="{{ route('telescope.commands') }}" class="sidebar-link {{ request()->routeIs('telescope.commands') ? 'active' : '' }}">
            <i class="fas fa-terminal"></i>
            Commands
        </a>
        <a href="{{ route('telescope.screens') }}" class="sidebar-link {{ request()->routeIs('telescope.screens') ? 'active' : '' }}">
            <i class="fas fa-desktop"></i>
            Screens
        </a>
        <a href="{{ route('telescope.gate') }}" class="sidebar-link {{ request()->routeIs('telescope.gate') ? 'active' : '' }}">
            <i class="fas fa-lock"></i>
            Gates
        </a>
        <a href="{{ route('telescope.dumps') }}" class="sidebar-link {{ request()->routeIs('telescope.dumps') ? 'active' : '' }}">
            <i class="fas fa-eye"></i>
            Dumps
        </a>
        <a href="{{ route('telescope.events') }}" class="sidebar-link {{ request()->routeIs('telescope.events') ? 'active' : '' }}">
            <i class="fas fa-bolt"></i>
            Events
        </a>
        <a href="{{ route('telescope.schedulers') }}" class="sidebar-link {{ request()->routeIs('telescope.schedulers') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            Schedulers
        </a>
        <a href="{{ route('telescope.batches') }}" class="sidebar-link {{ request()->routeIs('telescope.batches') ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i>
            Batches
        </a>
        <a href="{{ route('telescope.migrations') }}" class="sidebar-link {{ request()->routeIs('telescope.migrations') ? 'active' : '' }}">
            <i class="fas fa-arrow-right"></i>
            Migrations
        </a>
        <a href="{{ route('telescope.pending') }}" class="sidebar-link {{ request()->routeIs('telescope.pending') ? 'active' : '' }}">
            <i class="fas fa-clock"></i>
            Pending
        </a>
        <a href="{{ route('telescope.tag') }}" class="sidebar-link {{ request()->routeIs('telescope.tag') ? 'active' : '' }}">
            <i class="fas fa-tags"></i>
            Tags
        </a>
    </div>
</nav>
