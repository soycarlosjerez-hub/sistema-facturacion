<nav>
    <div class="sidebar-nav">
        <a href="<?php echo e(route('telescope.dashboard')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.dashboard') ? 'active' : ''); ?>">
            <i class="fas fa-chart-line"></i>
            Dashboard
        </a>
        <a href="<?php echo e(route('telescope.requests')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.requests') ? 'active' : ''); ?>">
            <i class="fas fa-globe"></i>
            Requests
        </a>
        <a href="<?php echo e(route('telescope.exceptions')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.exceptions') ? 'active' : ''); ?>">
            <i class="fas fa-bug"></i>
            Exceptions
        </a>
        <a href="<?php echo e(route('telescope.jobs')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.jobs') ? 'active' : ''); ?>">
            <i class="fas fa-tasks"></i>
            Jobs
        </a>
        <a href="<?php echo e(route('telescope.queue')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.queue') ? 'active' : ''); ?>">
            <i class="fas fa-list"></i>
            Queue
        </a>
        <a href="<?php echo e(route('telescope.logs')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.logs') ? 'active' : ''); ?>">
            <i class="fas fa-file-alt"></i>
            Logs
        </a>
        <a href="<?php echo e(route('telescope.models')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.models') ? 'active' : ''); ?>">
            <i class="fas fa-database"></i>
            Models
        </a>
        <a href="<?php echo e(route('telescope.cache')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.cache') ? 'active' : ''); ?>">
            <i class="fas fa-caches"></i>
            Cache
        </a>
        <a href="<?php echo e(route('telescope.queries')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.queries') ? 'active' : ''); ?>">
            <i class="fas fa-server"></i>
            Queries
        </a>
        <a href="<?php echo e(route('telescope.mail')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.mail') ? 'active' : ''); ?>">
            <i class="fas fa-envelope"></i>
            Mail
        </a>
        <a href="<?php echo e(route('telescope.commands')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.commands') ? 'active' : ''); ?>">
            <i class="fas fa-terminal"></i>
            Commands
        </a>
        <a href="<?php echo e(route('telescope.screens')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.screens') ? 'active' : ''); ?>">
            <i class="fas fa-desktop"></i>
            Screens
        </a>
        <a href="<?php echo e(route('telescope.gate')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.gate') ? 'active' : ''); ?>">
            <i class="fas fa-lock"></i>
            Gates
        </a>
        <a href="<?php echo e(route('telescope.dumps')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.dumps') ? 'active' : ''); ?>">
            <i class="fas fa-eye"></i>
            Dumps
        </a>
        <a href="<?php echo e(route('telescope.events')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.events') ? 'active' : ''); ?>">
            <i class="fas fa-bolt"></i>
            Events
        </a>
        <a href="<?php echo e(route('telescope.schedulers')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.schedulers') ? 'active' : ''); ?>">
            <i class="fas fa-calendar-alt"></i>
            Schedulers
        </a>
        <a href="<?php echo e(route('telescope.batches')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.batches') ? 'active' : ''); ?>">
            <i class="fas fa-layer-group"></i>
            Batches
        </a>
        <a href="<?php echo e(route('telescope.migrations')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.migrations') ? 'active' : ''); ?>">
            <i class="fas fa-arrow-right"></i>
            Migrations
        </a>
        <a href="<?php echo e(route('telescope.pending')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.pending') ? 'active' : ''); ?>">
            <i class="fas fa-clock"></i>
            Pending
        </a>
        <a href="<?php echo e(route('telescope.tag')); ?>" class="sidebar-link <?php echo e(request()->routeIs('telescope.tag') ? 'active' : ''); ?>">
            <i class="fas fa-tags"></i>
            Tags
        </a>
    </div>
</nav>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/telescope/navigation.blade.php ENDPATH**/ ?>