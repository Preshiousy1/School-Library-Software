# School-Library-Software

School Library Software for 23IT

Installation Steps

1. Run git clone https://github.com/Preshiousy1/School-Library-Software.git

# cd into the project

2. Run composer install
3. Run cp .env.example .env
4. Run php artisan key:generate
5. Run php artisan jwt:secret

# confirm its in .env file at JWT_SECRET=secret_jwt_string_key

# set up database with

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library
DB_USERNAME=root
DB_PASSWORD=

6. Run php artisan migrate

# set up your own superadmin username and password at .env file

ADMIN_USERNAME= admin(default)
ADMIN_PASSWORD= password(default)

7. Run php artisan db:seed
8. Run php artisan serve
9. Go to link localhost:8000
