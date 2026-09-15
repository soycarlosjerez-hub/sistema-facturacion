sudo git pull
sudo php artisan migrate
sudo php artisan optimize
sudo php artisan optimize:clear
php artisan db:seed --class=BusinessTypeSeeder --force
php artisan db:seed --class=PlanSeeder --force
php artisan db:seed --class=PermissionSeeder --force
php artisan db:seed --class=ModuloSeeder --force
