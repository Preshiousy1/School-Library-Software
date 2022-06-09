# School-Library-Software

School Library Software for 23IT

Installation Steps

1. Run git clone https://github.com/Preshiousy1/School-Library-Software.git

# cd into the project

2. Run cp .env.example .env
3. Run docker-compose up -d --build

4. Run docker-compose exec app php artisan migrate

# set up your own superadmin username and password at .env file

ADMIN_USERNAME= admin(default)
ADMIN_PASSWORD= password(default)

7. Run docker-compose exec app php artisan db:seed
8. Run php artisan serve
9. Go to link localhost:8000
