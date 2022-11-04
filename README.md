# Palmleaf Store

This is the code for the Palmleaf Creates store.

## Get started

### Clone the repo

```bash
git clone git@github.com:ChaDonSom/palmleaf-store.git
```

### Mysql setup
```bash
sudo mysql -u root
> create database palmleaf;
> create user 'palmleaf'@'localhost' identified by 'secret';
> grant all privileges on palmleaf.* to 'palmleaf'@'localhost';
```

(These details should match your `.env` file.)

### Install
```bash
composer install
npm install

php artisan storage:link
php artisan migrate
```

### Start dev server
```bash
php artisan serve
npm run hot
```